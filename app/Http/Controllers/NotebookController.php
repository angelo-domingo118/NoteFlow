<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Notebook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotebookController extends ApiController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the notebooks.
     */
    public function index(Request $request): View
    {
        $sort = $request->query('sort', 'recent');
        $search = $request->query('search');
        
        $query = auth()->user()->notebooks();
        
        // Apply search filter if provided
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }
        
        switch ($sort) {
            case 'alpha':
                $query->orderBy('title');
                break;
            case 'modified':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'recent':
            default:
                $query->latest();
                break;
        }
        
        $notebooks = $query->withCount(['notes', 'sources'])->get();

        return view('notebooks.index', compact('notebooks', 'search'));
    }

    /**
     * Show the form for creating a new notebook.
     */
    public function create(): View
    {
        return view('notebooks.create');
    }

    /**
     * Store a newly created notebook in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $notebook = auth()->user()->notebooks()->create($validated);

        return redirect()->route('notebooks.show', $notebook)
            ->with('status', 'notebook-created');
    }

    /**
     * Display the specified notebook.
     */
    public function show(Notebook $notebook): View
    {
        $this->authorize('view', $notebook);

        $notes = $notebook->notes()->latest()->get();
        $sources = $notebook->sources()->get();

        return view('notebooks.show', compact('notebook', 'notes', 'sources'));
    }

    /**
     * Update the specified notebook in storage.
     */
    public function update(Request $request, Notebook $notebook): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $notebook);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $notebook->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notebook updated successfully',
                'notebook' => $notebook
            ]);
        }

        return redirect()->route('notebooks.show', $notebook)
            ->with('status', 'notebook-updated');
    }

    /**
     * Remove the specified notebook from storage.
     */
    public function destroy(Notebook $notebook): RedirectResponse
    {
        $this->authorize('delete', $notebook);

        $notebook->delete();

        return redirect()->route('notebooks.index')
            ->with('status', 'notebook-deleted');
    }
}
