<?php

namespace App\Http\Controllers\Owner;

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
        return view('owner.users.index');
    }

    /**
     * Ambil data untuk DataTables (Disertai Filter Kategori Dropdown).
     */
    /**
     * Ambil data untuk DataTables.
     */
   public function data(Request $request)
    {
        $users = User::query();

        // 1. Filter Dropdown Kategori Akun
        if ($request->filled('category')) {
            if ($request->category == 'karyawan') {
                $users->whereIn('role', ['ADMIN', 'KRU']);
            } elseif ($request->category == 'pelanggan') {
                $users->where('role', 'PELANGGAN');
            }
        }

        // Ambil data selain Owner dan urutkan A-Z berdasarkan nama
        $users->where('role', '!=', 'OWNER')->orderBy('name', 'asc');

        return DataTables::of($users)
            ->addIndexColumn()
            
            // Kolom Role (Bawaan database, otomatis bisa dicari)
            ->editColumn('role', function ($user) {
                return strtoupper($user->role);
            })
            
            // Kolom Jabatan (Kolom kustom)
            ->addColumn('jabatan', function ($user) {
                return $user->jabatan ? strtoupper($user->jabatan) : '-';
            })
            
            // KUNCI UTAMA: Logika agar kolom Jabatan yang kustom bisa dicari lewat input Search
            ->filterColumn('jabatan', function($query, $keyword) {
                $sql = "users.jabatan LIKE ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            
            ->addColumn('action', function ($user) {
                return '
                <div class="d-flex justify-content-center gap-2">
                    <a href="' . route('owner.users.edit', $user->id) . '" 
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
        return view('owner.users.create');
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'password' => 'required|min:8|confirmed',
            'role' => 'required',
            'jabatan' => 'required_if:role,kru', // Wajib isi jika role kru
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'jabatan' => $request->role == 'kru' ? $request->jabatan : null,
        ]);

        return redirect()->route('owner.users.index')->with('swal_success', 'User berhasil ditambahkan!');
    }


    /**
     * Tampilkan form edit user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('owner.users.edit', compact('user'));
    }

    /**
     * Update data user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
            'jabatan' => 'required_if:role,kru',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'jabatan' => $request->role == 'kru' ? $request->jabatan : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('owner.users.index')->with('swal_success', 'User berhasil diperbarui!');
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
