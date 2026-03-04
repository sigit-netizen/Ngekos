<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('register');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('login');

Route::get('/verify-device/select', [\App\Http\Controllers\Auth\DeviceOtpController::class, 'showSelectChannel'])
    ->middleware('guest')
    ->name('otp.select');

Route::post('/verify-device/send', [\App\Http\Controllers\Auth\DeviceOtpController::class, 'sendOtp'])
    ->middleware('guest')
    ->name('otp.send');

Route::get('/verify-device', [\App\Http\Controllers\Auth\DeviceOtpController::class, 'show'])
    ->middleware('guest')
    ->name('otp.verify');

Route::post('/verify-device', [\App\Http\Controllers\Auth\DeviceOtpController::class, 'verify'])
    ->middleware('guest')
    ->name('otp.verify.post');

Route::post('/verify-device/resend', [\App\Http\Controllers\Auth\DeviceOtpController::class, 'resend'])
    ->middleware('guest')
    ->name('otp.resend');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
