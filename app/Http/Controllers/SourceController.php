<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Source;
use App\Models\Notebook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Jobs\ExtractWebPageContent;
use App\Jobs\ExtractYouTubeTranscript;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class SourceController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created source in storage.
     */
    public function store(Request $request, Notebook $notebook): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $notebook);

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'type' => ['required', 'string', 'in:text,file,website,youtube'],
                'content' => ['required_if:type,text', 'nullable', 'string'],
                'url' => [
                    'required_if:type,website,youtube', 
                    'nullable', 
                    'url',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->input('type') === 'youtube') {
                            if (!preg_match('/^https?:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[a-zA-Z0-9_-]{11}$/', $value)) {
                                $fail('The YouTube URL format is invalid.');
                            }
                        }
                    },
                ],
                'file' => ['required_if:type,file', 'nullable', 'file', 'mimes:pdf,txt,md', 'max:10240'], // 10MB max
            ]);

            // Create source with basic info
            $source = new Source([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'is_active' => true,
            ]);

            // Handle different source types
            switch ($validated['type']) {
                case 'text':
                    $source->data = $validated['content'];
                    break;

                case 'file':
                    if ($request->hasFile('file')) {
                        $file = $request->file('file');
                        $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('sources', $fileName, 'public');
                        
                        $source->data = '';  // Will be updated after text extraction
                        $source->file_path = $path;
                        $source->file_type = $file->getMimeType();
                    }
                    break;

                case 'website':
                case 'youtube':
                    $source->data = $validated['url'];
                    break;
            }

            $notebook->sources()->save($source);

            // If it's a website, dispatch the content extraction job
            if ($validated['type'] === 'website') {
                ExtractWebPageContent::dispatch($source);
            }
            
            // If it's a YouTube video, dispatch the transcript extraction job
            if ($validated['type'] === 'youtube') {
                ExtractYouTubeTranscript::dispatch($source);
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Source added successfully',
                    'source' => $source
                ]);
            }

            return back()->with('status', 'source-created');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Toggle source active status.
     */
    public function toggle(Source $source): RedirectResponse
    {
        $this->authorize('update', $source->notebook);

        $source->update(['is_active' => !$source->is_active]);

        return back()->with('status', 'source-toggled');
    }

    /**
     * Update the specified source in storage.
     */
    public function update(Request $request, Source $source): RedirectResponse
    {
        $this->authorize('update', $source->notebook);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'extracted_content' => ['nullable', 'string'],
        ]);

        $data = [
            'name' => $validated['name'],
        ];

        // Handle text content update
        if ($source->isText() && isset($validated['content'])) {
            $data['data'] = $validated['content'];
        }

        // Handle website content update
        if ($source->isWebsite() && isset($validated['extracted_content'])) {
            try {
                $websiteData = json_decode($source->data, true);
                if (is_array($websiteData)) {
                    $websiteData['content'] = $validated['extracted_content'];
                    $websiteData['updated_at'] = now()->toIso8601String();
                    $data['data'] = json_encode($websiteData);
                }
            } catch (\Exception $e) {
                // If there's an error parsing the JSON, just update the name
            }
        }
        
        // Handle YouTube content update
        if ($source->isYouTube() && isset($validated['extracted_content'])) {
            try {
                $youtubeData = json_decode($source->data, true);
                if (is_array($youtubeData)) {
                    $youtubeData['plain_text'] = $validated['extracted_content'];
                    $youtubeData['updated_at'] = now()->toIso8601String();
                    $data['data'] = json_encode($youtubeData);
                }
            } catch (\Exception $e) {
                // If there's an error parsing the JSON, just update the name
            }
        }

        // Handle retry extraction
        if ($source->isWebsite() && $request->has('retry_extraction')) {
            // Reset the data to just the URL
            if (is_string($source->data) && filter_var($source->data, FILTER_VALIDATE_URL)) {
                // It's just a URL string
                $url = $source->data;
            } else {
                // Try to extract URL from JSON
                try {
                    $websiteData = json_decode($source->data, true);
                    $url = $websiteData['url'] ?? null;
                } catch (\Exception $e) {
                    $url = null;
                }
            }

            if ($url) {
                $data['data'] = $url;
                $source->update($data);
                
                // Dispatch the extraction job
                ExtractWebPageContent::dispatch($source);
                
                return back()->with('status', 'extraction-retried');
            }
        }
        
        // Handle retry YouTube transcript extraction
        if ($source->isYouTube() && $request->has('retry_extraction')) {
            // Reset the data to just the URL
            if (is_string($source->data) && filter_var($source->data, FILTER_VALIDATE_URL)) {
                // It's just a URL string
                $url = $source->data;
            } else {
                // Try to extract URL from JSON
                try {
                    $youtubeData = json_decode($source->data, true);
                    $url = $youtubeData['url'] ?? null;
                } catch (\Exception $e) {
                    $url = null;
                }
            }

            if ($url) {
                $data['data'] = $url;
                $source->update($data);
                
                // Dispatch the extraction job
                ExtractYouTubeTranscript::dispatch($source);
                
                return back()->with('status', 'extraction-retried');
            }
        }

        $source->update($data);

        return back()->with('status', 'source-updated');
    }

    /**
     * Remove the specified source from storage.
     */
    public function destroy(Source $source): RedirectResponse
    {
        $this->authorize('update', $source->notebook);

        // Delete associated file if it exists
        if ($source->file_path) {
            Storage::disk('public')->delete($source->file_path);
        }

        $source->delete();

        return back()->with('status', 'source-deleted');
    }

    /**
     * Delete multiple sources at once.
     */
    public function batchDelete(Request $request, Notebook $notebook): JsonResponse
    {
        $this->authorize('update', $notebook);

        $validated = $request->validate([
            'sources' => ['required', 'array'],
            'sources.*' => ['required', 'integer', 'exists:sources,id'],
        ]);

        try {
            $sources = Source::whereIn('id', $validated['sources'])
                ->where('notebook_id', $notebook->id)
                ->get();

            foreach ($sources as $source) {
                $source->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => count($sources) . ' sources deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
