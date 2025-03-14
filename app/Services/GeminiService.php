<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notebook;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GeminiService
{
    /**
     * The Gemini API endpoint for text generation.
     */
    protected string $endpoint = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent';

    /**
     * The Gemini API endpoint for vision capabilities.
     */
    protected string $visionEndpoint = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro-vision:generateContent';

    /**
     * Generate a response from Gemini based on the user's question and context.
     */
    public function generateResponse(string $question, Notebook $notebook): string
    {
        $activeSourcesData = $this->getActiveSourcesContent($notebook);
        
        // Check if we have YouTube sources that need vision capabilities
        $hasYoutubeSource = $activeSourcesData->contains(function($source) {
            return $source['type'] === 'link' && $this->isYoutubeUrl($source['content']);
        });
        
        if ($hasYoutubeSource) {
            return $this->generateMultimodalResponse($question, $activeSourcesData);
        }
        
        return $this->generateTextResponse($question, $activeSourcesData);
    }
    
    /**
     * Generate a text-only response using the Gemini text model.
     */
    protected function generateTextResponse(string $question, Collection $sources): string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->endpoint . '?key=' . config('services.gemini.api_key'), [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $this->buildPrompt($question, $sources)
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ],
            'safetySettings' => $this->getSafetySettings(),
        ]);

        return $this->processApiResponse($response);
    }
    
    /**
     * Generate a multimodal response for sources with YouTube videos.
     */
    protected function generateMultimodalResponse(string $question, Collection $sources): string
    {
        // Prepare the parts array for the multimodal request
        $parts = [];
        
        // Add the text prompt first
        $parts[] = [
            'text' => $this->buildMultimodalPrompt($question, $sources)
        ];
        
        // Add image parts for YouTube thumbnails
        foreach ($sources as $source) {
            if ($source['type'] === 'link' && $this->isYoutubeUrl($source['content'])) {
                $thumbnailUrl = $this->getYoutubeThumbnailUrl($source['content']);
                
                if ($thumbnailUrl) {
                    $imageData = $this->getImageData($thumbnailUrl);
                    if ($imageData) {
                        $parts[] = [
                            'inline_data' => [
                                'mime_type' => 'image/jpeg',
                                'data' => $imageData
                            ]
                        ];
                    }
                }
            }
        }
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->visionEndpoint . '?key=' . config('services.gemini.api_key'), [
            'contents' => [
                [
                    'parts' => $parts
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ],
            'safetySettings' => $this->getSafetySettings(),
        ]);
        
        return $this->processApiResponse($response);
    }
    
    /**
     * Process the API response and handle errors.
     */
    protected function processApiResponse($response): string
    {
        if (!$response->successful()) {
            $errorBody = $response->json();
            Log::error('Gemini API error:', [
                'status' => $response->status(),
                'body' => $errorBody,
            ]);
            
            $errorMessage = $errorBody['error']['message'] ?? 'Unknown error occurred';
            return "Error: {$errorMessage}. Please ensure your API key is valid and has proper permissions.";
        }

        $data = $response->json();
        
        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            Log::error('Unexpected Gemini API response structure:', ['response' => $data]);
            return 'Error: Received unexpected response format from API';
        }

        return $data['candidates'][0]['content']['parts'][0]['text'];
    }
    
    /**
     * Get common safety settings for Gemini API.
     */
    protected function getSafetySettings(): array
    {
        return [
            [
                'category' => 'HARM_CATEGORY_HARASSMENT',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE',
            ],
            [
                'category' => 'HARM_CATEGORY_HATE_SPEECH',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE',
            ],
            [
                'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE',
            ],
            [
                'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE',
            ],
        ];
    }

    /**
     * Check if a URL is a valid YouTube URL.
     */
    protected function isYoutubeUrl(string $url): bool
    {
        return preg_match('/^https?:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[a-zA-Z0-9_-]{11}$/', $url) === 1;
    }
    
    /**
     * Extract video ID from YouTube URL.
     */
    protected function extractYoutubeVideoId(string $url): ?string
    {
        $pattern = '/^https?:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})$/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[3];
        }
        return null;
    }
    
    /**
     * Get the thumbnail URL for a YouTube video.
     */
    protected function getYoutubeThumbnailUrl(string $youtubeUrl): ?string
    {
        $videoId = $this->extractYoutubeVideoId($youtubeUrl);
        if (!$videoId) {
            return null;
        }
        
        // Use the high-quality thumbnail
        return "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
    }
    
    /**
     * Get image data as base64 encoded string.
     */
    protected function getImageData(string $imageUrl): ?string
    {
        try {
            $response = Http::get($imageUrl);
            if ($response->successful()) {
                return base64_encode($response->body());
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch image data:', [
                'url' => $imageUrl,
                'error' => $e->getMessage(),
            ]);
        }
        
        return null;
    }

    /**
     * Get content from active sources.
     */
    protected function getActiveSourcesContent(Notebook $notebook): Collection
    {
        return $notebook->sources()
            ->where('is_active', true)
            ->get()
            ->map(function ($source) {
                $content = '';
                
                if ($source->isText()) {
                    $content = $source->data;
                } elseif ($source->isWebsite()) {
                    $websiteContent = $source->getWebsiteContent();
                    if (!empty($websiteContent['content'])) {
                        $content = $websiteContent['content'];
                    } else {
                        $content = $source->data; // Fallback to URL if content not extracted
                    }
                } elseif ($source->isYouTube()) {
                    $content = $source->data; // YouTube URL
                } elseif ($source->isFile() && $source->file_path) {
                    // For text files, try to get content
                    if (in_array($source->file_type, ['text/plain', 'text/markdown'])) {
                        try {
                            $content = Storage::disk('public')->get($source->file_path);
                        } catch (\Exception $e) {
                            $content = "File: " . basename($source->file_path);
                        }
                    } else {
                        $content = "File: " . basename($source->file_path);
                    }
                }
                
                return [
                    'type' => $source->type,
                    'content' => $content,
                    'name' => $source->name,
                ];
            });
    }

    /**
     * Build the prompt with context from active sources.
     */
    protected function buildPrompt(string $question, Collection $sources): string
    {
        $contextStr = $sources->map(function ($source) {
            $sourceName = $source['name'];
            
            // For links that are YouTube videos
            if ($source['type'] === 'link' && isset($source['video_id']) && !empty($source['extracted_content'])) {
                $content = $source['extracted_content'];
                
                // Truncate long content for better prompt management
                $maxLength = 8000; // Adjust based on model context window
                if (strlen($content) > $maxLength) {
                    $content = substr($content, 0, $maxLength) . "... [content truncated due to length]";
                }
                
                return "Source '{$sourceName}' (YouTube Video Transcript):\n{$content}";
            }
            
            // For regular links (websites)
            if ($source['type'] === 'link' && isset($source['extracted_content'])) {
                $content = $source['extracted_content'];
                
                // Truncate long content for better prompt management
                $maxLength = 8000; // Adjust based on model context window
                if (strlen($content) > $maxLength) {
                    $content = substr($content, 0, $maxLength) . "... [content truncated due to length]";
                }
                
                return "Source '{$sourceName}' (Website Content):\n{$content}";
            }
            
            // For text sources
            if ($source['type'] === 'text') {
                $content = $source['content'];
                return "Source '{$sourceName}' (Text):\n{$content}";
            }
            
            // For file sources
            if ($source['type'] === 'file') {
                $content = $source['content'];
                return "Source '{$sourceName}' (File):\n{$content}";
            }
            
            // Default format for any other type
            $content = $source['content'] ?? '';
            return "Source '{$sourceName}' ({$source['type']}): {$content}";
        })->join("\n\n");

        return <<<EOT
You are a helpful AI assistant. Use the following sources to inform your response:

{$contextStr}

Based on these sources, please answer the following question:
{$question}

When referencing information from the sources, make it clear which source you're drawing from by name.
If the question cannot be answered using only the provided sources, say so and provide a general response.
EOT;
    }
    
    /**
     * Build a multimodal prompt specifically for YouTube videos.
     */
    protected function buildMultimodalPrompt(string $question, Collection $sources): string
    {
        $textSources = $sources->filter(function ($source) {
            return $source['type'] !== 'youtube';
        });
        
        $youtubeSources = $sources->filter(function ($source) {
            return $source['type'] === 'youtube';
        });
        
        $textContextStr = $textSources->map(function ($source) {
            $content = $source['content'];
            $sourceName = $source['name'];
            
            // For website sources, format the content properly
            if ($source['type'] === 'website') {
                // If it's just a URL (not extracted yet), indicate that
                if (filter_var($content, FILTER_VALIDATE_URL)) {
                    return "Source '{$sourceName}' (Website URL): {$content}";
                }
                
                // Truncate long content for better prompt management
                $maxLength = 6000; // Shorter for multimodal prompts
                if (strlen($content) > $maxLength) {
                    $content = substr($content, 0, $maxLength) . "... [content truncated due to length]";
                }
                
                return "Source '{$sourceName}' (Website Content):\n{$content}";
            }
            
            // For text sources
            if ($source['type'] === 'text') {
                return "Source '{$sourceName}' (Text):\n{$content}";
            }
            
            // For file sources
            if ($source['type'] === 'file') {
                return "Source '{$sourceName}' (File):\n{$content}";
            }
            
            // Default format
            return "Source '{$sourceName}' ({$source['type']}): {$content}";
        })->join("\n\n");
        
        $youtubeContextStr = $youtubeSources->map(function ($source, $index) {
            $videoId = $this->extractYoutubeVideoId($source['content']);
            return "YouTube Source '{$source['name']}': https://youtube.com/watch?v={$videoId}";
        })->join("\n");
        
        return <<<EOT
You are a helpful AI assistant with vision capabilities. You'll be provided with:

1. Text sources for context
2. YouTube video thumbnails

Text Sources:
{$textContextStr}

YouTube Videos:
{$youtubeContextStr}

The YouTube thumbnails will be provided as images in this request. Analyze these thumbnails to get context about the videos.

Based on all these sources, please answer the following question:
{$question}

When referencing information, specify which source you're drawing from by name. For YouTube sources, describe what you can see in the thumbnail and how it relates to the question.
If the question cannot be answered using the provided sources, say so and provide a general response.
EOT;
    }

    /**
     * Prepare sources data for the AI.
     *
     * @param \Illuminate\Database\Eloquent\Collection $sources
     * @return \Illuminate\Support\Collection
     */
    protected function prepareSourcesData($sources)
    {
        return $sources->filter(function ($source) {
            return $source->is_active;
        })->map(function ($source) {
            if ($source->isText()) {
                return [
                    'type' => 'text',
                    'name' => $source->name,
                    'content' => $source->data,
                ];
            } elseif ($source->isWebsite()) {
                $websiteContent = $source->getWebsiteContent();
                return [
                    'type' => 'link',
                    'name' => $source->name,
                    'content' => $websiteContent['url'] ?? '',
                    'extracted_content' => $websiteContent['content'] ?? '',
                ];
            } elseif ($source->isYouTube()) {
                $youtubeContent = $source->getYouTubeContent();
                
                // If there's an error or no transcript, return basic info
                if (isset($youtubeContent['error']) || empty($youtubeContent['plain_text'])) {
                    return [
                        'type' => 'link',
                        'name' => $source->name,
                        'content' => $youtubeContent['url'] ?? '',
                        'extracted_content' => 'No transcript available for this YouTube video.',
                        'video_id' => $youtubeContent['video_id'] ?? null,
                        'title' => $youtubeContent['title'] ?? null,
                    ];
                }
                
                // Format the transcript with video metadata for better context
                $formattedContent = '';
                
                if (!empty($youtubeContent['title'])) {
                    $formattedContent .= "Title: " . $youtubeContent['title'] . "\n\n";
                }
                
                if (!empty($youtubeContent['author'])) {
                    $formattedContent .= "Author: " . $youtubeContent['author'] . "\n\n";
                }
                
                $formattedContent .= "Transcript:\n\n" . ($youtubeContent['plain_text'] ?? '');
                
                return [
                    'type' => 'link',
                    'name' => $source->name,
                    'content' => $youtubeContent['url'] ?? '',
                    'extracted_content' => $formattedContent,
                    'video_id' => $youtubeContent['video_id'] ?? null,
                    'title' => $youtubeContent['title'] ?? null,
                ];
            } elseif ($source->isFile()) {
                // For now, we don't handle file content
                return [
                    'type' => 'file',
                    'name' => $source->name,
                    'content' => 'File content not available',
                ];
            }
            
            return null;
        })->filter();
    }
}
