<?php

declare(strict_types=1);

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use MrMySQL\YoutubeTranscript\TranscriptListFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;

// Function to display transcript in a readable format
function displayTranscript(array $transcript): void
{
    foreach ($transcript as $segment) {
        $startTime = gmdate('H:i:s', (int)$segment['start']);
        echo "[{$startTime}] {$segment['text']}\n";
    }
}

// Test function to fetch and display transcript
function testYoutubeTranscript(string $videoId): void
{
    try {
        echo "Fetching transcript for video ID: {$videoId}\n\n";
        
        // Initialize HTTP client and request factory
        $httpClient = new Client([
            'timeout' => 30,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ],
        ]);
        $requestFactory = new HttpFactory();
        
        // Initialize the TranscriptListFetcher
        $fetcher = new TranscriptListFetcher($httpClient, $requestFactory);
        
        // Fetch the transcript list
        $transcriptList = $fetcher->fetch($videoId);
        
        // Display available languages
        $availableLanguages = $transcriptList->getAvailableLanguageCodes();
        echo "Available languages: " . implode(', ', $availableLanguages) . "\n\n";
        
        // Try to get English transcript first, fallback to first available language
        $languageCodes = ['en'];
        if (!in_array('en', $availableLanguages) && !empty($availableLanguages)) {
            $languageCodes = [$availableLanguages[0]];
        }
        
        // Fetch the transcript
        $transcript = $transcriptList->findTranscript($languageCodes);
        $transcriptData = $transcript->fetch();
        
        if (empty($transcriptData)) {
            echo "No transcript found for this video.\n";
            return;
        }
        
        echo "Transcript fetched successfully!\n";
        echo "Total segments: " . count($transcriptData) . "\n\n";
        
        // Display the first 10 segments of the transcript
        echo "First 10 segments of the transcript:\n";
        echo "--------------------------------\n";
        $firstTenSegments = array_slice($transcriptData, 0, 10);
        displayTranscript($firstTenSegments);
        
        echo "\n--------------------------------\n";
        echo "Full transcript length: " . count($transcriptData) . " segments\n";
        
        // Try to translate if possible
        if ($transcript->isTranslatable() && !in_array('en', $languageCodes)) {
            echo "\nTranslating transcript to English...\n";
            $translatedTranscript = $transcript->translate('en');
            $translatedData = $translatedTranscript->fetch();
            
            echo "Translation successful!\n";
            echo "First 10 segments of the translated transcript:\n";
            echo "--------------------------------\n";
            $firstTenTranslated = array_slice($translatedData, 0, 10);
            displayTranscript($firstTenTranslated);
        }
        
    } catch (\Exception $e) {
        echo "Error fetching transcript: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
}

// Test with a few different YouTube videos
$videoIds = [
    'jNQXAC9IVRw', // "Me at the zoo" (First YouTube video)
    'dQw4w9WgXcQ', // Rick Astley - Never Gonna Give You Up
    'TcMBFSGVi1c'  // Avengers: Endgame Trailer
];

foreach ($videoIds as $videoId) {
    testYoutubeTranscript($videoId);
    echo "\n" . str_repeat("=", 50) . "\n\n";
}

// Allow user to input a custom video ID
echo "Enter a YouTube video ID to fetch its transcript (or press Enter to exit): ";
$handle = fopen("php://stdin", "r");
$customVideoId = trim(fgets($handle));
fclose($handle);

if (!empty($customVideoId)) {
    testYoutubeTranscript($customVideoId);
}

echo "\nScript execution completed.\n"; 