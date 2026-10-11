<?php

use App\Modules\Employees\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

// Employee Management Routes
Route::middleware(['auth', 'permission:view employees'])->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('index');
    Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
    Route::get('/department/{departmentId}', [EmployeeController::class, 'byDepartment'])->name('department');
    Route::get('/section/{sectionId}', [EmployeeController::class, 'bySection'])->name('section');
    Route::get('/active', [EmployeeController::class, 'active'])->name('active');
    Route::get('/on-leave', [EmployeeController::class, 'onLeave'])->name('on.leave');
    Route::get('/upcoming-holidays', [EmployeeController::class, 'upcomingHolidays'])->name('upcoming.holidays');
    Route::get('/expiring-documents', [EmployeeController::class, 'expiringDocuments'])->name('expiring.documents');
});

Route::middleware(['auth', 'permission:create employees'])->group(function () {
    Route::get('/create', [EmployeeController::class, 'create'])->name('create');
    Route::post('/', [EmployeeController::class, 'store'])->name('store');
});

Route::middleware(['auth', 'permission:update employees'])->group(function () {
    Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
    Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
    Route::post('/{employee}/status', [EmployeeController::class, 'updateStatus'])->name('status.update');
    Route::post('/bulk-update-status', [EmployeeController::class, 'bulkUpdateStatus'])->name('bulk.status.update');
});

Route::middleware(['auth', 'permission:delete employees'])->group(function () {
    Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
});

// API Routes
Route::middleware(['auth', 'permission:view employees'])->group(function () {
    Route::get('/search', [EmployeeController::class, 'search'])->name('search');
});
