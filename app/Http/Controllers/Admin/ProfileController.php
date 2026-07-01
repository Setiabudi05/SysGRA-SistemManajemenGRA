<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Pastikan model User di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil admin
     */
    public function index()
    {
        $user = Auth::user(); // Mengambil data user yang sedang login
        return view('admin.profile.index', compact('user'));
    }

    /**
     * Memproses update nama, email, dan password
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
            'password.min' => 'Password minimal harus 8 karakter.'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // Update password hanya jika kolom diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save(); // Method 'save' sekarang aman digunakan

        return back()->with('swal_success', 'Profil Anda berhasil diperbarui!');
    }
}