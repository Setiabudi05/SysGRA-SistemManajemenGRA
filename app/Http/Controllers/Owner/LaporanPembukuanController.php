<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembukuan;
use Carbon\Carbon;

class LaporanPembukuanController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil tanggal dari input, default-nya hari ini
        $tanggalDipilih = $request->get('tanggal', date('Y-m-d'));

        // Pemasukan harian
        $listPemasukan = Pembukuan::where('tipe', 'pemasukan')
            ->whereDate('tanggal', $tanggalDipilih)
            ->get();

        // Pengeluaran harian
        $listPengeluaran = Pembukuan::where('tipe', 'pengeluaran')
            ->whereDate('tanggal', $tanggalDipilih)
            ->get();

        // Hitung total untuk card ringkasan
        $pemasukan = $listPemasukan->sum('nominal');
        $pengeluaran = $listPengeluaran->sum('nominal');
        $saldo = $pemasukan - $pengeluaran;

        return view('owner.pembukuan.index', compact(
            'listPemasukan',
            'listPengeluaran',
            'pemasukan',
            'pengeluaran',
            'saldo',
            'tanggalDipilih'
        ));
    }

    public function print(Request $request)
    {
        $start = $request->get('start_date');
        $end = $request->get('end_date');

        $dataPemasukan = Pembukuan::where('tipe', 'pemasukan')
            ->whereBetween('tanggal', [$start, $end])
            ->get();

        $dataPengeluaran = Pembukuan::where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$start, $end])
            ->get();

        $totalMasuk = $dataPemasukan->sum('nominal');
        $totalKeluar = $dataPengeluaran->sum('nominal');

        return view('owner.pembukuan.print', compact('dataPemasukan', 'dataPengeluaran', 'totalMasuk', 'totalKeluar', 'start', 'end'));
    }
}
