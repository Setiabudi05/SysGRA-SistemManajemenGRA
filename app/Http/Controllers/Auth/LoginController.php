<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    */

    use AuthenticatesUsers;

    /**
     * Tentukan redirect setelah login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $role = Auth::user()->role;

        if ($role === 'admin') {
            return '/admin/dashboard';
        } elseif ($role === 'owner') {
            return '/owner/dashboard';
        } elseif ($role === 'kru') {
            return '/kru/dashboard';
        } else {
            // Pelanggan diarahkan ke dashboard user
            return '/user/dashboard';
        }
    }

    /**
     * Kirim response setelah user berhasil login.
     * * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        // Tentukan path redirect
        $redirectPath = $this->redirectPath();

        // Dapatkan role user yang baru saja login
        $role = Auth::user()->role;

        // Inisialisasi kunci flash message
        $flashKey = '';

        if ($role === 'admin') {
            // **ADMIN:** Gunakan kunci yang dideteksi oleh paket SweetAlert (misalnya: 'toast_success')
            $flashKey = 'toast_success';
        } else {
            // **USER BIASA:** Gunakan kunci yang dideteksi oleh script manual Anda (yaitu: 'success_message')
            $flashKey = 'success_message';
        }

        // Redirect dengan flash message yang sesuai dengan layout masing-masing
        return redirect()->intended($redirectPath)
            ->with($flashKey, 'berhasil login');
    }

    /**
     * Konstruktor controller.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override fungsi logout untuk arahkan ke halaman login.
     */
    protected function loggedOut(Request $request)
    {
        // Arahkan ke halaman login setelah logout
        return redirect()->route('login');
    }
}
