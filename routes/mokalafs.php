<?php

use App\Http\Controllers\MokalafController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', \App\Http\Middleware\EagerLoadAuthUser::class])->group(function () {
    // Mokalaf Routes — عرض المكلفين حسب نوع الطلب (قبل resource لتجنب التعارض مع {mokalaf})
    Route::get('/mokalafs/for-engineering', [MokalafController::class, 'indexForEngineering'])->name('mokalafs.for-engineering');
    Route::get('/mokalafs/for-crafts', [MokalafController::class, 'indexForCrafts'])->name('mokalafs.for-crafts');
    Route::resource('/mokalafs', MokalafController::class);
    Route::get('/mokalafs/check-exists', [MokalafController::class, 'checkMokalafExists'])->name('mokalafs.check-exists');
    Route::get('/mokalafs/get-citizen-data', [MokalafController::class, 'getCitizenData'])->name('mokalafs.get-citizen-data');
});

