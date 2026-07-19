<?php

namespace App\Http\Controllers\Kru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPengantin;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /**
     * Menampilkan Halaman Riwayat
     */
    public function index()
    {
        return view('kru.riwayat.index');
    }

    /**
     * Helper Formatter Tanggal
     */
    private function formatTanggal($item)
    {
        $getDay = function ($value) {
            if (!$value)
                return null;
            if (strpos($value, '-') !== false) {
                $parts = explode('-', $value);
                return end($parts);
            }
            return str_pad($value, 2, '0', STR_PAD_LEFT);
        };

        $tglAwal = $getDay($item->getRawOriginal('tanggal_awal') ?? $item->tanggal_awal);
        $tglAkhir = $getDay($item->getRawOriginal('tanggal_akhir') ?? $item->tanggal_akhir);

        if ($tglAkhir && $tglAkhir != $tglAwal && $tglAkhir != "00") {
            return "{$tglAwal}-{$tglAkhir}";
        }
        return $tglAwal;
    }

    /**
     * Data Riwayat untuk DataTables (Logika Nama Depan Fleksibel)
     */
    /**
     * Data Riwayat untuk DataTables (Diperbarui untuk akurasi data)
     */
    public function data(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $namaKru = trim($user->name); // Gunakan TRIM untuk menghapus spasi tersembunyi
        $today = Carbon::today()->toDateString();

        $query = JadwalPengantin::with('paket')
            ->where(function ($q) use ($namaKru) {
                // Gunakan whereRaw dengan TRIM agar pencarian 100% akurat
                $q->whereRaw("TRIM(fg) = ?", [$namaKru])
                    ->orWhereRaw("TRIM(asisten) LIKE ?", ["%$namaKru%"])
                    ->orWhereRaw("TRIM(layos) = ?", [$namaKru]);
            })
            ->where('tanggal_awal', '<', $today)
            ->orderBy('tanggal_awal', 'asc');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_awal', $request->tanggal);
        } else {
            if ($request->filled('bulan')) {
                $query->where('bulan', $request->bulan);
            }
            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }
        }

        return DataTables::of($query)
            ->addIndexColumn() // <-- INI WAJIB ADA agar error DT_RowIndex hilang
            ->addColumn('tanggal_custom', function ($row) {
                $bulanIndo = [
                    'January' => 'Januari',
                    'February' => 'Februari',
                    'March' => 'Maret',
                    'April' => 'April',
                    'May' => 'Mei',
                    'June' => 'Juni',
                    'July' => 'Juli',
                    'August' => 'Agustus',
                    'September' => 'September',
                    'October' => 'Oktober',
                    'November' => 'November',
                    'December' => 'Desember'
                ];
                $bulan = $bulanIndo[$row->bulan] ?? $row->bulan;
                $tglText = $this->formatTanggal($row);
                return "{$tglText} {$bulan} {$row->tahun}";
            })
            ->addColumn('nama_paket', function ($row) {
                return $row->paket?->nama_paket ?? '-';
            })
            ->addColumn('status', function () {
                return '<span class="badge bg-light-success text-success fw-bold">Selesai</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    /**
     * Cetak PDF Riwayat (Logika Nama Depan Fleksibel)
     */
    public function print(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $namaKru = trim($user->name); // Pastikan trim juga digunakan di sini
        $today = Carbon::today()->toDateString();

        $query = JadwalPengantin::with('paket')
            ->where(function ($q) use ($namaKru) {
                // Gunakan logika yang SAMA PERSIS dengan di fungsi data()
                $q->whereRaw("TRIM(fg) = ?", [$namaKru])
                    ->orWhereRaw("TRIM(asisten) LIKE ?", ["%$namaKru%"])
                    ->orWhereRaw("TRIM(layos) = ?", [$namaKru]);
            })
            ->where('tanggal_awal', '<', $today);

        // Filter bulan & tahun
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $riwayat = $query->orderBy('tanggal_awal', 'asc')->get()->map(function ($item) {
            $item->tanggal_display = $this->formatTanggal($item);
            // Pastikan nama pengantin diambil dari field yang benar
            // Jika field nama pengantin adalah 'nama', pastikan $item->nama ada isinya
            return $item;
        });

        $pdf = Pdf::loadView('kru.riwayat.print', [
            'riwayat' => $riwayat,
            'bulan' => $request->bulan ?? 'Semua',
            'tahun' => $request->tahun ?? date('Y'),
            'namaKru' => $namaKru,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('riwayat_kru_' . strtolower(str_replace(' ', '_', $namaKru)) . '.pdf');
    }
}
