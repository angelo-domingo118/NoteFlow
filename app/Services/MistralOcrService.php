<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MistralOcrService
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl = 'https://api.mistral.ai/v1';

    public function __construct()
    {
        $this->apiKey = config('services.mistral.api_key');
        
        if (empty($this->apiKey)) {
            throw new \RuntimeException('Mistral API key is not configured');
        }
        
        $this->client = new Client([
            'timeout' => 60,
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Process a document using Mistral OCR.
     *
     * @param string $documentPath Path to the document file (PDF or image)
     * @param bool $includeImageBase64 Whether to include base64-encoded images in the response
     * @return array The OCR processing results
     * @throws \Exception
     */
    public function processDocument(string $documentPath, bool $includeImageBase64 = false): array
    {
        try {
            Log::info('Processing document with Mistral OCR', ['path' => $documentPath]);
            
            // Check if file exists
            if (!file_exists($documentPath)) {
                throw new \InvalidArgumentException("Document file not found: {$documentPath}");
            }
            
            // Get file mime type
            $mimeType = mime_content_type($documentPath);
            $isImage = str_starts_with($mimeType, 'image/');
            $isPdf = $mimeType === 'application/pdf';
            
            if (!$isImage && !$isPdf) {
                throw new \InvalidArgumentException("Unsupported file type: {$mimeType}. Only PDF and images are supported.");
            }
            
            // Encode file to base64
            $base64Content = base64_encode(file_get_contents($documentPath));
            
            // Prepare request payload
            $payload = [
                'model' => 'mistral-ocr-latest',
                'document' => [
                    'type' => $isImage ? 'image_url' : 'document_url',
                    $isImage ? 'image_url' : 'document_url' => "data:{$mimeType};base64,{$base64Content}"
                ],
                'include_image_base64' => $includeImageBase64
            ];
            
            // Make API request
            $response = $this->client->post("{$this->baseUrl}/ocr", [
                'json' => $payload
            ]);
            
            // Parse response
            $result = json_decode($response->getBody()->getContents(), true);
            
            // Transform the response to match the expected format
            $transformedResult = [
                'pages' => array_map(function ($page) {
                    return [
                        'index' => $page['index'] ?? 0,
                        'markdown' => $page['markdown'] ?? '',
                        'images' => $page['images'] ?? [],
                        'dimensions' => $page['dimensions'] ?? [
                            'dpi' => 200,
                            'height' => 0,
                            'width' => 0
                        ]
                    ];
                }, $result['pages'] ?? [])
            ];
            
            Log::info('Document processed successfully', [
                'pages' => count($transformedResult['pages']),
                'status' => $response->getStatusCode()
            ]);
            
            return $transformedResult;
        } catch (GuzzleException $e) {
            Log::error('Error processing document with Mistral OCR', [
                'error' => $e->getMessage(),
                'path' => $documentPath
            ]);
            
            throw new \Exception('Failed to process document: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Process a document from a URL using Mistral OCR.
     *
     * @param string $url URL of the document to process
     * @param bool $includeImageBase64 Whether to include base64-encoded images in the response
     * @return array The OCR processing results
     * @throws \Exception
     */
    public function processDocumentFromUrl(string $url, bool $includeImageBase64 = false): array
    {
        try {
            Log::info('Processing document from URL with Mistral OCR', ['url' => $url]);
            
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException('Invalid URL provided');
            }
            
            // Prepare request payload
            $payload = [
                'model' => 'mistral-ocr-latest',
                'document' => [
                    'type' => 'document_url',
                    'document_url' => $url
                ],
                'include_image_base64' => $includeImageBase64
            ];
            
            // Make API request
            $response = $this->client->post("{$this->baseUrl}/ocr", [
                'json' => $payload
            ]);
            
            // Parse response
            $result = json_decode($response->getBody()->getContents(), true);
            
            // Transform the response to match the expected format
            $transformedResult = [
                'pages' => array_map(function ($page) {
                    return [
                        'index' => $page['index'] ?? 0,
                        'markdown' => $page['markdown'] ?? '',
                        'images' => $page['images'] ?? [],
                        'dimensions' => $page['dimensions'] ?? [
                            'dpi' => 200,
                            'height' => 0,
                            'width' => 0
                        ]
                    ];
                }, $result['pages'] ?? [])
            ];
            
            Log::info('Document from URL processed successfully', [
                'pages' => count($transformedResult['pages']),
                'status' => $response->getStatusCode()
            ]);
            
            return $transformedResult;
        } catch (GuzzleException $e) {
            Log::error('Error processing document from URL with Mistral OCR', [
                'error' => $e->getMessage(),
                'url' => $url
            ]);
            
            throw new \Exception('Failed to process document from URL: ' . $e->getMessage(), 0, $e);
        }
    }
} 