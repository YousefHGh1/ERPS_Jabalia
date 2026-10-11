<?php

use App\Http\Controllers\Requests\RequestController;
use App\Http\Controllers\Requests\RequestMailController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', \App\Http\Middleware\EagerLoadAuthUser::class])->group(function () {
    // Requests Routes - AJAX routes must come BEFORE resource route
    Route::get('/requests/autocomplete/identity', [RequestController::class, 'autocompleteIdentity'])->name('requests.autocomplete-identity');
    Route::get('/requests/autocomplete/name', [RequestController::class, 'autocompleteName'])->name('requests.autocomplete-name');
    Route::get('/requests/autocomplete/mokalaf', [RequestController::class, 'autocompleteMokalaf'])->name('requests.autocomplete-mokalaf');
    Route::get('/requests/get-mokalaf-by-identity', [RequestController::class, 'getMokalafByIdentity'])->name('requests.get-mokalaf-by-identity');
    Route::get('/requests/get-mokalaf-data', [RequestController::class, 'getMokalafData'])->name('requests.get-mokalaf-data');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');

    // Requests Resource Routes
    Route::resource('/requests', RequestController::class)->except(['create']);
    Route::get('/requests/{request}/print', [RequestController::class, 'print'])->name('requests.print');
    Route::post('/requests/{request}/add-payment', [RequestController::class, 'addPayment'])->name('requests.add-payment');
    Route::post('/request-payments/{payment}/cancel', [RequestController::class, 'cancelRequestPayment'])->name('requests.cancel-payment');

    Route::put('/requests/{request}/status', [RequestController::class, 'updateStatus'])->name('requests.update-status');
    Route::get('/requests/{request}/download-attachment/{attachment}', [RequestController::class, 'downloadAttachment'])->name('requests.download-attachment');
    Route::delete('/requests/{request}/delete-attachment/{attachment}', [RequestController::class, 'deleteAttachment'])->name('requests.delete-attachment');

    // Request Types Routes
    Route::get('/request-types', [\App\Http\Controllers\Requests\RequestTypeController::class, 'index'])->name('request-types.index');
    Route::get('/request-types/data', [\App\Http\Controllers\Requests\RequestTypeController::class, 'getRequestTypeData'])->name('request-types.data');
    Route::post('/request-types', [\App\Http\Controllers\Requests\RequestTypeController::class, 'store'])->name('request-types.store');
    Route::get('/request-types/{id}/edit', [\App\Http\Controllers\Requests\RequestTypeController::class, 'edit'])->name('request-types.edit');
    Route::put('/request-types/{id}', [\App\Http\Controllers\Requests\RequestTypeController::class, 'update'])->name('request-types.update');
    Route::delete('/request-types/{id}', [\App\Http\Controllers\Requests\RequestTypeController::class, 'destroy'])->name('request-types.destroy');

    // Request Workflows Routes
    Route::get('/request-workflows/{requestTypeId}', [\App\Http\Controllers\Requests\RequestWorkflowController::class, 'index'])->name('request-workflows.index');
    Route::get('/request-workflows/{requestTypeId}/create', [\App\Http\Controllers\Requests\RequestWorkflowController::class, 'create'])->name('request-workflows.create');
    Route::post('/request-workflows', [\App\Http\Controllers\Requests\RequestWorkflowController::class, 'store'])->name('request-workflows.store');
    Route::get('/request-workflows/{id}/edit', [\App\Http\Controllers\Requests\RequestWorkflowController::class, 'edit'])->name('request-workflows.edit');
    Route::put('/request-workflows/{id}', [\App\Http\Controllers\Requests\RequestWorkflowController::class, 'update'])->name('request-workflows.update');
    Route::delete('/request-workflows/{id}', [\App\Http\Controllers\Requests\RequestWorkflowController::class, 'destroy'])->name('request-workflows.destroy');

    // تشخيص بريد الطلبات (للمطورين فقط - غير متاح في الإنتاج)
    if (!app()->environment('production')) {
        Route::get('/request-mail-debug', [RequestMailController::class, 'debug'])->name('requests.mail.debug');
    }

    // Request Mail Routes (بريد الطلبات)
    Route::prefix('requests-mail')->name('requests.mail.')->group(function () {
        Route::get('/waiting', [RequestMailController::class, 'waiting'])->name('waiting');
        Route::get('/accepted', [RequestMailController::class, 'accepted'])->name('accepted');
        Route::get('/pending', [RequestMailController::class, 'pending'])->name('pending');
        Route::get('/unaccepted', [RequestMailController::class, 'unaccepted'])->name('unaccepted');
        Route::get('/all', [RequestMailController::class, 'all'])->name('all');
        Route::get('/data', [RequestMailController::class, 'data'])->name('data');
        Route::get('/{requestId}/{responseId}', [RequestMailController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [RequestMailController::class, 'edit'])->name('edit');
        Route::post('/store', [RequestMailController::class, 'store'])->name('store');
        Route::put('/{id}', [RequestMailController::class, 'update'])->name('update');
    });
});