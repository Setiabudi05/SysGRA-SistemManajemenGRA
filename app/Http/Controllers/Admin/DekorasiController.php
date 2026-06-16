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
        $pakets = Paket::all(); // Mengambil seluruh paket untuk isi dropdown filter
        return view('admin.dekorasi.index', compact('pakets'));
    }

    /**
     * Ambil data untuk DataTables
     */
    /**
     * Ambil data untuk DataTables dengan penanganan filter paket dan pencarian nama paket
     */
    public function data(Request $request)
    {
        // Gunakan eager loading 'paket'
        $query = Dekorasi::with('paket');

        // Logika filter dropdown paket
        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }

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
            ->rawColumns(['action'])
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

    /**
     * Cetak katalog visual data dekorasi pernikahan (PDF)
     */
    /**
     * Cetak katalog visual data dekorasi pernikahan berdasarkan filter indeks
     */
    /**
     * Cetak katalog visual data dekorasi pernikahan berdasarkan urutan dekorasi terbanyak
     */
    public function print(Request $request)
    {
        // 1. Ambil paket, hitung jumlah dekorasinya, dan urutkan dari yang terbanyak (descending)
        $queryPaket = Paket::withCount('dekorasis') // Menghitung otomatis jumlah dekorasi per paket
            ->with(['dekorasis' => function ($query) {
                $query->latest();
            }])
            ->orderBy('dekorasis_count', 'desc'); // KUNCI UTAMA: Urutkan dari dekorasi terbanyak!

        // 2. Sinkronisasi filter: Jika indeks depan disaring per paket, cetak paket yang dipilih saja
        if ($request->filled('paket_id')) {
            $queryPaket->where('id', $request->paket_id);
        }

        $dataPaket = $queryPaket->get();

        // 3. Generate lembar PDF DomPDF murni
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.dekorasi.print', [
            'dataPaket' => $dataPaket
        ])
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true
            ]);

        return $pdf->stream('katalog_dekorasi_wedding.pdf');
    }
}
