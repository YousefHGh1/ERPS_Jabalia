<?php

use App\Http\Controllers\PerformanceMonitorController;
use Illuminate\Support\Facades\Route;

// Performance monitoring routes
Route::middleware(['auth', 'role_or_permission:admin|monitor performance'])->prefix('performance')->name('performance.')->group(function () {
    Route::get('/', [PerformanceMonitorController::class, 'index'])->name('dashboard');
    Route::get('/metrics', [PerformanceMonitorController::class, 'getMetrics'])->name('metrics');
    Route::post('/clear-cache', [PerformanceMonitorController::class, 'clearCache'])->name('clear-cache');
    Route::get('/report', [PerformanceMonitorController::class, 'generateReport'])->name('report');
});
