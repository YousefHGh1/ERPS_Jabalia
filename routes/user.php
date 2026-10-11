<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ProfileController;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Routes for user profile and account management
|
*/

// Profile Routes
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    
    Route::get('/profile/activity', [ProfileController::class, 'getActivity'])->name('profile.activity');
    
    Route::post('/profile/notifications', [ProfileController::class, 'updateNotificationSettings'])->name('profile.notifications');
    
    Route::post('/profile/appearance', [ProfileController::class, 'updateAppearanceSettings'])->name('profile.appearance');
    
    Route::get('/profile/session', [ProfileController::class, 'getSessionInfo'])->name('profile.session');
    
});
