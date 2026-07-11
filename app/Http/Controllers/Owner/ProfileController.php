<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User; // Pastikan Model User terimport

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil owner
     */
    public function index()
    {
        return view('owner.profile.index', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Memproses update data profil owner
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Update Data User
        $user->name = $request->name;
        $user->email = $request->email;

        // 3. Update Password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // 4. Simpan ke Database
        $user->save();

        // 5. Redirect dengan feedback
        return redirect()->route('owner.profile.index')
                         ->with('swal_success', 'Profil berhasil diperbarui!');
    }
}