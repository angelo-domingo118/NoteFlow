<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SuggestionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Notebook suggestion routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/notebooks/{notebook}/generate-suggestions', [SuggestionController::class, 'generate'])
        ->name('api.notebooks.generate-suggestions');
}); 