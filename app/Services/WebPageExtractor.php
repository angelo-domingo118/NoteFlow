<?php

declare(strict_types=1);

namespace App\Services;

use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class WebPageExtractor
{
    private Client $client;
    private array $allowedDomains = [
        'medium.com',
        'dev.to',
        'blog.google',
        'github.com',
        'stackoverflow.com',
        'laravel.com',
        'php.net',
        'wikipedia.org',
    ];

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ],
        ]);
    }

    /**
     * Extract content from a webpage.
     */
    public function extract(string $url): array
    {
        try {
            Log::info('Initializing webpage extraction', ['url' => $url]);

            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException('Invalid URL provided');
            }

            Log::info('Fetching webpage content', ['url' => $url]);

            // Fetch the webpage
            $response = $this->client->request('GET', $url);
            $html = $response->getBody()->getContents();

            Log::info('Webpage fetched successfully', ['url' => $url]);

            // Create a new DOMDocument
            $dom = new DOMDocument();
            @$dom->loadHTML($html, LIBXML_NOERROR);
            $xpath = new DOMXPath($dom);

            // Extract title
            $title = $this->extractTitle($xpath);
            Log::info('Extracted title', ['title' => $title]);

            // Extract main content
            $content = $this->extractMainContent($xpath);
            Log::info('Content extracted', [
                'content_length' => strlen($content),
                'url' => $url
            ]);

            // Extract metadata
            $metadata = $this->extractMetadata($xpath);
            Log::info('Metadata extracted', ['metadata' => $metadata]);

            return [
                'title' => $title,
                'content' => $content,
                'metadata' => $metadata,
                'url' => $url,
                'extracted_at' => now()->toIso8601String()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to extract webpage content', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function isAllowedDomain(string $domain): bool
    {
        foreach ($this->allowedDomains as $allowedDomain) {
            if (str_ends_with($domain, $allowedDomain)) {
                return true;
            }
        }
        return false;
    }

    private function extractTitle(DOMXPath $xpath): string
    {
        $titleNode = $xpath->query('//title')->item(0);
        return $titleNode ? trim($titleNode->textContent) : '';
    }

    private function extractMainContent(DOMXPath $xpath): string
    {
        // Try to find main content using common selectors
        $selectors = [
            '//article',
            '//main',
            '//div[contains(@class, "content")]',
            '//div[@id="content"]',
            '//div[@class="content"]',
            '//div[contains(@class, "post-content")]',
            '//div[contains(@class, "entry-content")]',
            '//div[contains(@class, "article")]',
        ];

        foreach ($selectors as $selector) {
            $nodes = $xpath->query($selector);
            if ($nodes->length > 0) {
                $content = '';
                foreach ($nodes as $node) {
                    $content .= $this->cleanNodeContent($node);
                }
                if (!empty($content)) {
                    return $content;
                }
            }
        }

        // Fallback to body content if no main content found
        $body = $xpath->query('//body')->item(0);
        return $body ? $this->cleanNodeContent($body) : '';
    }

    private function cleanNodeContent(\DOMNode $node): string
    {
        // Clone the node to avoid modifying the original
        $clonedNode = $node->cloneNode(true);
        
        // Remove script and style elements
        $xpath = new DOMXPath($clonedNode->ownerDocument);
        $scripts = $xpath->query('.//script|.//style|.//noscript|.//iframe', $clonedNode);
        
        for ($i = $scripts->length - 1; $i >= 0; $i--) {
            $item = $scripts->item($i);
            if ($item && $item->parentNode) {
                $item->parentNode->removeChild($item);
            }
        }

        // Get text content and clean it
        $content = $clonedNode->textContent;
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);

        return $content;
    }

    private function extractMetadata(DOMXPath $xpath): array
    {
        $metadata = [
            'description' => '',
            'keywords' => '',
            'author' => '',
            'published_date' => '',
        ];

        // Extract meta description
        $descNode = $xpath->query('//meta[@name="description"]/@content')->item(0);
        if ($descNode) {
            $metadata['description'] = trim($descNode->nodeValue);
        }

        // Extract meta keywords
        $keywordsNode = $xpath->query('//meta[@name="keywords"]/@content')->item(0);
        if ($keywordsNode) {
            $metadata['keywords'] = trim($keywordsNode->nodeValue);
        }

        // Extract meta author
        $authorNode = $xpath->query('//meta[@name="author"]/@content')->item(0);
        if ($authorNode) {
            $metadata['author'] = trim($authorNode->nodeValue);
        }

        // Extract published date
        $dateNode = $xpath->query('//meta[@property="article:published_time"]/@content')->item(0);
        if ($dateNode) {
            $metadata['published_date'] = trim($dateNode->nodeValue);
        }

        return $metadata;
    }
} 