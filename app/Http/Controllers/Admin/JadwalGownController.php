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
        $query = JadwalPengantin::with(['paket', 'jadwalGown'])->orderBy('tanggal_awal', 'asc');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_awal', $request->tanggal);
        } else {
            if ($request->filled('bulan')) {
                // Perbaikan: Fleksibel terhadap perbedaan bahasa (Inggris/Indo)
                $bulanInput = $request->bulan;
                $query->where(function($q) use ($bulanInput) {
                    $q->where('bulan', $bulanInput)
                      ->orWhere('bulan', $this->translateBulan($bulanInput));
                });
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
                $editUrl = route('admin.jadwalgown.edit', ['id' => $row->id]);
                $gownId = $row->jadwalGown ? $row->jadwalGown->id : 'null';
                return '
                <div class="d-flex justify-content-center gap-2">
                    <a href="' . $editUrl . '" class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <button onclick="hapusJadwal(' . $gownId . ')" class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>';
            })
            ->rawColumns(['gown_detail', 'action'])
            ->make(true);
    }

    // Fungsi bantu untuk sinkronisasi bahasa (PENTING)
    private function translateBulan($namaBulan)
    {
        $map = ['Januari'=>'January', 'Februari'=>'February', 'Maret'=>'March', 'April'=>'April', 'Mei'=>'May', 'Juni'=>'June', 'Juli'=>'July', 'Agustus'=>'August', 'September'=>'September', 'Oktober'=>'October', 'November'=>'November', 'Desember'=>'December'];
        return $map[$namaBulan] ?? $namaBulan;
    }

    public function create()
    {
        $jadwals = JadwalPengantin::where('tanggal_awal', '>=', now()->toDateString())
            ->whereDoesntHave('jadwalGown')
            ->orderBy('tanggal_awal', 'asc')
            ->get();
        return view('admin.jadwalgown.create', compact('jadwals'));
    }

    public function edit($id)
    {
        $pengantin = JadwalPengantin::with(['paket', 'jadwalGown'])->findOrFail($id);
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

        return redirect()->route('admin.jadwalgown.index')->with('swal_success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        try {
            $jadwal = JadwalGown::findOrFail($id);
            $jadwal->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data.']);
        }
    }

    public function print(Request $request)
    {
        $query = \App\Models\JadwalPengantin::with(['paket', 'jadwalGown']);
        
        if ($request->filled('bulan')) {
            $bulanInput = $request->bulan;
            $query->where(function($q) use ($bulanInput) {
                $q->where('bulan', $bulanInput)
                  ->orWhere('bulan', $this->translateBulan($bulanInput));
            });
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $jadwal = $query->orderBy('tanggal_awal', 'asc')->get();
        return Pdf::loadView('admin.jadwalgown.print', [
            'jadwal' => $jadwal,
            'bulan'  => $request->bulan ?? 'Semua',
            'tahun'  => $request->tahun ?? ''
        ])->setPaper('A4', 'portrait')->stream('jadwal_gown.pdf');
    }
}