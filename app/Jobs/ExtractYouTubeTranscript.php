<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Source;
use App\Services\YoutubeScraper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExtractYouTubeTranscript implements ShouldQueue
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
    public function handle(YoutubeScraper $scraper): void
    {
        try {
            \Log::info('Starting YouTube transcript extraction', [
                'source_id' => $this->source->id,
                'url' => $this->source->data
            ]);

            // Extract video ID from URL
            $videoId = $scraper->extractVideoId($this->source->data);
            
            if (!$videoId) {
                throw new \Exception('Invalid YouTube URL or could not extract video ID');
            }

            // Get available languages
            $availableLanguages = $scraper->getAvailableLanguages($videoId);
            
            // Get transcript (prefer English, fallback to first available)
            $languageCodes = ['en'];
            if (!in_array('en', $availableLanguages) && !empty($availableLanguages)) {
                $languageCodes = [$availableLanguages[0]];
            }
            
            $transcript = $scraper->getTranscript($videoId, $languageCodes);
            
            // Get video metadata
            $metadata = $scraper->getVideoMetadata($videoId);
            
            // Format transcript for storage
            $formattedTranscript = '';
            foreach ($transcript as $segment) {
                $startTime = gmdate('H:i:s', (int)$segment['start']);
                $formattedTranscript .= "[{$startTime}] {$segment['text']}\n";
            }
            
            // Get plain text version (without timestamps)
            $plainText = $scraper->getTranscriptAsText($videoId, $languageCodes);

            \Log::info('YouTube transcript extracted successfully', [
                'source_id' => $this->source->id,
                'video_id' => $videoId,
                'title' => $metadata['title'] ?? 'Unknown',
                'transcript_length' => strlen($formattedTranscript),
                'languages' => $availableLanguages
            ]);

            // Update the source with the extracted data
            $this->source->update([
                'data' => json_encode([
                    'url' => $this->source->data,
                    'video_id' => $videoId,
                    'title' => $metadata['title'] ?? 'Unknown',
                    'author' => $metadata['author'] ?? 'Unknown',
                    'thumbnail_url' => $metadata['thumbnail_url'] ?? '',
                    'transcript' => $formattedTranscript,
                    'plain_text' => $plainText,
                    'available_languages' => $availableLanguages,
                    'language_used' => $languageCodes[0],
                    'extracted_at' => now()->toIso8601String()
                ])
            ]);

            \Log::info('Source updated with YouTube transcript', [
                'source_id' => $this->source->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to extract YouTube transcript', [
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
        \Log::error('ExtractYouTubeTranscript job failed', [
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