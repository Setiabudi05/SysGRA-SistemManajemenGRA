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

        // Gunakan updateOrCreate agar jika user sudah pernah login, dia tidak terduplikasi
        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'      => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'password'  => bcrypt('password_acak_123'), // Wajib ada password meski login google
                // INI BAGIAN PENTINGNYA:
                'role'      => 'pelanggan',
            ]
        );

        // Login user ke sistem
        Auth::login($user);

        // Redirect berdasarkan role (sama dengan logika di routes)
        return redirect()->route('user.dashboard');


        /**
         * 🌟 PENYESUAIAN SYSGRA 🌟
         * Mengalihkan user berdasarkan role mereka setelah login Google
         */
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Pengalihan standar untuk user SYSGRA (Landing Page atau Dashboard User)
        // Jika Anda ingin ke dashboard user, gunakan: route('profile') atau route('home')
        return redirect()->intended(route('home'));
    }
}
