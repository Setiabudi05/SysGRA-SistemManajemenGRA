<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        // Mengambil data user tunggal yang sedang login
        $user = User::find(Auth::id());
        return view('user.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        
        // Validasi form (sesuai nama input html)
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'nullable|numeric', // Menyesuaikan input phone
            'address'  => 'nullable|string',  // Menyesuaikan input address
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Masukkan data ke kolom database asli kamu
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;     // Sesuai kolom database: phone
        $user->address = $request->address; // Sesuai kolom database: address

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}