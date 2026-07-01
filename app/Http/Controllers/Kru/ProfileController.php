<?php

namespace App\Http\Controllers\Kru;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JadwalPengantin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil khusus Kru
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $namaKru = $user->name;
        $today = Carbon::today()->toDateString();

        // Hitung TOTAL SEMUA riwayat job (Lifetime)
        // Logika: Tanggal acara sudah lebih kecil dari hari ini
        $tugasSelesai = JadwalPengantin::where(function ($q) use ($namaKru) {
                $q->where('fg', $namaKru)
                  ->orWhere('asisten', 'like', '%' . $namaKru . '%')
                  ->orWhere('layos', $namaKru);
            })
            ->whereDate('tanggal_awal', '<', $today)
            ->count();

        return view('kru.profile.index', compact('user', 'tugasSelesai'));
    }

    /**
     * Memproses update profil Kru
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user(); 
        
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('swal_success', 'Profil Kru Anda berhasil diperbarui!');
    }
}