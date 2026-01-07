<?php

namespace App\Http\Controllers\Admin;

use App\Models\Paket;
use App\Models\Dekorasi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class DekorasiController extends Controller
{
    /**
     * Tampilkan halaman daftar dekorasi
     */
    public function index()
    {
        return view('admin.dekorasi.index');
    }

    /**
     * Ambil data untuk DataTables
     */
    public function data()
    {
        // Mengambil relasi paket untuk menghindari N+1 query
        $query = Dekorasi::with('paket');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('paket', function ($row) {
                return $row->paket ? $row->paket->nama_paket : '-';
            })
            ->addColumn('action', function ($row) {
                return '
                <div class="d-flex justify-content-center gap-2">
                    <a href="' . route('admin.dekorasi.edit', $row->id) . '" 
                       class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                       <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <button onclick="hapusDekorasi(' . $row->id . ')" 
                            class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>';
            })
            ->rawColumns(['action', 'foto'])
            ->make(true);
    }

    /**
     * Tampilkan form tambah dekorasi
     */
    public function create()
    {
        $pakets = Paket::all();
        return view('admin.dekorasi.create', compact('pakets'));
    }

    /**
     * Simpan dekorasi baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'paket_id'  => 'required|exists:pakets,id',
            'deskripsi' => 'required|string',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['paket_id', 'deskripsi']);

        // Proses upload foto jika ada
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('dekorasi', 'public');
        }

        Dekorasi::create($data);

        return redirect()->route('admin.dekorasi.index')
            ->with('swal_success', 'Dekorasi berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit dekorasi
     */
    public function edit($id)
    {
        $dekorasi = Dekorasi::findOrFail($id);
        $pakets   = Paket::all();
        return view('admin.dekorasi.edit', compact('dekorasi', 'pakets'));
    }

    /**
     * Update data dekorasi di database
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'paket_id'  => 'required|exists:pakets,id',
            'deskripsi' => 'required|string',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $dekorasi = Dekorasi::findOrFail($id);
        $data     = $request->only(['paket_id', 'deskripsi']);

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama dari storage jika ada file baru yang diunggah
            if ($dekorasi->foto && Storage::disk('public')->exists($dekorasi->foto)) {
                Storage::disk('public')->delete($dekorasi->foto);
            }
            $data['foto'] = $request->file('foto')->store('dekorasi', 'public');
        }

        $dekorasi->update($data);

        return redirect()->route('admin.dekorasi.index')
            ->with('swal_success', 'Dekorasi berhasil diperbarui!');
    }

    /**
     * Hapus data dekorasi dan filenya
     */
    public function destroy($id)
    {
        $dekorasi = Dekorasi::findOrFail($id);

        // Hapus file dari storage sebelum menghapus data di database
        if ($dekorasi->foto && Storage::disk('public')->exists($dekorasi->foto)) {
            Storage::disk('public')->delete($dekorasi->foto);
        }

        $dekorasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dekorasi berhasil dihapus.'
        ]);
    }
}