<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SocialiteController extends Controller
{
    /**
     * Mengarahkan pengguna ke halaman autentikasi Google.
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Menangani callback dari Google setelah autentikasi.
     */
    public function handleProviderCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }

        // 1. Cari user berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // JIKA USER SUDAH ADA:
            // Login saja, JANGAN update name atau role agar data kru/admin tidak rusak
            Auth::login($user);
        } else {
            // JIKA USER BARU:
            // Baru buatkan akun dengan role pelanggan
            $user = User::create([
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password'  => bcrypt('password_acak_123'),
                'role'      => 'pelanggan',
            ]);
            Auth::login($user);
        }

        // 2. Redirect berdasarkan role
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Jika role-nya 'kru', arahkan ke dashboard kru (sesuaikan route-nya)
        if ($user->role === 'kru') {
            return redirect()->intended(route('kru.dashboard'));
        }

        // Pengalihan standar untuk pelanggan
        return redirect()->intended(route('home'));
    }
}
