<?php

namespace App\Http\Controllers\Admin;

use App\Models\Baju;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BajuPengantinController extends Controller
{
    /**
     * Tampilkan halaman daftar baju pengantin
     */
    public function index()
    {
        return view('admin.baju.index');
    }

    /**
     * Ambil data untuk DataTables
     */
    public function data()
    {
        $query = Baju::query()->latest();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('foto', function ($row) {
                if ($row->foto) {
                    return '<div class="img-container shadow-sm">
                            <img src="' . asset('storage/' . $row->foto) . '" alt="Foto Baju">
                        </div>';
                }
                return '<div class="img-container bg-light d-flex align-items-center justify-content-center text-muted small">No Image</div>';
            })
            ->addColumn('info_baju', function ($row) {
                // Menampilkan Kategori, Warna, dan Ukuran
                return "<strong>$row->kategori</strong><br>
                    <span class='badge bg-info text-dark'>$row->warna</span> 
                    <span class='badge bg-secondary'>Size: $row->ukuran</span>";
            })
            ->addColumn('status', function ($row) {
                return $row->stok > 0
                    ? '<span class="badge bg-success">Tersedia (' . $row->stok . ')</span>'
                    : '<span class="badge bg-danger">Habis</span>';
            })
            ->addColumn('action', function ($row) {
                return '
            <div class="d-flex justify-content-center gap-2">
                <a href="' . route('admin.baju.edit', $row->id) . '" class="btn btn-warning btn-sm fw-bold shadow-sm">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <button onclick="hapusBaju(' . $row->id . ')" class="btn btn-danger btn-sm fw-bold shadow-sm">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>';
            })
            ->rawColumns(['foto', 'info_baju', 'status', 'action'])
            ->make(true);
    }

    /**
     * Tampilkan form tambah baju
     */
    public function create()
    {
        return view('admin.baju.create');
    }

    /**
     * Simpan baju baru
     */
    public function store(Request $request)
    {
        // Sesuaikan validasi dengan name yang ada di file Blade
        $request->validate([
            'kategori' => 'required|string',
            'warna' => 'required|string|max:255',
            'ukuran' => 'nullable|string',
            'stok' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            // Simpan foto ke folder storage/app/public/baju
            $data['foto'] = $request->file('foto')->store('baju', 'public');
        }

        // Pastikan model Baju memiliki $fillable kategori, warna, ukuran, stok, foto
        Baju::create($data);

        return redirect()->route('admin.baju.index')
            ->with('swal_success', 'Koleksi baju berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit baju
     */
    public function edit($id)
    {
        $baju = Baju::findOrFail($id);
        return view('admin.baju.edit', compact('baju'));
    }

    /**
     * Update data baju
     */
    public function update(Request $request, $id)
    {
        $baju = Baju::findOrFail($id);

        // 1. Validasi input sesuai dengan name="" yang ada di form
        $request->validate([
            'kategori' => 'required|string',
            'warna' => 'required|string|max:255',
            'ukuran' => 'nullable|string',
            'stok' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        // 2. Logika Foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($baju->foto && Storage::disk('public')->exists($baju->foto)) {
                Storage::disk('public')->delete($baju->foto);
            }
            // Simpan foto baru
            $data['foto'] = $request->file('foto')->store('baju', 'public');
        }

        // 3. Update data (Pastikan $fillable di Model Baju sudah lengkap)
        $baju->update($data);

        // 4. Redirect dengan Alert Timer
        return redirect()->route('admin.baju.index')
            ->with('swal_success', 'Koleksi baju berhasil diperbarui.');
    }

    /**
     * Hapus data baju
     */
    public function destroy($id)
    {
        try {
            $baju = Baju::findOrFail($id);

            // Hapus file fisik foto dari storage jika ada
            if ($baju->foto && Storage::disk('public')->exists($baju->foto)) {
                Storage::disk('public')->delete($baju->foto);
            }

            $baju->delete();

            return response()->json([
                'success' => true,
                'message' => 'Koleksi baju berhasil dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cetak laporan data baju
     */
    public function print()
    {
        $data = Baju::latest()->get();
        return view('admin.baju.print', compact('data'));
    }
}