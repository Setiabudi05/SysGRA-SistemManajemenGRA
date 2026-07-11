<?php

namespace App\Http\Controllers\Admin;

use App\Models\BajuPengantin;
use App\Models\Paket;
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
    /**
     * Ambil data untuk DataTables
     */
    public function data()
    {
        $baju = BajuPengantin::latest()->get();
        return DataTables::of($baju)
            ->addIndexColumn()
            ->addColumn('foto', function ($row) {
                return $row->foto_gown
                    ? '<div class="img-container shadow-sm"><img src="' . asset('storage/' . $row->foto_gown) . '" alt="Foto"></div>'
                    : '<span class="text-muted small">No Image</span>';
            })
            ->editColumn('paket', function ($row) {
                return '<span class="fw-bold text-dark">' . $row->paket . '</span>';
            })
            ->addColumn('deskripsi', function ($row) {
                return $row->deskripsi_gown ?: '-';
            })
            ->addColumn('action', function ($row) {
                return '
                <div class="d-flex justify-content-center align-items-center gap-2">
                    <a href="' . route('admin.baju.edit', $row->id) . '" class="btn btn-warning btn-sm fw-bold shadow-sm py-1 px-2">Edit</a>
                    <button type="button" class="btn btn-danger btn-sm fw-bold shadow-sm py-1 px-2 hapus-btn" data-id="' . $row->id . '">Hapus</button>
                </div>';
            })
            ->rawColumns(['foto', 'paket', 'action'])
            ->make(true);
    }
    /**
     * Tampilkan form tambah baju
     */
    public function create()
    {
        $pakets = Paket::all();
        return view('admin.baju.create', compact('pakets'));
    }

    /**
     * Simpan baju baru
     */
    /**
     * Simpan koleksi baju pengantin baru dengan sistem input teks paket manual
     */
    public function store(Request $request)
    {
        $request->validate([
            'paket'          => 'required|string|max:255',
            'nama_gown'      => 'required|string|max:255',
            'deskripsi_gown' => 'nullable|string',
            'foto_gown'      => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_gown')) {
            $fotoPath = $request->file('foto_gown')->store('baju_pengantin', 'public');
        }

        BajuPengantin::create([
            'paket'          => $request->paket,
            'nama_gown'      => $request->nama_gown,
            'deskripsi_gown' => $request->deskripsi_gown,
            'foto_gown'      => $fotoPath,
        ]);

        return redirect()->route('admin.baju.index')->with('success', 'Baju pengantin berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit baju
     */
    public function edit($id)
    {
        $baju = BajuPengantin::findOrFail($id);
        $pakets = Paket::all();
        return view('admin.baju.edit', compact('baju', 'pakets'));
    }

    /**
     * Update data baju
     */

    public function update(Request $request, $id)
    {
        $baju = BajuPengantin::findOrFail($id);

        $request->validate([
            'paket'          => 'required|string|max:255',
            'nama_gown'      => 'required|string|max:255',
            'deskripsi_gown' => 'nullable|string',
            'foto_gown'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = [
            'paket'          => $request->paket,
            'nama_gown'      => $request->nama_gown,
            'deskripsi_gown' => $request->deskripsi_gown,
        ];

        if ($request->hasFile('foto_gown')) {
            if ($baju->foto_gown && Storage::disk('public')->exists($baju->foto_gown)) {
                Storage::disk('public')->delete($baju->foto_gown);
            }
            $data['foto_gown'] = $request->file('foto_gown')->store('baju_pengantin', 'public');
        }

        $baju->update($data);
        return redirect()->route('admin.baju.index')->with('success', 'Koleksi baju berhasil diperbarui.');
    }

    /**
     * Hapus data baju
     */
    public function destroy($id)
    {
        try {
            $baju = BajuPengantin::findOrFail($id);

            if ($baju->foto_gown && Storage::disk('public')->exists($baju->foto_gown)) {
                Storage::disk('public')->delete($baju->foto_gown);
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
    /**
     * Cetak laporan data baju pengantin (Menggunakan Grouping String Paket murni)
     */
    public function print(Request $request)
    {
        // AMAN: Kita ambil data baju pengantin murni tanpa memanggil relasi Paket yang memakai paket_id
        $queryBaju = BajuPengantin::latest();

        // Jika di indeks depan ada filter paket tertentu, kita saring berdasarkan teks string paketnya
        if ($request->filled('paket')) {
            $queryBaju->where('paket', $request->paket);
        }

        $allBaju = $queryBaju->get();

        // KUNCI SOLUSI: Kita kelompokkan data baju berdasarkan kolom string 'paket' secara otomatis via Laravel Collection
        $dataPaket = $allBaju->groupBy('paket');

        // Kirim hasil grouping ke view print
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.baju.print', [
            'dataPaket' => $dataPaket
        ])
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true
            ]);

        return $pdf->stream('laporan_baju_pengantin.pdf');
    }
}
