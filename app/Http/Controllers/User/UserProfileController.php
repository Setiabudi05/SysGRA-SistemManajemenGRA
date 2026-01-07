<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // <-- PENTING: Import Model User

class UserProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna yang sedang login.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Menggunakan view yang diasumsikan: resources/views/user/profile/index.blade.php
        return view('user.profile.index', compact('user'));
    }

    /**
     * Menyimpan pembaruan data profil pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Memastikan email unik, kecuali untuk dirinya sendiri
            'email' => 'required|email|unique:users,email,' . Auth::id(), 
            // Tambahkan validasi lain jika diperlukan
        ]);

        /** @var \App\Models\User $user */ // <-- PERBAIKAN: Type Hinting untuk menghilangkan warning 'Undefined method save'
        $user = Auth::user();
        
        $user->name = $request->name;
        $user->email = $request->email;
        // $user->phone = $request->phone; // contoh
        
        $user->save(); // <-- Metode save() sekarang dikenali oleh IDE

        return redirect()->route('profile')->with('success_message', 'Profil berhasil diperbarui!');
    }
}