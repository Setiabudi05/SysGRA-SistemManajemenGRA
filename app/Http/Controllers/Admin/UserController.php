<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Tampilkan halaman daftar user.
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Ambil data untuk DataTables.
     */
    public function data()
    {
        $users = User::select(['id', 'name', 'email', 'role']);

        return DataTables::of($users)
            ->addIndexColumn()
            ->editColumn('role', function ($user) {
                // Merubah teks menjadi kapital sesuai permintaan agar seragam dengan tabel user lainnya
                return strtoupper($user->role);
            })
            ->addColumn('action', function ($user) {
                return '
                <div class="d-flex justify-content-center gap-2">
                    <a href="' . route('admin.users.edit', $user->id) . '" 
                       class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                       <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <button onclick="hapusUser(' . $user->id . ')" 
                            class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Tampilkan form tambah user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,user'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')
            ->with('swal_success', 'User berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update data user.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'role' => ['required', 'in:admin,user'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Proses update password jika diisi
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('swal_success', 'Data user berhasil diperbarui.');
    }

    /**
     * Hapus user.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        // Proteksi agar tidak menghapus diri sendiri
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Aksi ditolak! Anda tidak diperbolehkan menghapus akun yang sedang digunakan.'
            ], 403);
        }

        try {
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data user telah berhasil dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}