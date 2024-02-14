<?php

use App\Http\Controllers\AuthController;
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

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::post('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendEmailVerification'])
        ->middleware(['throttle:6,1', 'auth:sanctum'])->name('verification.send');

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout/all', [AuthController::class, 'logoutAll']);
    });
});
