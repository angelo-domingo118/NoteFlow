<?php

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Initialize GuzzleHttp client directly
$client = new GuzzleHttp\Client([
    'timeout' => 30,
    'verify' => false,
    'headers' => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    ],
]);

// Test URL
$url = 'https://www.fandom.com/';

try {
    echo "Starting extraction from: $url\n";
    
    // Make the request
    $response = $client->request('GET', $url);
    $html = $response->getBody()->getContents();
    
    // Create a new DOMDocument
    $dom = new DOMDocument();
    @$dom->loadHTML($html, LIBXML_NOERROR);
    $xpath = new DOMXPath($dom);
    
    // Extract title
    $title = '';
    $titleNode = $xpath->query('//title')->item(0);
    if ($titleNode) {
        $title = trim($titleNode->textContent);
    }
    
    // Extract main content
    $content = '';
    $selectors = [
        '//article',
        '//main',
        '//div[contains(@class, "content")]',
        '//div[@id="main"]',
        '//div[@class="main"]'
    ];
    
    foreach ($selectors as $selector) {
        $nodes = $xpath->query($selector);
        if ($nodes->length > 0) {
            foreach ($nodes as $node) {
                $content .= trim($node->textContent) . "\n";
            }
            break;
        }
    }
    
    // If no content found with selectors, get body content
    if (empty($content)) {
        $body = $xpath->query('//body')->item(0);
        if ($body) {
            $content = trim($body->textContent);
        }
    }
    
    echo "\nExtraction successful!\n";
    echo "Title: " . $title . "\n";
    echo "Content length: " . strlen($content) . " characters\n";
    echo "\nFirst 200 characters of content:\n";
    echo substr($content, 0, 200) . "...\n";
    
} catch (\Exception $e) {
    echo "Error during extraction: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 