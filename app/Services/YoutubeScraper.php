<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use MrMySQL\YoutubeTranscript\TranscriptListFetcher;
use MrMySQL\YoutubeTranscript\Exception\NoTranscriptFoundException;
use MrMySQL\YoutubeTranscript\Exception\TranscriptsDisabledException;
use MrMySQL\YoutubeTranscript\Exception\YouTubeRequestFailedException;

class YoutubeScraper
{
    /**
     * @var Client
     */
    protected Client $httpClient;

    /**
     * @var HttpFactory
     */
    protected HttpFactory $requestFactory;

    /**
     * @var TranscriptListFetcher
     */
    protected TranscriptListFetcher $transcriptFetcher;

    /**
     * @var int Cache duration in minutes
     */
    protected int $cacheDuration = 60 * 24; // 1 day

    /**
     * YoutubeScraper constructor.
     */
    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 30,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ],
        ]);
        
        $this->requestFactory = new HttpFactory();
        $this->transcriptFetcher = new TranscriptListFetcher($this->httpClient, $this->requestFactory);
    }

    /**
     * Extract video ID from YouTube URL
     *
     * @param string $url
     * @return string|null
     */
    public function extractVideoId(string $url): ?string
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
        
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Get transcript for a YouTube video
     *
     * @param string $videoIdOrUrl
     * @param array $languageCodes
     * @param bool $useCache
     * @return array
     * @throws \Exception
     */
    public function getTranscript(string $videoIdOrUrl, array $languageCodes = ['en'], bool $useCache = true): array
    {
        $videoId = $this->extractVideoId($videoIdOrUrl) ?? $videoIdOrUrl;
        
        $cacheKey = "youtube_transcript_{$videoId}_" . implode('_', $languageCodes);
        
        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        try {
            $transcriptList = $this->transcriptFetcher->fetch($videoId);
            
            // Check if requested languages are available, otherwise use first available
            $availableLanguages = $transcriptList->getAvailableLanguageCodes();
            
            $selectedLanguages = array_filter($languageCodes, function ($lang) use ($availableLanguages) {
                return in_array($lang, $availableLanguages);
            });
            
            if (empty($selectedLanguages) && !empty($availableLanguages)) {
                $selectedLanguages = [$availableLanguages[0]];
            }
            
            $transcript = $transcriptList->findTranscript($selectedLanguages);
            $transcriptData = $transcript->fetch();
            
            if ($useCache) {
                Cache::put($cacheKey, $transcriptData, $this->cacheDuration * 60);
            }
            
            return $transcriptData;
            
        } catch (NoTranscriptFoundException $e) {
            Log::warning("No transcript found for video ID: {$videoId}", [
                'error' => $e->getMessage(),
                'languages' => $languageCodes
            ]);
            return [];
        } catch (TranscriptsDisabledException $e) {
            Log::warning("Transcripts are disabled for video ID: {$videoId}", [
                'error' => $e->getMessage()
            ]);
            return [];
        } catch (YouTubeRequestFailedException $e) {
            Log::error("YouTube request failed for video ID: {$videoId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error("Error fetching transcript for video ID: {$videoId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get available language codes for a YouTube video
     *
     * @param string $videoIdOrUrl
     * @param bool $useCache
     * @return array
     * @throws \Exception
     */
    public function getAvailableLanguages(string $videoIdOrUrl, bool $useCache = true): array
    {
        $videoId = $this->extractVideoId($videoIdOrUrl) ?? $videoIdOrUrl;
        
        $cacheKey = "youtube_languages_{$videoId}";
        
        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        try {
            $transcriptList = $this->transcriptFetcher->fetch($videoId);
            $languages = $transcriptList->getAvailableLanguageCodes();
            
            if ($useCache) {
                Cache::put($cacheKey, $languages, $this->cacheDuration * 60);
            }
            
            return $languages;
        } catch (\Exception $e) {
            Log::error("Error fetching available languages for video ID: {$videoId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get transcript in plain text format (without timestamps)
     *
     * @param string $videoIdOrUrl
     * @param array $languageCodes
     * @param bool $useCache
     * @return string
     * @throws \Exception
     */
    public function getTranscriptAsText(string $videoIdOrUrl, array $languageCodes = ['en'], bool $useCache = true): string
    {
        $transcript = $this->getTranscript($videoIdOrUrl, $languageCodes, $useCache);
        
        if (empty($transcript)) {
            return '';
        }
        
        return implode(' ', array_map(function ($segment) {
            return $segment['text'];
        }, $transcript));
    }

    /**
     * Translate transcript to another language
     *
     * @param string $videoIdOrUrl
     * @param string $targetLanguage
     * @param array $sourceLanguageCodes
     * @param bool $useCache
     * @return array
     * @throws \Exception
     */
    public function translateTranscript(
        string $videoIdOrUrl, 
        string $targetLanguage, 
        array $sourceLanguageCodes = ['en'], 
        bool $useCache = true
    ): array {
        $videoId = $this->extractVideoId($videoIdOrUrl) ?? $videoIdOrUrl;
        
        $cacheKey = "youtube_transcript_translated_{$videoId}_{$targetLanguage}_" . implode('_', $sourceLanguageCodes);
        
        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        try {
            $transcriptList = $this->transcriptFetcher->fetch($videoId);
            
            // Check if requested languages are available, otherwise use first available
            $availableLanguages = $transcriptList->getAvailableLanguageCodes();
            
            $selectedLanguages = array_filter($sourceLanguageCodes, function ($lang) use ($availableLanguages) {
                return in_array($lang, $availableLanguages);
            });
            
            if (empty($selectedLanguages) && !empty($availableLanguages)) {
                $selectedLanguages = [$availableLanguages[0]];
            }
            
            $transcript = $transcriptList->findTranscript($selectedLanguages);
            
            if (!$transcript->isTranslatable()) {
                Log::warning("Transcript is not translatable for video ID: {$videoId}");
                return $this->getTranscript($videoIdOrUrl, $sourceLanguageCodes, $useCache);
            }
            
            $translatedTranscript = $transcript->translate($targetLanguage);
            $translatedData = $translatedTranscript->fetch();
            
            if ($useCache) {
                Cache::put($cacheKey, $translatedData, $this->cacheDuration * 60);
            }
            
            return $translatedData;
            
        } catch (\Exception $e) {
            Log::error("Error translating transcript for video ID: {$videoId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get basic metadata for a YouTube video
     *
     * @param string $videoIdOrUrl
     * @param bool $useCache
     * @return array
     * @throws \Exception
     */
    public function getVideoMetadata(string $videoIdOrUrl, bool $useCache = true): array
    {
        $videoId = $this->extractVideoId($videoIdOrUrl) ?? $videoIdOrUrl;
        
        $cacheKey = "youtube_metadata_{$videoId}";
        
        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        try {
            $response = $this->httpClient->request('GET', "https://www.youtube.com/watch?v={$videoId}");
            $html = $response->getBody()->getContents();
            
            // Extract title
            preg_match('/<title>(.*?)<\/title>/', $html, $titleMatches);
            $title = $titleMatches[1] ?? '';
            $title = str_replace(' - YouTube', '', $title);
            
            // Extract description
            preg_match('/"description":"(.*?)(?<!\\\\)"/', $html, $descMatches);
            $description = $descMatches[1] ?? '';
            $description = str_replace('\\n', "\n", $description);
            $description = str_replace('\\"', '"', $description);
            
            // Extract channel name
            preg_match('/"ownerChannelName":"(.*?)(?<!\\\\)"/', $html, $channelMatches);
            $channelName = $channelMatches[1] ?? '';
            
            // Extract view count
            preg_match('/"viewCount":"(\d+)"/', $html, $viewMatches);
            $viewCount = $viewMatches[1] ?? 0;
            
            // Extract upload date
            preg_match('/"uploadDate":"(\d{4}-\d{2}-\d{2})"/', $html, $dateMatches);
            $uploadDate = $dateMatches[1] ?? '';
            
            $metadata = [
                'video_id' => $videoId,
                'title' => $title,
                'description' => $description,
                'channel_name' => $channelName,
                'view_count' => (int) $viewCount,
                'upload_date' => $uploadDate,
                'url' => "https://www.youtube.com/watch?v={$videoId}",
                'embed_url' => "https://www.youtube.com/embed/{$videoId}",
                'thumbnail_url' => "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg",
            ];
            
            if ($useCache) {
                Cache::put($cacheKey, $metadata, $this->cacheDuration * 60);
            }
            
            return $metadata;
            
        } catch (\Exception $e) {
            Log::error("Error fetching metadata for video ID: {$videoId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Set cache duration in minutes
     *
     * @param int $minutes
     * @return $this
     */
    public function setCacheDuration(int $minutes): self
    {
        $this->cacheDuration = $minutes;
        return $this;
    }
} 