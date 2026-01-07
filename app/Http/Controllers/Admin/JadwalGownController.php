<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalGown;
use App\Models\JadwalPengantin;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalGownController extends Controller
{
    public function index()
    {
        return view('admin.jadwalgown.index');
    }

    public function data(Request $request)
    {
        $query = JadwalGown::with(['jadwalPengantin.paket'])
            ->whereHas('jadwalPengantin');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($row) {
                $awal = $row->jadwalPengantin->tanggal_awal ? Carbon::parse($row->jadwalPengantin->tanggal_awal)->format('d') : '-';
                $akhir = $row->jadwalPengantin->tanggal_akhir ? Carbon::parse($row->jadwalPengantin->tanggal_akhir)->format('d') : null;
                return $akhir ? "$awal-$akhir" : $awal;
            })
            ->addColumn('nama', fn($row) => $row->jadwalPengantin->nama ?? '-')
            ->addColumn('alamat', fn($row) => $row->jadwalPengantin->alamat ?? '-')
            ->addColumn('paket', fn($row) => $row->jadwalPengantin->paket->nama_paket ?? '-')
            ->addColumn('action', function ($row) use ($request) {
                // Menambahkan parameter filter aktif ke URL Edit
                $params = [
                    'id' => $row->id,
                    'f_bulan' => $request->bulan,
                    'f_tahun' => $request->tahun
                ];

                return '
                <div class="d-flex justify-content-center gap-2">
                    <a href="' . route('admin.jadwalgown.edit', $params) . '" 
                       class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                       <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <button onclick="hapusJadwal(' . $row->id . ')" 
                            class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        // Mengambil semua data untuk dropdown (ASC agar urut dari tanggal terdekat)
        $jadwals = JadwalPengantin::orderBy('tanggal_awal', 'asc')->get();
        return view('admin.jadwalgown.create', compact('jadwals'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $jadwalPengantin = JadwalPengantin::findOrFail($request->jadwal_pengantin_id);

        if (JadwalGown::where('jadwal_pengantin_id', $jadwalPengantin->id)->exists()) {
            return back()->withErrors(['error' => 'Jadwal ini sudah memiliki data Gown.']);
        }

        $sync = $this->syncWithJadwal($jadwalPengantin);
        $data = array_merge($validated, $sync);
        JadwalGown::create($data);

        // Redirect ke filter sesuai bulan dan tahun data yang baru dibuat
        return redirect()->route('admin.jadwalgown.index', [
            'bulan' => $sync['bulan'],
            'tahun' => $sync['tahun']
        ])->with('swal_success', 'Jadwal berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $jadwalGown = JadwalGown::findOrFail($id);
        $jadwals = JadwalPengantin::orderBy('tanggal_awal', 'asc')->get();
        return view('admin.jadwalgown.edit', compact('jadwalGown', 'jadwals'));
    }

    public function update(Request $request, $id)
    {
        $jadwalGown = JadwalGown::findOrFail($id);
        $validated = $this->validateRequest($request);
        $jadwalPengantin = JadwalPengantin::findOrFail($request->jadwal_pengantin_id);

        $data = array_merge($validated, $this->syncWithJadwal($jadwalPengantin));
        $jadwalGown->update($data);

        // Redirect menggunakan parameter 'last_filter' dari hidden input
        return redirect()->route('admin.jadwalgown.index', [
            'bulan' => $request->last_bulan,
            'tahun' => $request->last_tahun
        ])->with('swal_success', 'Jadwal berhasil diperbarui!');
    }

    /**
     * Hapus data gown.
     */
    public function destroy($id)
    {
        try {
            $jadwal = JadwalGown::findOrFail($id);
            $jadwal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal penggunaan busana/gown berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Cetak jadwal gown ke PDF.
     */
    public function print(Request $request)
    {
        $query = JadwalGown::with(['jadwalPengantin.paket'])->whereHas('jadwalPengantin');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $jadwal = $query->get();
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        return Pdf::loadView('admin.jadwalgown.print', compact('jadwal', 'bulan', 'tahun'))
            ->setPaper('A4', 'portrait')
            ->stream('jadwal-gown.pdf');
    }

    /**
     * Helper: Validasi Request.
     */
   private function validateRequest(Request $request)
    {
        return $request->validate([
            'jadwal_pengantin_id' => 'required|exists:jadwal_pengantins,id',
            'gown' => 'required|string|max:255',
        ]);
    }

    private function syncWithJadwal($jadwalPengantin)
    {
        $date = Carbon::parse($jadwalPengantin->tanggal_awal);
        return [
            'bulan' => $date->locale('id')->translatedFormat('F'),
            'tahun' => $date->year
        ];
    }
}