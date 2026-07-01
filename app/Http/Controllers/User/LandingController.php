<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Paket; // Pastikan namespace Model Paket Anda sudah benar
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    public function index()
    {
        // 1. Logika Redirect
        // Jika user sudah login, arahkan langsung ke dashboard agar tidak melihat landing page
        if (Auth::check()) {
            return redirect()->route('user.dashboard');
        }

        // 2. Ambil Data Paket
        // Mengambil paket tahun 2026 dan diurutkan dari yang termurah (asc)
        $tahunSekarang = date('Y');
        $pakets = Paket::where('tahun', $tahunSekarang)
                       ->orderBy('harga', 'asc') 
                       ->get();

        // 3. Kirim ke View
        return view('landing.index', compact('pakets'));
    }
}