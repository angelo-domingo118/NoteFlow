<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NotebookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SourceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('notebooks.index');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notebook routes
    Route::resource('notebooks', NotebookController::class);

    // Note routes
    Route::post('/notebooks/{notebook}/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::patch('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
    Route::post('/notes/{note}/convert', [NoteController::class, 'convertToSource'])->name('notes.convert');
    Route::post('/notebooks/{notebook}/notes/batch-delete', [NoteController::class, 'batchDelete'])->name('notes.batch-delete');
    Route::post('/notebooks/{notebook}/notes/batch-convert', [NoteController::class, 'batchConvert'])->name('notes.batch-convert');

    // Source routes
    Route::post('/notebooks/{notebook}/sources', [SourceController::class, 'store'])->name('sources.store');
    Route::patch('/sources/{source}', [SourceController::class, 'update'])->name('sources.update');
    Route::delete('/sources/{source}', [SourceController::class, 'destroy'])->name('sources.destroy');
    Route::post('/sources/{source}/toggle', [SourceController::class, 'toggle'])->name('sources.toggle');
    Route::post('/notebooks/{notebook}/sources/batch-delete', [SourceController::class, 'batchDelete'])->name('sources.batch-delete');

    // Chat routes
    Route::post('/notebooks/{notebook}/chat', [ChatController::class, 'generate'])->name('chat.generate');
    Route::get('/notebooks/{notebook}/suggestions', [ChatController::class, 'suggestions'])->name('chat.suggestions');
});

require __DIR__.'/auth.php';
