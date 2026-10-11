<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Api\CacheController;
use App\Http\Controllers\Api\SecurityController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Auth\UpdatePasswordController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchDirectorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CentralTreasuryController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\CitizenInvoiceController;
use App\Http\Controllers\CollectionInvoiceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentDirectorController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployeeAdvanceController;
use App\Http\Controllers\EmployeeBonusController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeDependentController;
use App\Http\Controllers\EmployeeHolidayController;
use App\Http\Controllers\EmployeeJobHistoryController;
use App\Http\Controllers\EmployeeJobInfoController;
use App\Http\Controllers\EmployeePermissionController;
use App\Http\Controllers\EmployeePortalController;
use App\Http\Controllers\EmployeeReportController;
use App\Http\Controllers\Engineering\EngineeringController;
use App\Http\Controllers\ExternalCollectionController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LegacyEngineeringController;
use App\Http\Controllers\MainHolidayController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrganizationChartController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\RealTimeDashboardController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SectionDirectorController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// فحص صحة التطبيق (للموازنات والمراقبة) — لا يتطلب تسجيل دخول
Route::get('/up', function () {
    try {
        DB::connection()->getPdo();
        DB::select('SELECT 1');
    } catch (\Throwable $e) {
        return response()->json(['status' => 'error', 'message' => 'Database unavailable'], 503);
    }
    return response()->json(['status' => 'ok'], 200);
})->name('health');

// Route for serving images from storage (works on both local and production)
Route::get('/storage-images/{path}', [ImageController::class, 'show'])->where('path', '.*')->name('storage.image');

Auth::routes(['register' => false]);

// Auth routes with strict rate limiting
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

// Password change with strict rate limiting
Route::middleware(['auth', 'throttle:3,1'])->post('/password/change', [UpdatePasswordController::class, 'update'])->name('password.change');

// Settings routes (Users, Roles, Permissions) - must be registered before package routes to use custom views
Route::middleware(config('permission_ui.middleware'))->group(function () {
    Route::redirect(config('permission_ui.url_prefix'), config('permission_ui.url_prefix') . '/dashboard');
    Route::group([
        'prefix' => config('permission_ui.url_prefix'),
        'as'     => config('permission_ui.route_name_prefix'),
    ], function () {
        Route::get('dashboard', [\App\Http\Controllers\Settings\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('roles', \App\Http\Controllers\Settings\RoleController::class)->except('show');
        Route::post('roles/{role}/duplicate', [\App\Http\Controllers\Settings\RoleController::class, 'duplicate'])
            ->name('roles.duplicate');
        Route::post('roles/create-by-type', [\App\Http\Controllers\Settings\RoleController::class, 'createByType'])
            ->name('roles.create-by-type');
        Route::resource('permissions', \App\Http\Controllers\Settings\PermissionController::class)->except('show');
        Route::post('permissions/sync-controllers', [\App\Http\Controllers\Settings\PermissionController::class, 'syncControllerPermissions'])
            ->name('permissions.sync-controllers');
        Route::resource('users', \App\Http\Controllers\Settings\UserController::class)->only('index', 'edit', 'update', 'destroy');
        Route::post('users/{user}/reset-password', [\App\Http\Controllers\Settings\UserController::class, 'resetPassword'])
            ->name('users.reset-password');
        
        // Export routes
        Route::get('export/users', [\App\Http\Controllers\Settings\ExportController::class, 'exportUsers'])
            ->name('export.users');
        Route::get('export/roles', [\App\Http\Controllers\Settings\ExportController::class, 'exportRoles'])
            ->name('export.roles');
        Route::get('export/permissions', [\App\Http\Controllers\Settings\ExportController::class, 'exportPermissions'])
            ->name('export.permissions');
        Route::get('export/permission-matrix', [\App\Http\Controllers\Settings\ExportController::class, 'exportPermissionMatrix'])
            ->name('export.permission-matrix');
    });
});

Route::middleware(['auth', \App\Http\Middleware\EagerLoadAuthUser::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // User Guide Route
    Route::get('/user-guide', function () {
        return view('user-guide.index');
    })->name('user-guide');

    // Real-time Dashboard Routes (protected by permission)
    Route::middleware('can:index real_time_dashboards')->group(function () {
        Route::get('/dashboard/realtime', [RealTimeDashboardController::class, 'index'])->name('dashboard.realtime');
        Route::get('/dashboard/realtime-stats', [RealTimeDashboardController::class, 'getRealTimeStats'])->name('dashboard.realtime-stats');
        Route::get('/dashboard/live-updates', [RealTimeDashboardController::class, 'getLiveUpdates'])->name('dashboard.live-updates');
    });

    // صفحات لوحة التحكم المنفصلة
    Route::get('/admin/administrative', [AdminDashboardController::class, 'administrative'])->name('admin.administrative');
    Route::get('/admin/crafts', [AdminDashboardController::class, 'crafts'])->name('admin.crafts');
    Route::get('/admin/engineering', [AdminDashboardController::class, 'engineering'])->name('admin.engineering');
    Route::post('/admin/clear-cache', [AdminDashboardController::class, 'clearCache'])->name('admin.clear_cache');
    // Central Treasury (الصندوق المركزي للإيرادات)
    Route::get('/central-treasury/today', [CentralTreasuryController::class, 'todaySummary'])->name('central_treasury.today');
    Route::get('/central-treasury/history', [CentralTreasuryController::class, 'history'])->name('central_treasury.history');
    Route::get('/central-treasury/batches/{batch}', [CentralTreasuryController::class, 'showBatch'])->name('central_treasury.show_batch');
    Route::post('/central-treasury/batches/{batch}/reopen', [CentralTreasuryController::class, 'reopenBatch'])->name('central_treasury.reopen_batch');
    Route::post('/central-treasury/section-batches/{batch}/close', [CentralTreasuryController::class, 'closeSectionBatch'])->name('central_treasury.close_section_batch');
    Route::post('/central-treasury/unified/close-today', [CentralTreasuryController::class, 'closeUnifiedToday'])->name('central_treasury.close_unified_today');
    Route::get('/citizens/unified-invoice', [CitizenInvoiceController::class, 'index'])->name('citizen.unified_invoice');
    Route::get('/citizens/unified-invoice/print', [CitizenInvoiceController::class, 'print'])->name('citizen.unified_invoice.print');

    // الاستعلام عن فواتير الجباية (ضمن خانة قلم الجمهور)
    Route::get('/public-pen/collection-inquiry', [CollectionInvoiceController::class, 'index'])->name('collection.inquiry');
    Route::get('/public-pen/collection-inquiry/print', [CollectionInvoiceController::class, 'print'])->name('collection.inquiry.print');
    Route::get('/public-pen/collection-inquiry/{id}', [CollectionInvoiceController::class, 'show'])->name('collection.inquiry.show');
    Route::get('/receipts/payments/craft/{payment}', [PaymentReceiptController::class, 'craft'])->name('receipts.payments.craft');
    Route::get('/receipts/payments/engineering/{payment}', [PaymentReceiptController::class, 'engineering'])->name('receipts.payments.engineering');
    Route::get('/receipts/payments/request/{payment}', [PaymentReceiptController::class, 'request'])->name('receipts.payments.request');
    Route::get('/receipts/external-collection/{payment}', [PaymentReceiptController::class, 'externalCollection'])->name('receipts.external-collection');
    Route::get('/zk', [HomeController::class, 'zk'])->name('zk');
    Route::resource('categories', CategoryController::class);
    Route::resource('jobs', JobController::class);
    Route::resource('/departments', DepartmentController::class);
    Route::get('/structure/org-chart', [OrganizationChartController::class, 'index'])->name('structure.org-chart');
    Route::get('/structure/org-chart/builder', [OrganizationChartController::class, 'builder'])->name('structure.org-chart.builder');
    Route::post('/structure/org-chart/builder', [OrganizationChartController::class, 'saveBuilder'])->name('structure.org-chart.builder.save');
    Route::get('/structure/org-chart/export/word', [OrganizationChartController::class, 'exportWord'])->name('structure.org-chart.export.word');
    Route::get('/structure/org-chart/export/pdf/a3', [OrganizationChartController::class, 'exportPdfA3'])->name('structure.org-chart.export.pdf.a3');
    // نقاط النهاية الخاصة بالنافذة المنبثقة لمخطط الهيكل التنظيمي (عرض الهيكلية + جلب بيانات التعديل + التحديث)
    Route::get('/structure/org-chart/print/{type}/{id}', [OrganizationChartController::class, 'printUnit'])
        ->where('type', 'department|section|branch')
        ->where('id', '[0-9]+')
        ->name('structure.org-chart.print');
    Route::get('/structure/org-chart/hierarchy/{type}/{id}', [OrganizationChartController::class, 'hierarchy'])
        ->where('type', 'department|section|branch')
        ->where('id', '[0-9]+')
        ->name('structure.org-chart.hierarchy');
    Route::get('/structure/org-chart/edit-data/{type}/{id}', [OrganizationChartController::class, 'editData'])
        ->where('type', 'department|section|branch')
        ->where('id', '[0-9]+')
        ->name('structure.org-chart.edit-data');
    Route::put('/structure/org-chart/update/{type}/{id}', [OrganizationChartController::class, 'update'])
        ->where('type', 'department|section|branch')
        ->where('id', '[0-9]+')
        ->name('structure.org-chart.update');
    // نقاط النهاية الخاصة بالإضافة من داخل نافذة الهيكلية (قسم تحت دائرة / شعبة تحت قسم أو دائرة / موظف تحت أي وحدة)
    Route::get('/structure/org-chart/create-data/{type}/{unitType}/{id}', [OrganizationChartController::class, 'createData'])
        ->where('type', 'section|branch|employee')
        ->where('unitType', 'department|section|branch')
        ->where('id', '[0-9]+')
        ->name('structure.org-chart.create-data');
    Route::post('/structure/org-chart/store/{type}', [OrganizationChartController::class, 'store'])
        ->where('type', 'section|branch|employee')
        ->name('structure.org-chart.store');
    // نقاط النهاية لتعيين/تغيير مدير (دائرة/قسم/شعبة) من داخل نافذة الهيكلية
    Route::get('/structure/org-chart/director-data/{type}/{id}', [OrganizationChartController::class, 'directorData'])
        ->where('type', 'department|section|branch')
        ->where('id', '[0-9]+')
        ->name('structure.org-chart.director-data');
    Route::post('/structure/org-chart/store-director/{type}/{id}', [OrganizationChartController::class, 'storeDirector'])
        ->where('type', 'department|section|branch')
        ->where('id', '[0-9]+')
        ->name('structure.org-chart.store-director');
    Route::delete('/structure/org-chart/delete-director/{type}/{id}', [OrganizationChartController::class, 'deleteDirector'])
        ->where('type', 'department|section|branch')
        ->where('id', '[0-9]+')
        ->name('structure.org-chart.delete-director');
    // نقطة نهاية حذف قسم/شعبة من داخل نافذة الهيكلية (بنفس صلاحيات شاشة الحذف الأصلية)
    Route::delete('/structure/org-chart/delete/{type}/{id}', [OrganizationChartController::class, 'destroy'])
        ->where('type', 'section|branch')
        ->where('id', '[0-9]+')
        ->name('structure.org-chart.delete');
    Route::resource('/sections', SectionController::class);
    Route::resource('/branches', BranchController::class);
    Route::get('/employees_export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::get('/tawjihi-export', [EmployeeController::class, 'exportTawjihiDependents'])->name('tawjihi.export');
    Route::resource('/employees', EmployeeController::class)->except(['create', 'edit']);
    Route::post('/employees/import', [EmployeeController::class, 'importEmployee'])->name('employees.import');
    Route::resource('/attachments', AttachmentController::class);
    Route::get('/attachments/{id}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::resource('/departmentdirector', DepartmentDirectorController::class);
    Route::resource('/sectiondirector', SectionDirectorController::class);
    Route::resource('/branchdirector', BranchDirectorController::class);
    Route::resource('/jobinfo', EmployeeJobInfoController::class);
    Route::post('/jobinfo/{employee}/sync-related', [EmployeeJobInfoController::class, 'syncRelatedData'])->name('jobinfo.sync-related');
    Route::resource('/holidays', HolidayController::class);
    Route::resource('/EmployeeHolidays', EmployeeHolidayController::class);
    Route::resource('/bonus', BonusController::class);
    Route::resource('/bonus_employee', EmployeeBonusController::class);
    Route::resource('/dependents', EmployeeDependentController::class);
    Route::resource('/job_history', EmployeeJobHistoryController::class);
    Route::resource('/main_holiday', MainHolidayController::class);
    Route::resource('/EmployeePermissions', EmployeePermissionController::class);
    Route::resource('/Shift', ShiftController::class);

    // Employee Advance (سلفة) Routes
    Route::prefix('employee-advances')->middleware(['auth'])->group(function () {
        Route::get('/', [EmployeeAdvanceController::class, 'index'])->name('employee-advances.index');
        Route::get('/create', [EmployeeAdvanceController::class, 'create'])->name('employee-advances.create');
        Route::post('/', [EmployeeAdvanceController::class, 'store'])->name('employee-advances.store');
        Route::get('/{id}', [EmployeeAdvanceController::class, 'show'])->name('employee-advances.show');
        Route::post('/{id}/approve-section', [EmployeeAdvanceController::class, 'approveBySection'])->name('employee-advances.approve-section');
        Route::post('/{id}/approve-mayor', [EmployeeAdvanceController::class, 'approveByMayor'])->name('employee-advances.approve-mayor');
        Route::post('/{id}/approve-finance', [EmployeeAdvanceController::class, 'approveByFinance'])->name('employee-advances.approve-finance');
        Route::post('/{id}/reject', [EmployeeAdvanceController::class, 'reject'])->name('employee-advances.reject');
    });

    // Notifications Routes (موحّدة - أسماء kebab-case)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/latest', [NotificationController::class, 'getLatest'])->name('notifications.latest');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');

    Route::resource('/documents', DocumentController::class);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    Route::resource('/vehicle', VehicleController::class);

    // Engineering routes are now handled by the Engineering module
    // Temporary route for export using old controller
    Route::get('/engineering_export', [EngineeringController::class, 'export'])->name('engineering.export');

    // الملفات التاريخية (هندسة وتنظيم) — جدول MASTER
    Route::get('/legacy-engineering', [LegacyEngineeringController::class, 'index'])->name('legacy-engineering.index');
    Route::get('/legacy-engineering/{id}', [LegacyEngineeringController::class, 'show'])->name('legacy-engineering.show');

    Route::get('/employee_reports', [EmployeeReportController::class, 'employee_reports'])->name('employee_reports');
    Route::post('/get_employee_reports', [EmployeeReportController::class, 'getEmployeeReport'])->name('getEmployeeReport');

    Route::post('/update-password', [UpdatePasswordController::class, 'update'])->name('user.password.update');
    Route::post('/get-czn', [CitizenController::class, 'getCzn'])->name('getCzn');
    Route::get('/citizen-for-mokalaf', [CitizenController::class, 'getCitizenForMokalaf'])->name('citizen.forMokalaf');
    Route::get('/userdata', [CitizenController::class, 'getCitizenForMokalaf'])->name('getuserData');
    Route::get('/insertuserdata', [CitizenController::class, 'insertUserData'])->name('insertuserData');

    Route::post('/accept_holiday/{id}', [EmployeeHolidayController::class, 'acceptHoliday'])->name('acceptHoliday');
    Route::post('/reject_holiday/{id}', [EmployeeHolidayController::class, 'rejectHoliday'])->name('rejectHoliday');

    Route::post('/accept_permission/{id}', [EmployeePermissionController::class, 'accept_permission'])->name('accept_permission');
    Route::post('/reject_permission/{id}', [EmployeePermissionController::class, 'reject_permission'])->name('reject_permission');

    // مزامنة الحضور يدوياً (للتطوير/الاختبار فقط - غير متاح في الإنتاج)
    if (! app()->environment('production')) {
        Route::get('/test', function (\App\Services\AttendanceService $attendanceService) {
            $count = $attendanceService->syncAttendance();
            return "Synced $count records.";
        });
    }

    Route::get('/employee-portal', [EmployeePortalController::class, 'index'])->name('employee.portal');
    Route::get('/employee-portal/calendar', [EmployeePortalController::class, 'attendanceCalendar'])->name('employee.calendar');
    Route::get('/employee-portal/requests', [EmployeePortalController::class, 'requests'])->name('employee.requests');
    Route::post('/employee-portal/holidays', [EmployeePortalController::class, 'storeHoliday'])->name('employee.holidays.store');
    Route::post('/employee-portal/permissions', [EmployeePortalController::class, 'storePermission'])->name('employee.permissions.store');
    Route::get('/employee-portal/attendance-data', [EmployeePortalController::class, 'getAttendanceData'])->name('employee.attendance.data');
    Route::get('/get_sections_list/{id}', [HomeController::class, 'getSectionsList'])->name('get_sections_list');
    Route::get('/get_branches_list/{id}', [HomeController::class, 'get_branches_list'])->name('get_branches_list');

    // External Collection Routes (التحصيل الخارجي)
    Route::prefix('external-collections')->middleware(['auth'])->group(function () {
        Route::get('/', [ExternalCollectionController::class, 'index'])->name('external-collections.index');
        Route::get('/create', [ExternalCollectionController::class, 'create'])->name('external-collections.create');
        Route::post('/', [ExternalCollectionController::class, 'store'])->name('external-collections.store');
        Route::get('/{externalCollection}', [ExternalCollectionController::class, 'show'])->name('external-collections.show');
        Route::get('/{externalCollection}/edit', [ExternalCollectionController::class, 'edit'])->name('external-collections.edit');
        Route::put('/{externalCollection}', [ExternalCollectionController::class, 'update'])->name('external-collections.update');
        Route::delete('/{externalCollection}', [ExternalCollectionController::class, 'destroy'])->name('external-collections.destroy');
        Route::post('/search-taxpayer', [ExternalCollectionController::class, 'searchTaxpayer'])->name('external-collections.search-taxpayer');
        Route::get('/{externalCollection}/payment', [ExternalCollectionController::class, 'paymentForm'])->name('external-collections.payment');
        Route::post('/{externalCollection}/payment', [ExternalCollectionController::class, 'processPayment'])->name('external-collections.process-payment');
        Route::post('/payments/{payment}/cancel', [ExternalCollectionController::class, 'cancelPayment'])->name('external-collections.payments.cancel');
        Route::post('/{externalCollection}/fix-calculations', [ExternalCollectionController::class, 'fixPaymentCalculations'])->name('external-collections.fix-calculations');
    });

    // Cache Management Routes (محمية للمسؤولين)
    Route::prefix('cache')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/clear-all', [CacheController::class, 'clearAll'])->name('cache.clear.all');
        Route::get('/clear-employees', [CacheController::class, 'clearEmployees'])->name('cache.clear.employees');
        Route::get('/clear-requests', [CacheController::class, 'clearRequests'])->name('cache.clear.requests');
        Route::get('/refresh-stats', [CacheController::class, 'refreshStats'])->name('cache.refresh.stats');
        Route::get('/info', [CacheController::class, 'info'])->name('cache.info');
    });

    // Security Management Routes (محمية للمسؤولين)
    Route::prefix('security')->middleware(['auth'])->group(function () {
        Route::get('/check-password-strength', [SecurityController::class, 'checkPasswordStrength'])->name('security.password.strength');
        Route::get('/check-suspicious', [SecurityController::class, 'checkSuspiciousActivity'])->name('security.suspicious.check');
        Route::post('/sanitize-input', [SecurityController::class, 'sanitizeInput'])->name('security.sanitize');
        Route::post('/validate-file', [SecurityController::class, 'validateFile'])->name('security.file.validate');
        Route::get('/generate-token', [SecurityController::class, 'generateToken'])->name('security.token.generate');
        Route::post('/verify-token', [SecurityController::class, 'verifyToken'])->name('security.token.verify');
        Route::post('/log-event', [SecurityController::class, 'logSecurityEvent'])->name('security.log.event');
        Route::get('/session-info', [SecurityController::class, 'getSessionInfo'])->name('security.session.info');
        Route::post('/logout-all', [SecurityController::class, 'logoutAllDevices'])->name('security.logout.all');
        Route::get('/check-2fa', [SecurityController::class, 'check2FA'])->name('security.2fa.check');
        Route::post('/change-password', [SecurityController::class, 'changePassword'])->name('security.password.change');
    });

    // Historical crafts viewer (one-time import results)
    Route::get('/historical-crafts', [\App\Http\Controllers\HistoricalCraftController::class, 'index'])->name('historical_crafts.index');

    // Unified historical files query for crafts and legacy engineering/organization data
    Route::get('/historical-files', [\App\Http\Controllers\HistoricalFilesController::class, 'index'])->name('historical-files.index');
    Route::get('/historical-files/export', [\App\Http\Controllers\HistoricalFilesController::class, 'export'])->name('historical-files.export');

});
