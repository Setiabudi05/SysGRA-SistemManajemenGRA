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
        // Mengambil data utama dari Jadwal Pengantin agar otomatis terisi
        $query = \App\Models\JadwalPengantin::with(['paket', 'jadwalGown'])->orderBy('tanggal_awal', 'asc');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_full', function ($row) {
                $awal = $row->tanggal_awal ? \Carbon\Carbon::parse($row->tanggal_awal)->format('d') : '-';
                $akhir = $row->tanggal_akhir ? \Carbon\Carbon::parse($row->tanggal_akhir)->format('d') : null;
                $bulanTahun = $row->bulan . ' ' . $row->tahun;
                return ($akhir && $awal != $akhir) ? "$awal - $akhir $bulanTahun" : "$awal $bulanTahun";
            })
            ->addColumn('paket', fn($row) => $row->paket?->nama_paket ?? '-')
            // Menampilkan rincian busana
            ->addColumn('gown_detail', function ($row) {
                return $row->jadwalGown->nama_gown ?? '<span class="badge bg-light-secondary text-muted">Belum diset</span>';
            })
            ->addColumn('action', function ($row) {
                $gownId = $row->jadwalGown ? $row->jadwalGown->id : 'null';
                return '
            <div class="d-flex justify-content-center gap-2">
                <a href="' . route('admin.jadwalgown.edit', $row->id) . '" class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <button onclick="hapusGown(' . $gownId . ')" class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>';
            })
            ->rawColumns(['gown_detail', 'action'])
            ->make(true);
    }

    public function create()
    {
        $jadwals = JadwalPengantin::where('tanggal_awal', '>=', now()->toDateString())
            ->whereDoesntHave('jadwalGown') // Sesuaikan 'jadwalGown' dengan nama fungsi relasi di Model JadwalPengantin kamu
            ->orderBy('tanggal_awal', 'asc')
            ->get();

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
        // KUNCI UTAMA: Ambil dari JadwalPengantin agar semua acara tetap muncul meskipun gown belum diset
        $query = \App\Models\JadwalPengantin::with(['paket', 'jadwalGown']);

        // Filter bulan dan tahun disesuaikan dengan data acara pengantin
        if ($request->filled('bulan')) {
            $query->where('bulan', trim($request->bulan));
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', trim($request->tahun));
        }

        // Urutkan berdasarkan tanggal acara
        $jadwal = $query->orderBy('tanggal_awal', 'asc')->get()->map(function ($item) {
            $awal = $item->tanggal_awal ? \Carbon\Carbon::parse($item->tanggal_awal)->format('d') : '-';
            $akhir = $item->tanggal_akhir ? \Carbon\Carbon::parse($item->tanggal_akhir)->format('d') : null;
            $item->tanggal_display = $akhir && $akhir != $awal ? "$awal - $akhir" : $awal;
            return $item;
        });

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.jadwalgown.print', compact('jadwal', 'bulan', 'tahun'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true
            ]);

        return $pdf->stream('jadwal_gown.pdf');
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
