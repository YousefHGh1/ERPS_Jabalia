<?php

use App\Modules\CraftLicenses\Controllers\CraftLicenseController;
use Illuminate\Support\Facades\Route;

// Craft License Management Routes
Route::middleware(['auth', 'permission:view craft licenses'])->group(function () {
    Route::get('/', [CraftLicenseController::class, 'index'])->name('index');
    Route::get('/{license}', [CraftLicenseController::class, 'show'])->name('show');
    Route::get('/expiring', [CraftLicenseController::class, 'expiring'])->name('expiring');
    Route::get('/outstanding-payments', [CraftLicenseController::class, 'outstandingPayments'])->name('outstanding.payments');
});

Route::middleware(['auth', 'permission:create craft licenses'])->group(function () {
    Route::get('/create', [CraftLicenseController::class, 'create'])->name('create');
    Route::post('/', [CraftLicenseController::class, 'store'])->name('store');
});

Route::middleware(['auth', 'permission:update craft licenses'])->group(function () {
    Route::get('/{license}/edit', [CraftLicenseController::class, 'edit'])->name('edit');
    Route::put('/{license}', [CraftLicenseController::class, 'update'])->name('update');
    Route::post('/{license}/renew', [CraftLicenseController::class, 'renew'])->name('renew');
    Route::post('/{license}/suspend', [CraftLicenseController::class, 'suspend'])->name('suspend');
    Route::post('/{license}/reactivate', [CraftLicenseController::class, 'reactivate'])->name('reactivate');
});

Route::middleware(['auth', 'permission:delete craft licenses'])->group(function () {
    Route::delete('/{license}', [CraftLicenseController::class, 'destroy'])->name('destroy');
});

// API Routes
Route::middleware(['auth', 'permission:view craft licenses'])->group(function () {
    Route::get('/search', [CraftLicenseController::class, 'search'])->name('search');
});
