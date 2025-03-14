<?php

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Import the MistralOcrService
use App\Services\MistralOcrService;

// Test document paths - update these with actual paths to test documents
$pdfPath = __DIR__ . '/storage/app/test_documents/sample.pdf';
$imagePath = __DIR__ . '/storage/app/test_documents/sample.jpg';
$documentUrl = 'https://arxiv.org/pdf/2201.04234';

// Create test document directory if it doesn't exist
$testDocDir = __DIR__ . '/storage/app/test_documents';
if (!file_exists($testDocDir)) {
    mkdir($testDocDir, 0755, true);
    echo "Created test document directory: $testDocDir\n";
}

// Function to download a sample PDF if none exists
function downloadSamplePdf($savePath) {
    echo "Downloading sample PDF...\n";
    $client = new GuzzleHttp\Client([
        'timeout' => 30,
        'verify' => false,
    ]);
    
    try {
        $response = $client->get('https://arxiv.org/pdf/2201.04234', [
            'sink' => $savePath
        ]);
        echo "Sample PDF downloaded successfully to: $savePath\n";
        return true;
    } catch (\Exception $e) {
        echo "Error downloading sample PDF: " . $e->getMessage() . "\n";
        return false;
    }
}

// Check if test PDF exists, download if not
if (!file_exists($pdfPath)) {
    if (!downloadSamplePdf($pdfPath)) {
        echo "Failed to download sample PDF. Please provide a test document manually.\n";
        exit(1);
    }
}

try {
    echo "Starting Mistral OCR test...\n";
    
    // Initialize the service
    $mistralService = new MistralOcrService();
    
    // Test 1: Process a PDF document
    if (file_exists($pdfPath)) {
        echo "\n=== Testing PDF document processing ===\n";
        $result = $mistralService->processDocument($pdfPath);
        
        echo "PDF processing successful!\n";
        echo "Number of pages: " . count($result['pages']) . "\n";
        
        // Display content for each page
        foreach ($result['pages'] as $page) {
            echo "\n=== Page " . $page['index'] . " ===\n";
            if (!empty($page['markdown'])) {
                echo $page['markdown'] . "\n";
            }
            if (!empty($page['images'])) {
                echo "\nImages found: " . count($page['images']) . "\n";
            }
            echo "----------------------------------------\n";
        }
    } else {
        echo "Skipping PDF test - no PDF file available at: $pdfPath\n";
    }
    
    // Test 2: Process an image document
    if (file_exists($imagePath)) {
        echo "\n=== Testing image document processing ===\n";
        $result = $mistralService->processDocument($imagePath);
        
        echo "Image processing successful!\n";
        echo "Content length: " . strlen($result['pages'][0]['markdown'] ?? '') . " characters\n";
        
        // Display content
        if (!empty($result['pages'][0]['markdown'])) {
            echo "\n=== Image Content ===\n";
            echo $result['pages'][0]['markdown'] . "\n";
            echo "----------------------------------------\n";
        }
    } else {
        echo "Skipping image test - no image file available at: $imagePath\n";
    }
    
    // Test 3: Process a document from URL
    echo "\n=== Testing document processing from URL ===\n";
    $result = $mistralService->processDocumentFromUrl($documentUrl);
    
    echo "URL document processing successful!\n";
    echo "Number of pages: " . count($result['pages']) . "\n";
    
    // Display content for each page
    foreach ($result['pages'] as $page) {
        echo "\n=== Page " . $page['index'] . " ===\n";
        if (!empty($page['markdown'])) {
            echo $page['markdown'] . "\n";
        }
        if (!empty($page['images'])) {
            echo "\nImages found: " . count($page['images']) . "\n";
        }
        echo "----------------------------------------\n";
    }
    
    echo "\nAll tests completed successfully!\n";
    
} catch (\Exception $e) {
    echo "Error during Mistral OCR testing: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    
    // Check if it's an API key issue
    if (strpos($e->getMessage(), 'api_key') !== false || strpos($e->getMessage(), '401') !== false) {
        echo "\nIMPORTANT: It appears there might be an issue with your Mistral API key.\n";
        echo "Please make sure you've set a valid API key in your .env file:\n";
        echo "MISTRAL_API_KEY=your_actual_api_key_here\n";
    }
} 