<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Source;
use App\Models\Notebook;

class SuggestionController extends Controller
{
    /**
     * Generate AI suggestions based on active sources
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notebook  $notebook
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request, Notebook $notebook)
    {
        // Validate request
        $validated = $request->validate([
            'sources' => 'required|array',
            'sources.*' => 'exists:sources,id',
            'model' => 'sometimes|string'
        ]);

        $model = $validated['model'] ?? 'gemini-2.0-flash-lite';
        $sourceIds = $validated['sources'];
        
        try {
            // Get content from active sources
            $sources = Source::whereIn('id', $sourceIds)
                ->where('notebook_id', $notebook->id)
                ->get();
            
            if ($sources->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active sources found',
                    'suggestions' => $this->getDefaultSuggestions()
                ]);
            }
            
            // Compile source content
            $sourceContent = $this->compileSourceContent($sources);
            
            if (empty($sourceContent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No content found in active sources',
                    'suggestions' => $this->getDefaultSuggestions()
                ]);
            }
            
            // Generate suggestions using AI
            $suggestions = $this->generateSuggestionsFromAI($sourceContent, $model);
            
            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to generate suggestions: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate suggestions',
                'error' => $e->getMessage(),
                'suggestions' => $this->getDefaultSuggestions()
            ]);
        }
    }
    
    /**
     * Compile content from sources
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $sources
     * @return string
     */
    private function compileSourceContent($sources)
    {
        $content = '';
        
        foreach ($sources as $source) {
            switch ($source->type) {
                case 'text':
                    $content .= $source->content . "\n\n";
                    break;
                    
                case 'website':
                    $websiteContent = $source->getWebsiteContent();
                    if (!empty($websiteContent['content'])) {
                        $content .= $websiteContent['content'] . "\n\n";
                    }
                    break;
                    
                case 'youtube':
                    $youtubeContent = $source->getYouTubeContent();
                    if (!empty($youtubeContent['transcript']) || !empty($youtubeContent['plain_text'])) {
                        $content .= (!empty($youtubeContent['plain_text']) ? $youtubeContent['plain_text'] : $youtubeContent['transcript']) . "\n\n";
                    }
                    break;
                    
                case 'file':
                    if ($source->file_type === 'application/pdf') {
                        $pdfContent = $source->getPdfContent();
                        if (!empty($pdfContent['content'])) {
                            $content .= $pdfContent['content'] . "\n\n";
                        }
                    }
                    break;
            }
        }
        
        // Limit content length to prevent token limits
        if (strlen($content) > 8000) {
            $content = substr($content, 0, 8000) . '...';
        }
        
        return $content;
    }
    
    /**
     * Generate suggestions using AI
     *
     * @param  string  $content
     * @param  string  $model
     * @return array
     */
    private function generateSuggestionsFromAI($content, $model)
    {
        // Fallback to default suggestions if AI integration is not available
        if (!config('services.ai.enabled', false)) {
            return $this->getDefaultSuggestions();
        }
        
        try {
            // Make API call to AI service
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . config('services.ai.api_key')
            ])->post(config('services.ai.endpoint'), [
                'model' => $model,
                'prompt' => "Based on the following content, generate 3-5 insightful questions that would help to analyze, understand and extract key information from the text. Make questions diverse and specific to the content. Format as a simple array of questions only with no additional text.\n\nContent: $content",
                'max_tokens' => 200,
                'temperature' => 0.7
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                
                // Parse the suggestions from the response
                // This will depend on the exact API response format
                $rawSuggestions = $result['content'] ?? $result['text'] ?? $result['completion'] ?? '';
                
                // Clean and format suggestions
                $suggestions = $this->parseSuggestions($rawSuggestions);
                
                // If we couldn't parse any suggestions, use defaults
                if (empty($suggestions)) {
                    return $this->getDefaultSuggestions();
                }
                
                return $suggestions;
            }
            
            // Log the error if the API call was not successful
            Log::error('AI suggestion generation failed: ' . json_encode($response->json()));
            return $this->getDefaultSuggestions();
            
        } catch (\Exception $e) {
            Log::error('Error generating AI suggestions: ' . $e->getMessage());
            return $this->getDefaultSuggestions();
        }
    }
    
    /**
     * Parse suggestions from raw AI output
     *
     * @param  string  $rawText
     * @return array
     */
    private function parseSuggestions($rawText)
    {
        // Try to parse as JSON array first
        if (preg_match('/\[.*\]/s', $rawText, $matches)) {
            try {
                $jsonArray = json_decode($matches[0], true);
                if (is_array($jsonArray) && !empty($jsonArray)) {
                    return array_slice($jsonArray, 0, 5);
                }
            } catch (\Exception $e) {
                // Continue with other parsing methods
            }
        }
        
        // Try to extract numbered or bullet points
        if (preg_match_all('/(?:\d+[.):]|\*|\-)\s*([^\n]+)/', $rawText, $matches)) {
            $suggestions = $matches[1];
            return array_slice($suggestions, 0, 5);
        }
        
        // If all else fails, split by newlines and clean up
        $lines = explode("\n", $rawText);
        $suggestions = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && strlen($line) > 10 && substr($line, -1) === '?') {
                $suggestions[] = $line;
            }
        }
        
        return array_slice($suggestions, 0, 5);
    }
    
    /**
     * Get default suggestions when AI generation fails
     *
     * @return array
     */
    private function getDefaultSuggestions()
    {
        return [
            'Summarize the key points from all sources',
            'What are the main arguments presented?',
            'Compare and contrast the different perspectives',
            'What evidence supports the main claims?',
            'What are the implications of these findings?'
        ];
    }
}
