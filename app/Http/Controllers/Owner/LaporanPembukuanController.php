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
        // Ambil filter tanggal (default hari ini)
        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        // Data untuk Card Ringkasan
        $pemasukan = Pembukuan::where('tipe', 'pemasukan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('nominal');

        $pengeluaran = Pembukuan::where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('nominal');

        $saldo = $pemasukan - $pengeluaran;

        // Data untuk Tabel Rincian
        $listPemasukan = Pembukuan::where('tipe', 'pemasukan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();

        $listPengeluaran = Pembukuan::where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('owner.pembukuan.index', compact(
            'pemasukan', 'pengeluaran', 'saldo', 
            'startDate', 'endDate', 
            'listPemasukan', 'listPengeluaran'
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