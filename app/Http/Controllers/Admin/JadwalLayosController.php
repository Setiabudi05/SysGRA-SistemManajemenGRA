<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalLayos;
use App\Models\JadwalPengantin;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalLayosController extends Controller
{
    /**
     * Tampilkan halaman utama jadwal layos.
     */
    public function index()
    {
        return view('admin.jadwallayos.index');
    }

    /**
     * Ambil data untuk DataTables AJAX dengan filter bulan.
     */
    /**
     * Ambil data untuk DataTables AJAX dengan filter.
     */
    public function data(Request $request)
    {
        // Mengambil data utama dari Jadwal Pengantin
        $query = \App\Models\JadwalPengantin::with(['paket', 'jadwalLayos'])->orderBy('tanggal_awal', 'asc');

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
            // Menampilkan rincian tenda/layos
            ->addColumn('layos_detail', function ($row) {
                return $row->jadwalLayos->deskripsi ?? '<span class="text-muted italic small">Belum ada rincian...</span>';
            })
            ->addColumn('action', function ($row) {
                $layosId = $row->jadwalLayos ? $row->jadwalLayos->id : 'null';
                return '
            <div class="d-flex justify-content-center gap-2">
                <a href="' . route('admin.jadwallayos.edit', $row->id) . '" class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <button onclick="hapusLayos(' . $layosId . ')" class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>';
            })
            ->rawColumns(['layos_detail', 'action'])
            ->make(true);
    }

    /**
     * Tampilkan form tambah jadwal layos.
     */
    public function create()
    {
        $jadwals = JadwalPengantin::with('paket')
            ->orderBy('tanggal_awal')
            ->get();

        return view('admin.jadwallayos.create', compact('jadwals'));
    }

    /**
     * Simpan jadwal layos baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $pengantin = JadwalPengantin::findOrFail($request->jadwal_pengantin_id);

        $sync = $this->syncWithPengantin($pengantin);
        $data = array_merge($validated, $sync);
        JadwalLayos::create($data);

        // Redirect ke filter sesuai bulan dan tahun data yang baru dibuat
        return redirect()->route('admin.jadwallayos.index', [
            'bulan' => $sync['bulan'],
            'tahun' => $sync['tahun']
        ])->with('swal_success', 'Jadwal Layos berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit jadwal layos.
     */
    public function edit($id)
    {
        $jadwal = JadwalLayos::findOrFail($id);
        $jadwals = JadwalPengantin::with('paket')->orderBy('tanggal_awal')->get();

        return view('admin.jadwallayos.edit', compact('jadwal', 'jadwals'));
    }

    /**
     * Perbarui data jadwal layos.
     */
    public function update(Request $request, $id)
    {
        $validated = $this->validateRequest($request);
        $jadwal = JadwalLayos::findOrFail($id);
        $pengantin = JadwalPengantin::findOrFail($request->jadwal_pengantin_id);

        $sync = $this->syncWithPengantin($pengantin);
        $data = array_merge($validated, $sync);
        $jadwal->update($data);

        // Redirect menggunakan parameter jejak filter dari input hidden
        return redirect()->route('admin.jadwallayos.index', [
            'bulan' => $request->last_bulan,
            'tahun' => $request->last_tahun
        ])->with('swal_success', 'Jadwal Layos berhasil diperbarui!');
    }

    /**
     * Hapus data jadwal layos.
     */
    public function destroy($id)
    {
        try {
            $jadwal = JadwalLayos::findOrFail($id);
            $jadwal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal layos berhasil dihapus dari sistem.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cetak jadwal layos ke format PDF.
     */
    public function print(Request $request)
    {
        $query = JadwalLayos::with(['pengantin.paket']);

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $jadwal = $query->get();
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        return Pdf::loadView('admin.jadwallayos.print', compact('jadwal', 'bulan', 'tahun'))
            ->setPaper('A4', 'portrait')
            ->stream('jadwal-layos.pdf');
    }

    /**
     * Helper: Validasi Request.
     */
    private function validateRequest(Request $request)
    {
        return $request->validate([
            'jadwal_pengantin_id' => 'required|exists:jadwal_pengantins,id',
            'layos' => 'required|string',
        ]);
    }

    /**
     * Helper: Ambil Bulan dan Tahun dari Jadwal Pengantin.
     */
    // Di JadwalLayosController.php pada fungsi syncWithPengantin
    private function syncWithPengantin($pengantin)
    {
        // Tambahkan locale('id') agar hasil format('F') menjadi bahasa Indonesia
        $date = Carbon::parse($pengantin->tanggal_awal)->locale('id');

        return [
            'jadwal_pengantin_id' => $pengantin->id,
            'bulan' => $date->translatedFormat('F'), // Menghasilkan "Januari", "Februari", dll
            'tahun' => $date->format('Y'),
        ];
    }
}
