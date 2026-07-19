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

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // User sudah ada, pastikan email_verified_at terisi
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
                $user->save();
            }
            Auth::login($user);
        } else {
            // User baru, buat dengan email_verified_at yang sudah terisi
            $user = User::create([
                'name'              => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'password'          => bcrypt(Str::random(16)), // Password acak yang kuat
                'role'              => 'pelanggan',
                'email_verified_at' => now(), 
            ]);
            Auth::login($user);
        }

        // Redirect setelah Auth::login()
        if ($user->role === 'admin') return redirect()->route('admin.dashboard');
        if ($user->role === 'kru') return redirect()->route('kru.dashboard');
        if ($user->role === 'owner') return redirect()->route('owner.dashboard');
        if ($user->role === 'pelanggan') return redirect()->route('user.dashboard');

        return redirect()->route('home');
    }
}
