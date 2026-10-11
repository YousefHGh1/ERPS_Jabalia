<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/citizen', [App\Http\Controllers\CitizenController::class, 'getCitizen'])
    ->middleware('citizen.api.key')
    ->middleware('throttle:100,1');

// Cache management routes with strict rate limiting
Route::prefix('cache')->middleware(['auth', 'api.strict'])->group(function () {
    Route::get('/clear-all', [\App\Http\Controllers\Api\CacheController::class, 'clearAll']);
    Route::get('/clear-employees', [\App\Http\Controllers\Api\CacheController::class, 'clearEmployees']);
    Route::get('/clear-requests', [\App\Http\Controllers\Api\CacheController::class, 'clearRequests']);
    Route::get('/refresh-stats', [\App\Http\Controllers\Api\CacheController::class, 'refreshStats']);
    Route::get('/info', [\App\Http\Controllers\Api\CacheController::class, 'info']);
});

// Security routes with very strict rate limiting
Route::prefix('security')->middleware(['auth', 'api.very_strict'])->group(function () {
    Route::get('/check-password-strength', [\App\Http\Controllers\Api\SecurityController::class, 'checkPasswordStrength']);
    Route::get('/check-suspicious', [\App\Http\Controllers\Api\SecurityController::class, 'checkSuspiciousActivity']);
    Route::post('/sanitize-input', [\App\Http\Controllers\Api\SecurityController::class, 'sanitizeInput']);
    Route::post('/validate-file', [\App\Http\Controllers\Api\SecurityController::class, 'validateFile']);
    Route::get('/generate-token', [\App\Http\Controllers\Api\SecurityController::class, 'generateToken']);
    Route::post('/verify-token', [\App\Http\Controllers\Api\SecurityController::class, 'verifyToken']);
    Route::post('/log-event', [\App\Http\Controllers\Api\SecurityController::class, 'logSecurityEvent']);
    Route::get('/session-info', [\App\Http\Controllers\Api\SecurityController::class, 'getSessionInfo']);
    Route::post('/logout-all', [\App\Http\Controllers\Api\SecurityController::class, 'logoutAllDevices']);
    Route::get('/check-2fa', [\App\Http\Controllers\Api\SecurityController::class, 'check2FA']);
    Route::post('/change-password', [\App\Http\Controllers\Api\SecurityController::class, 'changePassword']);
});
