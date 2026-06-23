<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paket;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class PaketController extends Controller
{
    public function index()
    {
        return view('admin.paket.index');
    }
    public function data(Request $request)
    {
        $pakets = Paket::select(['id', 'nama_paket', 'tahun', 'makeup', 'dekorasi', 'dokumentasi', 'include', 'bonus', 'harga']);

        // Filter Tahun
        if ($request->has('tahun') && !empty($request->tahun)) {
            $pakets->where('tahun', $request->tahun);
        }

        // Filter Kategori (Normal, Promo, Expo)
        if ($request->has('kategori') && !empty($request->kategori)) {
            if ($request->kategori == 'Expo') {
                $pakets->where('nama_paket', 'like', '%Expo%');
            } elseif ($request->kategori == 'Promo') {
                // Filter paket yang namanya mengandung kata 'Promo'
                $pakets->where('nama_paket', 'like', '%Promo%');
            } elseif ($request->kategori == 'Normal') {
                // Filter paket yang TIDAK mengandung 'Expo' DAN TIDAK mengandung 'Promo'
                $pakets->where('nama_paket', 'not like', '%Expo%')
                    ->where('nama_paket', 'not like', '%Promo%');
            }
        }

        return DataTables::of($pakets)
            ->addIndexColumn()
            ->addColumn('action', function ($paket) {
                return '
            <div class="d-flex justify-content-center gap-2">
                <a href="' . route('admin.paket.edit', $paket->id) . '" class="btn btn-warning btn-sm px-2 py-1"><i class="bi bi-pencil-square"></i> Edit</a>
                <button onclick="hapus(' . $paket->id . ')" class="btn btn-danger btn-sm px-2 py-1"><i class="bi bi-trash"></i> Hapus</button>
            </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function create()
    {
        return view('admin.paket.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'tahun'      => 'required|numeric',
            'makeup'     => 'nullable|string',
            'dekorasi'   => 'nullable|string',
            'dokumentasi' => 'nullable|string',
            'include'    => 'nullable|string', // Tambahkan ini
            'bonus'      => 'nullable|string', // Tambahkan ini
            'harga'      => 'required|numeric|min:0',
        ]);

        Paket::create($validated);
        return redirect()->route('admin.paket.index')->with('swal_success', 'Paket berhasil ditambah!');
    }

    public function edit($id)
    {
        $paket = Paket::findOrFail($id);
        return view('admin.paket.edit', compact('paket'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi data termasuk kolom baru
        $validated = $request->validate([
            'nama_paket'  => 'required|string|max:255',
            'tahun'       => 'required|numeric',
            'makeup'      => 'nullable|string',
            'dekorasi'    => 'nullable|string',
            'dokumentasi' => 'nullable|string',
            'include'     => 'nullable|string', // Kolom baru
            'bonus'       => 'nullable|string', // Kolom baru
            'harga'       => 'required|numeric|min:0',
        ]);

        // 2. Cari data paket berdasarkan ID
        $paket = Paket::findOrFail($id);

        // 3. Update data
        $paket->update($validated);

        // 4. Redirect dengan pesan sukses
        return redirect()->route('admin.paket.index')->with('swal_success', 'Paket berhasil diperbarui!');
    }

    public function destroy($id)
    {
        try {
            $paket = Paket::findOrFail($id);
            $paket->delete();

            // Mengembalikan response sukses dalam format JSON
            return response()->json([
                'success' => true,
                'message' => 'Data paket pernikahan berhasil dihapus dari sistem.'
            ]);
        } catch (Exception $e) {
            // Mengembalikan response gagal dalam format JSON
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
