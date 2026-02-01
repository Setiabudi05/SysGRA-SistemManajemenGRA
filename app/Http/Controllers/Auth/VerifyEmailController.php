<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Menandai email pengguna sebagai terverifikasi.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // 1. Jika user sudah verifikasi, arahkan ke halaman utama/login dengan status
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('login')->with('verified', true);
        }

        // 2. Tandai email sebagai terverifikasi di database
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // 3. Arahkan ke halaman login agar user bisa masuk dengan status 'verified'
        // Ini akan memicu alert sukses yang sudah kita pasang di login.blade.php
        return redirect()->route('login')->with('verified', true);
    }
}