<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Source;
use App\Models\Notebook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SourceController extends ApiController
{
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
    public function store(Request $request, Notebook $notebook): RedirectResponse
    {
        $this->authorize('update', $notebook);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:text,file,link'],
            'data' => ['required', 'string'],
        ]);

        $notebook->sources()->create($validated + ['is_active' => true]);

        return back()->with('status', 'source-created');
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
        ]);

        $source->update($validated);

        return back()->with('status', 'source-updated');
    }

    /**
     * Remove the specified source from storage.
     */
    public function destroy(Source $source): RedirectResponse
    {
        $this->authorize('update', $source->notebook);

        $source->delete();

        return back()->with('status', 'source-deleted');
    }
}
