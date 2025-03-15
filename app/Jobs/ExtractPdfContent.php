<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Source;
use App\Services\MistralOcrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExtractPdfContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Source $source
    ) {}

    /**
     * Execute the job.
     */
    public function handle(MistralOcrService $ocrService): void
    {
        try {
            Log::info('Starting PDF content extraction', [
                'source_id' => $this->source->id,
                'file_path' => $this->source->file_path
            ]);

            // Get the full path to the file
            $fullPath = Storage::disk('public')->path($this->source->file_path);
            
            if (!file_exists($fullPath)) {
                throw new \Exception("PDF file not found at path: {$fullPath}");
            }

            // Process the document with Mistral OCR
            $result = $ocrService->processDocument($fullPath);
            
            // Extract text content from all pages
            $content = '';
            foreach ($result['pages'] as $page) {
                $content .= $page['markdown'] . "\n\n";
            }

            Log::info('PDF content extracted successfully', [
                'source_id' => $this->source->id,
                'pages' => count($result['pages']),
                'content_length' => strlen($content)
            ]);

            // Update the source with the extracted data
            $this->source->update([
                'data' => json_encode([
                    'content' => $content,
                    'pages' => count($result['pages']),
                    'ocr_result' => $result,
                    'extracted_at' => now()->toIso8601String()
                ]),
                'has_extracted_text' => true
            ]);

            Log::info('Source updated with PDF content', [
                'source_id' => $this->source->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to extract PDF content', [
                'source_id' => $this->source->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update the source with error information
            $this->source->update([
                'data' => json_encode([
                    'error' => $e->getMessage(),
                    'error_timestamp' => now()->toIso8601String()
                ])
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $e): void
    {
        Log::error('ExtractPdfContent job failed', [
            'source_id' => $this->source->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Update the source with error information
        $this->source->update([
            'data' => json_encode([
                'error' => $e->getMessage(),
                'error_timestamp' => now()->toIso8601String()
            ])
        ]);
    }
} 