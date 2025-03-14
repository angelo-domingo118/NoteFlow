<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Notebook;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends ApiController
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected GeminiService $gemini)
    {
        $this->middleware('auth');
    }

    /**
     * Generate a chat response.
     */
    public function generate(Request $request, Notebook $notebook): JsonResponse
    {
        $this->authorize('view', $notebook);

        $validated = $request->validate([
            'question' => ['required', 'string'],
        ]);

        try {
            $response = $this->gemini->generateResponse(
                $validated['question'],
                $notebook
            );

            return response()->json([
                'response' => $response,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'error' => 'Failed to generate response. Please try again.',
            ], 500);
        }
    }

    /**
     * Get suggested questions based on the notebook's sources.
     */
    public function suggestions(Notebook $notebook): JsonResponse
    {
        $this->authorize('view', $notebook);

        // Example suggestions based on source content types
        $suggestions = [
            'text' => ['Summarize this text', 'What are the key points?', 'Explain this in simpler terms'],
            'file' => ['What is this document about?', 'Extract the main ideas'],
            'link' => ['What is the main topic of this link?', 'What are the key takeaways?'],
        ];

        $activeSources = $notebook->sources()->where('is_active', true)->get();
        $relevantSuggestions = $activeSources->flatMap(function ($source) use ($suggestions) {
            return $suggestions[$source->type] ?? [];
        })->unique()->values();

        return response()->json([
            'suggestions' => $relevantSuggestions,
        ]);
    }
}
