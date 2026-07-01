<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalGown;
use App\Models\JadwalPengantin;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class JadwalGownController extends Controller
{
    public function index()
    {
        return view('admin.jadwalgown.index');
    }

    public function data(Request $request)
    {
        $query = JadwalPengantin::with(['paket', 'jadwalGown'])->orderBy('tanggal_awal', 'asc');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_awal', $request->tanggal);
        } else {
            // Hanya gunakan bulan & tahun jika tanggal tidak dipilih
            if ($request->filled('bulan')) {
                $query->where('bulan', $request->bulan);
            }
            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }
        }
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_full', function ($row) {
                $awal = $row->tanggal_awal ? Carbon::parse($row->tanggal_awal)->format('d') : '-';
                return "$awal {$row->bulan} {$row->tahun}";
            })
            ->addColumn('paket', fn($row) => $row->paket?->nama_paket ?? '-')
            ->addColumn('gown_detail', function ($row) {
                return $row->jadwalGown ? $row->jadwalGown->gown : '<span class="text-muted small">Belum diset</span>';
            })
            ->addColumn('action', function ($row) {
                // Alur mengikuti Dekor: Mengarahkan ke edit ID Pengantin
                $editUrl = route('admin.jadwalgown.edit', ['id' => $row->id]);
                $gownId = $row->jadwalgown ? $row->jadwalGown->id : 'null';
                return '
    <div class="d-flex justify-content-center gap-2">
        <a href="' . $editUrl . '" class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
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

    // Pastikan fungsi edit mengirim 'jadwal' dan 'pengantin'
    public function edit($id)
    {
        // Kita ambil pengantinnya dulu
        $pengantin = JadwalPengantin::with(['paket', 'jadwalGown'])->findOrFail($id);

        // Kita siapkan objek Gown (jika null, buat objek baru)
        $jadwal = $pengantin->jadwalGown ?? new JadwalGown();

        return view('admin.jadwalgown.edit', compact('jadwal', 'pengantin'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['gown' => 'required']);
        $p = JadwalPengantin::findOrFail($id);

        JadwalGown::updateOrCreate(
            ['jadwal_pengantin_id' => $id],
            [
                'gown' => $request->gown,
                'nama' => $p->nama,
                'alamat' => $p->alamat,
                'paket_id' => $p->paket_id,
                'bulan' => $p->bulan,
                'tahun' => $p->tahun,
            ]
        );

        return redirect()->route('admin.jadwalgown.index')->with('success', 'Data berhasil diperbarui!');
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
