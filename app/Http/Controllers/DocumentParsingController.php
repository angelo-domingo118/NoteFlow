<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MistralOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentParsingController extends Controller
{
    private MistralOcrService $mistralOcrService;

    public function __construct(MistralOcrService $mistralOcrService)
    {
        $this->mistralOcrService = $mistralOcrService;
    }

    /**
     * Show the document parsing form.
     */
    public function index()
    {
        return view('document-parsing.index');
    }

    /**
     * Process a document upload.
     */
    public function processUpload(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        try {
            // Store the uploaded file
            $file = $request->file('document');
            $path = $file->store('documents');
            $fullPath = Storage::path($path);
            
            // Process the document with Mistral OCR
            $result = $this->mistralOcrService->processDocument($fullPath);
            
            // Store the OCR result
            $resultId = Str::uuid()->toString();
            $resultPath = "ocr_results/{$resultId}.json";
            Storage::put($resultPath, json_encode($result, JSON_PRETTY_PRINT));
            
            // Log success
            Log::info('Document processed successfully', [
                'document' => $path,
                'result' => $resultPath,
                'pages' => count($result['pages'] ?? [])
            ]);
            
            // Return the result view
            return view('document-parsing.result', [
                'result' => $result,
                'resultId' => $resultId,
                'documentName' => $file->getClientOriginalName()
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing document', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Failed to process document: ' . $e->getMessage()]);
        }
    }

    /**
     * Process a document from a URL.
     */
    public function processUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        try {
            $url = $request->input('url');
            
            // Process the document with Mistral OCR
            $result = $this->mistralOcrService->processDocumentFromUrl($url);
            
            // Store the OCR result
            $resultId = Str::uuid()->toString();
            $resultPath = "ocr_results/{$resultId}.json";
            Storage::put($resultPath, json_encode($result, JSON_PRETTY_PRINT));
            
            // Log success
            Log::info('Document from URL processed successfully', [
                'url' => $url,
                'result' => $resultPath,
                'pages' => count($result['pages'] ?? [])
            ]);
            
            // Return the result view
            return view('document-parsing.result', [
                'result' => $result,
                'resultId' => $resultId,
                'documentName' => $url
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing document from URL', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->input('url')
            ]);
            
            return back()->withErrors(['error' => 'Failed to process document from URL: ' . $e->getMessage()]);
        }
    }

    /**
     * View a previously processed OCR result.
     */
    public function viewResult(string $resultId)
    {
        $resultPath = "ocr_results/{$resultId}.json";
        
        if (!Storage::exists($resultPath)) {
            return abort(404, 'OCR result not found');
        }
        
        $result = json_decode(Storage::get($resultPath), true);
        
        return view('document-parsing.result', [
            'result' => $result,
            'resultId' => $resultId,
            'documentName' => 'Previously processed document'
        ]);
    }

    /**
     * Download the OCR result as JSON.
     */
    public function downloadResult(string $resultId)
    {
        $resultPath = "ocr_results/{$resultId}.json";
        
        if (!Storage::exists($resultPath)) {
            return abort(404, 'OCR result not found');
        }
        
        return Storage::download($resultPath, "ocr_result_{$resultId}.json");
    }
} 