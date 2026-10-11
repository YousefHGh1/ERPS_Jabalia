<?php

use App\Modules\Requests\Controllers\RequestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:create requests'])->group(function () {
    Route::get('/create', [RequestController::class, 'create'])->name('create');
    Route::post('/', [RequestController::class, 'store'])->name('store');
});

// Request Management Routes
Route::middleware(['auth', 'permission:view requests'])->group(function () {
    Route::get('/', [RequestController::class, 'index'])->name('index');
    Route::get('/{request}', [RequestController::class, 'show'])->name('show');
});

Route::middleware(['auth', 'permission:update requests'])->group(function () {
    Route::get('/{request}/edit', [RequestController::class, 'edit'])->name('edit');
    Route::put('/{request}', [RequestController::class, 'update'])->name('update');
    Route::post('/{request}/status', [RequestController::class, 'updateStatus'])->name('status.update');
});

Route::middleware(['auth', 'permission:delete requests'])->group(function () {
    Route::delete('/{request}', [RequestController::class, 'destroy'])->name('destroy');
});

// API Routes
Route::middleware(['auth', 'permission:view requests'])->group(function () {
    Route::get('/search', [RequestController::class, 'search'])->name('search');
    Route::post('/{request}/attachment', [RequestController::class, 'addAttachment'])->name('attachment.add');
});
