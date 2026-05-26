<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Ambil user yang login
        $user = Auth::user();

        // Logika Redirect berdasarkan Role
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'))
                ->with('swal_success', 'Selamat Datang Admin!');
        }

        // Jika user biasa, arahkan ke dashboard user
        return redirect()->intended(route('user.dashboard'))
            ->with('swal_success', 'Selamat Datang! Anda berhasil login ke sistem SysGRA.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Anda telah berhasil keluar.');
    }
}
