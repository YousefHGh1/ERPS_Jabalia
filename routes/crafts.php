<?php

use App\Http\Controllers\Licensing\CraftLicenseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', \App\Http\Middleware\EagerLoadAuthUser::class])->group(function () {
    Route::get('/craft_licenses_export', [CraftLicenseController::class, 'export'])->name('craft_licenses.export');
    Route::resource('craft_licenses', CraftLicenseController::class);
    Route::get('/craft_licenses/{craft_license}/print', [CraftLicenseController::class, 'print'])->name('craft_licenses.print');
    Route::get('/craft_licenses/{craft_license}/download-attachment/{attachment}', [CraftLicenseController::class, 'downloadAttachment'])->name('craft_licenses.download-attachment');
    Route::post('/craft_licenses/{craft_license}/add_payment', [CraftLicenseController::class, 'addPayment'])->name('craft_licenses.add_payment');
    Route::post('/craft_licenses/payments/{payment}/cancel', [CraftLicenseController::class, 'cancelPayment'])->name('craft_licenses.cancel_payment');
    Route::delete('/craft_licenses/{craft_license}/delete-attachment/{attachment}', [CraftLicenseController::class, 'deleteAttachment'])->name('craft_licenses.delete-attachment');
    Route::post('/craft_licenses/{craft_license}/add_partner', [CraftLicenseController::class, 'addPartner'])->name('craft_licenses.add_partner');
    Route::post('/craft_licenses/{craft_license}/partners/{partner}/delete', [CraftLicenseController::class, 'deletePartner'])->name('craft_licenses.delete_partner');
    Route::post('/craft_licenses/{craft_license}/renew', [CraftLicenseController::class, 'renew'])->name('craft_licenses.renew');
    Route::post('/craft_licenses/search', [CraftLicenseController::class, 'searchByFileNumber'])->name('craft_licenses.search');
    Route::post('/craft_licenses/accrue-annual-fees', [CraftLicenseController::class, 'accrueAnnualFees'])
        ->name('craft_licenses.accrue_annual_fees');
});

