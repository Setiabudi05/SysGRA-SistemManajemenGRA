<?php

// use App\Http\Controllers\Auth\AuthenticatedSessionController; // <-- 1. INI KITA MATIKAN
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\LoginController; // <-- 2. KITA TAMBAHKAN INI

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    // Route Register (Sudah benar dari perbaikan sebelumnya)
    Route::get('register', function () {
        return view('auth.register');
    })->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    // Route Login
    Route::get('login', function () {
        return view('auth.login');
    })->name('login');

    // Route::post('login', [AuthenticatedSessionController::class, 'store']); // <-- 3. INI SALAH
    Route::post('login', [LoginController::class, 'login']); // <-- 4. GANTI DENGAN INI

    // Route Forgot Password
    Route::get('forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Route Reset Password
    Route::get('reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['request' => request()]);
    })->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // --- Route Verifikasi Email (Koreksi) ---
    Route::get('verify-email', function () {
        return view('auth.verify-email'); // Menampilkan blade cantik yang baru dibuat
    })->name('verification.notice');


    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // --- Route Confirm Password ---
    Route::get('confirm-password', function () {
        return view('auth.confirm-password'); // <- Baru
    })->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // --- Sisa Route ---
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Route::post('logout', [AuthenticatedSessionController::class, 'destroy']) // <-- 5. INI SALAH
    //             ->name('logout');
    Route::post('logout', [LoginController::class, 'logout']) // <-- 6. GANTI DENGAN INI
        ->name('logout');
});
