<?php

use App\Http\Controllers\Notices\NoticeController;
use App\Http\Controllers\ImprisonmentOrders\ImprisonmentOrderController;
use App\Http\Controllers\Reservations\ReservationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', \App\Http\Middleware\EagerLoadAuthUser::class])->group(function () {
    // Reservations (الحجوزات)
    Route::resource('/reservations', ReservationController::class);
    Route::get('/reservations/{reservation}/download-attachment/{attachment}', [ReservationController::class, 'downloadAttachment'])->name('reservations.download-attachment');
    Route::delete('/reservations/{reservation}/delete-attachment/{attachment}', [ReservationController::class, 'deleteAttachment'])->name('reservations.delete-attachment');

    // مسارات البحث لنموذج الإخطارات (قلم الجمهور) — قبل resource
    // Select2 search (صفحة الإخطارات نفسها)
    Route::get('/notices/ajax/fetch-id', [NoticeController::class, 'fetchId'])->name('notices.fetch_id');
    Route::get('/notices/ajax/fetch-name', [NoticeController::class, 'fetchName'])->name('notices.fetch_name');
    Route::get('/notices/ajax/fetch-jebaia', [NoticeController::class, 'fetchJebaia'])->name('notices.fetch_jebaia');

    // مسارات جلب بيانات المكلف من صفحة/جدول المكلفين لاستخدامها داخل نموذج الإخطارات
    Route::get('/ajaxUserDataNotice', [NoticeController::class, 'ajaxUserDataNotice'])->name('notices.ajax-user-data');
    Route::get('/ajaxNameDataNotice', [NoticeController::class, 'ajaxNameDataNotice'])->name('notices.ajax-name-data');
    Route::get('/ajaxJebaiaDataNotice', [NoticeController::class, 'ajaxJebaiaDataNotice'])->name('notices.ajax-jebaia-data');

    Route::resource('/notices', NoticeController::class);
    Route::get('/notices/{notice}/download-attachment/{attachment}', [NoticeController::class, 'downloadAttachment'])->name('notices.download-attachment');
    Route::delete('/notices/{notice}/delete-attachment/{attachment}', [NoticeController::class, 'deleteAttachment'])->name('notices.delete-attachment');

    // أوامر الحبس
    Route::resource('/imprisonment_orders', ImprisonmentOrderController::class);
    Route::get('/imprisonment_orders/{order}/download-attachment/{attachment}', [ImprisonmentOrderController::class, 'downloadAttachment'])->name('imprisonment_orders.download-attachment');
    Route::delete('/imprisonment_orders/{order}/delete-attachment/{attachment}', [ImprisonmentOrderController::class, 'deleteAttachment'])->name('imprisonment_orders.delete-attachment');
});

