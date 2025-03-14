<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notebook;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    /**
     * The Gemini API endpoint.
     */
    protected string $endpoint = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent';

    /**
     * Generate a response from Gemini based on the user's question and context.
     */
    public function generateResponse(string $question, Notebook $notebook): string
    {
        $activeSourcesData = $this->getActiveSourcesContent($notebook);
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->endpoint . '?key=' . config('services.gemini.api_key'), [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $this->buildPrompt($question, $activeSourcesData)
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
            'safetySettings' => [
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
            ],
        ]);

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
     * Get content from active sources.
     */
    protected function getActiveSourcesContent(Notebook $notebook): Collection
    {
        return $notebook->sources()
            ->where('is_active', true)
            ->get()
            ->map(fn ($source) => [
                'type' => $source->type,
                'content' => $source->data,
            ]);
    }

    /**
     * Build the prompt with context from active sources.
     */
    protected function buildPrompt(string $question, Collection $sources): string
    {
        $contextStr = $sources->map(function ($source) {
            return "Content ({$source['type']}): {$source['content']}";
        })->join("\n\n");

        return <<<EOT
You are a helpful AI assistant. Use the following sources to inform your response:

{$contextStr}

Based on these sources, please answer the following question:
{$question}

When referencing information from the sources, make it clear which source you're drawing from.
If the question cannot be answered using only the provided sources, say so and provide a general response.
EOT;
    }
}
