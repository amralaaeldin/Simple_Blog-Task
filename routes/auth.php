<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;


Route::controller(AuthController::class)->group(function () {
  Route::post('register', 'register')->middleware('guest')->name('register');
  Route::post('login', 'login')->middleware('guest')->name('login');
  Route::post('logout', 'logout')->middleware('auth')->name('logout');
});

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
  ->middleware('guest')
  ->name('password.email');
  
Route::post('/reset-password', [NewPasswordController::class, 'store'])
  ->middleware('guest')
  ->name('password.store');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
  ->middleware(['auth:sanctum', 'throttle:6,1'])
  ->name('verification.send');

Route::post('/verify-email', VerifyEmailController::class)
  ->middleware(['auth:sanctum', 'throttle:6,1'])
  ->name('verification.verify');
