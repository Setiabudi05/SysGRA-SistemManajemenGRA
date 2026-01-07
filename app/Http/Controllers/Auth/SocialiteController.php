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
            $socialUser = Socialite::driver('google')->user(); 
        } catch (\Exception $e) {
            Log::error("Google Socialite Error (SYSGRA): " . $e->getMessage());
            return redirect()->route('login')->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        // Cari user berdasarkan email
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Jika user sudah ada, langsung login
            Auth::login($user, true);
        } else {
            // Jika user belum ada (pendaftaran baru via Google)
            $user = User::create([
                'name'              => $socialUser->getName() ?? $socialUser->getNickname() ?? explode('@', $socialUser->getEmail())[0],
                'email'             => $socialUser->getEmail(),
                'password'          => bcrypt(Str::random(16)), // Password acak aman
                'role'              => 'user',                  // Default role di SYSGRA
                'email_verified_at' => now(),                   // Langsung diverifikasi
            ]);
            Auth::login($user, true);
        }

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