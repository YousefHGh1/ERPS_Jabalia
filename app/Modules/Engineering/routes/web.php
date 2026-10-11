<?php

use App\Modules\Engineering\Controllers\EngineeringController;
use Illuminate\Support\Facades\Route;

// Engineering Management Routes
Route::middleware(['auth', 'permission:index engineerings'])->group(function () {
    Route::get('/', [EngineeringController::class, 'index'])->name('index');
    Route::get('/neighborhood/{neighborhood}', [EngineeringController::class, 'byNeighborhood'])->name('neighborhood');
    Route::get('/outstanding-payments', [EngineeringController::class, 'outstandingPayments'])->name('outstanding.payments');
    Route::get('/{file}', [EngineeringController::class, 'show'])->name('show')->where('file', '[0-9]+');
});

Route::middleware(['auth', 'permission:create engineerings'])->group(function () {
    Route::get('/create', [EngineeringController::class, 'create'])->name('create');
    Route::post('/', [EngineeringController::class, 'store'])->name('store');
});

Route::middleware(['auth', 'permission:update engineerings'])->group(function () {
    Route::get('/{file}/edit', [EngineeringController::class, 'edit'])->name('edit')->where('file', '[0-9]+');
    Route::put('/{file}', [EngineeringController::class, 'update'])->name('update')->where('file', '[0-9]+');
    Route::post('/{file}/approve', [EngineeringController::class, 'approve'])->name('approve')->where('file', '[0-9]+');
    Route::post('/{file}/reject', [EngineeringController::class, 'reject'])->name('reject')->where('file', '[0-9]+');
    Route::post('/{file}/complete', [EngineeringController::class, 'complete'])->name('complete')->where('file', '[0-9]+');
});

Route::middleware(['auth', 'permission:delete engineerings'])->group(function () {
    Route::delete('/{file}', [EngineeringController::class, 'destroy'])->name('destroy')->where('file', '[0-9]+');
});

// API Routes
Route::middleware(['auth', 'permission:index engineerings'])->group(function () {
    Route::get('/search', [EngineeringController::class, 'search'])->name('search');
});
