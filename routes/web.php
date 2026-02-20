<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectGeneratorWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('page.dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Web Routes for Project Generator
    Route::prefix('project')->group(function () {
        Route::get('/history', [ProjectGeneratorWebController::class, 'history'])->name('project.history');
        Route::get('/create', [ProjectGeneratorWebController::class, 'create'])->name('project.create');
        Route::get('/{id}/viewer', [ProjectGeneratorWebController::class, 'viewer'])->name('project.viewer');

        // Generator Action Routes
        Route::post('/generate', [\App\Http\Controllers\ProjectGeneratorController::class, 'generate']);
        Route::get('/{id}/stream-log', [\App\Http\Controllers\ProjectGeneratorController::class, 'streamLog']);
        Route::get('/{id}/tree', [\App\Http\Controllers\ProjectGeneratorController::class, 'getTree']);
        Route::get('/{id}/file', [\App\Http\Controllers\ProjectGeneratorController::class, 'getFile']);
        Route::post('/{id}/ai-chat', [\App\Http\Controllers\ProjectGeneratorController::class, 'handleAiChat']);
    });
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard.index');
    })->name('dashboard');
});

require __DIR__ . '/auth.php';
