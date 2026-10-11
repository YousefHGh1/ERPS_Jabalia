<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\CraftLicenseController;
use App\Http\Controllers\EngineeringController;

/*
|--------------------------------------------------------------------------
| Cached Routes - مسارات مع التخزين المؤقت
|--------------------------------------------------------------------------
|
| هذه المسارات تستخدم CacheResponse middleware لتحسين الأداء
| مدة التخزين: 10 دقائق (600 ثانية)
|
*/

Route::middleware(['auth', 'cache.response:600'])->group(function () {
    
    // صفحة لوحة التحكم الرئيسية
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
    
    // صفحات الموظفين (الأكثر استخداماً)
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/filter', [EmployeeController::class, 'filter'])->name('employees.filter');
    
    // صفحات الطلبات (استعلامات ثقيلة)
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/filter', [RequestController::class, 'filter'])->name('requests.filter');
    
    // صفحات التراخيص
    Route::get('/craft-licenses', [CraftLicenseController::class, 'index'])->name('craft_licenses.index');
    Route::get('/engineerings', [EngineeringController::class, 'index'])->name('engineerings.index');
    
    // صفحات التقارير (استعلامات معقدة)
    Route::get('/reports/employees', [EmployeeController::class, 'reports'])->name('employees.reports');
    Route::get('/reports/requests', [RequestController::class, 'reports'])->name('requests.reports');
    Route::get('/reports/statistics', [HomeController::class, 'statistics'])->name('reports.statistics');
    
    // صفحات الهيكل التنظيمي
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    
    // صفحات الإجازات والحوافز
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    Route::get('/employee-holidays', [EmployeeHolidayController::class, 'index'])->name('employee_holidays.index');
    Route::get('/bonuses', [BonusController::class, 'index'])->name('bonuses.index');
    
    // صفحات المكلفين
    Route::get('/mokalafs', [MokalafController::class, 'index'])->name('mokalafs.index');
    
    // صفحات المركبات والسائقين
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
    
});
