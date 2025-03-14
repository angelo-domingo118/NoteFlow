<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Source;
use App\Services\WebPageExtractor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExtractWebPageContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Source $source
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WebPageExtractor $extractor): void
    {
        try {
            \Log::info('Starting webpage content extraction', [
                'source_id' => $this->source->id,
                'url' => $this->source->data
            ]);

            // Extract data from the source
            $data = $extractor->extract($this->source->data);

            \Log::info('Webpage content extracted successfully', [
                'source_id' => $this->source->id,
                'title' => $data['title'] ?? 'No title',
                'content_length' => strlen($data['content'] ?? ''),
                'metadata' => $data['metadata'] ?? []
            ]);

            // Update the source with the extracted data
            $this->source->update([
                'data' => json_encode([
                    'url' => $this->source->data,
                    'content' => $data['content'] ?? '',
                    'metadata' => $data['metadata'] ?? [],
                    'extracted_at' => now()->toIso8601String()
                ])
            ]);

            \Log::info('Source updated with extracted content', [
                'source_id' => $this->source->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to extract webpage content', [
                'source_id' => $this->source->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update the source with error information
            $this->source->update([
                'data' => json_encode([
                    'url' => $this->source->data,
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
        \Log::error('ExtractWebPageContent job failed', [
            'source_id' => $this->source->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Update the source with error information
        $this->source->update([
            'data' => json_encode([
                'url' => $this->source->data,
                'error' => $e->getMessage(),
                'error_timestamp' => now()->toIso8601String()
            ])
        ]);
    }
} 