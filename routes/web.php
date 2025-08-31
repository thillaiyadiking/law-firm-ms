<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\CaseNoteController;
use App\Http\Controllers\CaseFileController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Case routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Resource with renamed parameter
    Route::resource('cases', CaseController::class)->parameters([
        'cases' => 'caseModel',
    ]);

    // Case notes routes
    Route::post('cases/{caseModel}/notes', [CaseNoteController::class, 'store'])->name('cases.notes.store');
    Route::delete('notes/{note}', [CaseNoteController::class, 'destroy'])->name('notes.destroy');

    // Case files routes
    Route::post('cases/{caseModel}/files', [CaseFileController::class, 'store'])->name('cases.files.store');
    Route::get('files/{file}/download', [CaseFileController::class, 'download'])->name('files.download');
    Route::delete('files/{file}', [CaseFileController::class, 'destroy'])->name('files.destroy');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
