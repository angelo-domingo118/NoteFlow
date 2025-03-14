<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Notebook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoteController extends ApiController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created note in storage.
     */
    public function store(Request $request, Notebook $notebook): RedirectResponse
    {
        $this->authorize('update', $notebook);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $notebook->notes()->create($validated);

        return back()->with('status', 'note-created');
    }

    /**
     * Update the specified note in storage.
     */
    public function update(Request $request, Note $note): RedirectResponse
    {
        $this->authorize('update', $note->notebook);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $note->update($validated);

        return back()->with('status', 'note-updated');
    }

    /**
     * Convert the note to a source.
     */
    public function convertToSource(Note $note): RedirectResponse
    {
        $this->authorize('update', $note->notebook);

        $note->notebook->sources()->create([
            'name' => $note->title,
            'type' => 'text',
            'data' => $note->content,
        ]);

        $note->delete();

        return back()->with('status', 'note-converted');
    }

    /**
     * Remove the specified note from storage.
     */
    public function destroy(Note $note): RedirectResponse
    {
        $this->authorize('update', $note->notebook);

        $note->delete();

        return back()->with('status', 'note-deleted');
    }
}
