<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalDekor;
use App\Models\JadwalPengantin;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalDekorController extends Controller
{
    public function index()
    {
        return view('owner.jadwaldekor.index');
    }

    public function data(Request $request)
    {
        // Mengambil data dari master Jadwal Pengantin agar otomatis muncul
        $query = \App\Models\JadwalPengantin::with(['paket', 'jadwalDekor'])->orderBy('tanggal_awal', 'asc');

        // Filter akan aktif sesuai kiriman dari AJAX di atas
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_awal', $request->tanggal);
        } else {
            // Hanya gunakan bulan & tahun jika tanggal tidak dipilih
            if ($request->filled('bulan')) {
                // Fleksibel: mendukung filter nama bulan Indo atau Inggris
                $bulanInput = $request->bulan;
                $query->where(function ($q) use ($bulanInput) {
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
                // Gabungkan tanggal, bulan, dan tahun
                $awal = $row->tanggal_awal ? \Carbon\Carbon::parse($row->tanggal_awal)->format('d') : '-';
                $akhir = $row->tanggal_akhir ? \Carbon\Carbon::parse($row->tanggal_akhir)->format('d') : null;
                $bulanTahun = $row->bulan . ' ' . $row->tahun;
                return ($akhir && $awal != $akhir) ? "$awal - $akhir $bulanTahun" : "$awal $bulanTahun";
            })
            ->addColumn('paket_nama', fn($row) => $row->paket->nama_paket ?? '-')
            ->addColumn('foto', function ($row) {
                if ($row->jadwalDekor && $row->jadwalDekor->foto) {
                    return '<div class="text-center"><img src="' . asset('storage/' . $row->jadwalDekor->foto) . '" width="60" class="rounded shadow-sm"></div>';
                }
                return '<div class="text-center"><span class="badge bg-light-secondary text-muted small">No Photo</span></div>';
            })
            ->addColumn('deskripsi', function ($row) {
                return $row->jadwalDekor->deskripsi ?? '<span class="text-muted italic small">Belum ada rincian...</span>';
            })
            ->rawColumns(['foto', 'deskripsi'])
            ->make(true);
    }

    public function print(Request $request)
    {
        $query = \App\Models\JadwalPengantin::with(['paket', 'jadwalDekor']);

        if ($request->filled('bulan')) {
            $bulanInput = $request->bulan;
            $query->where(function ($q) use ($bulanInput) {
                $q->where('bulan', $bulanInput)
                    ->orWhere('bulan', $this->translateBulan($bulanInput));
            });
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $jadwal = $query->orderBy('tanggal_awal', 'asc')->get();

        return Pdf::loadView('owner.jadwaldekor.print', [
            'jadwal' => $jadwal,
            'bulan'  => $request->bulan ?? 'Semua',
            'tahun'  => $request->tahun ?? ''
        ])->setPaper('A4', 'portrait')->stream('laporan-jadwal-dekorasi.pdf');
    }

    // Fungsi bantu untuk mengatasi perbedaan format bulan (Indo/Inggris)
    private function translateBulan($namaBulan)
    {
        $map = [
            'Januari' => 'January',
            'Februari' => 'February',
            'Maret' => 'March',
            'April' => 'April',
            'Mei' => 'May',
            'Juni' => 'June',
            'Juli' => 'July',
            'Agustus' => 'August',
            'September' => 'September',
            'Oktober' => 'October',
            'November' => 'November',
            'Desember' => 'December'
        ];
        return $map[$namaBulan] ?? $namaBulan;
    }
}
