<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembukuan;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class LaporanPembukuanController extends Controller
{
    public function index(Request $request)
    {
        // Default rentang tanggal: Hari ini
        $start = $request->get('start_date', now()->format('Y-m-d'));
        $end = $request->get('end_date', now()->format('Y-m-d'));

        // Query berdasarkan rentang
        $listPemasukan = Pembukuan::where('tipe', 'pemasukan')
            ->whereBetween('tanggal', [$start, $end])
            ->get();

        $listPengeluaran = Pembukuan::where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$start, $end])
            ->get();

        $pemasukan = $listPemasukan->sum('nominal');
        $pengeluaran = $listPengeluaran->sum('nominal');
        $saldo = $pemasukan - $pengeluaran;

        return view('owner.pembukuan.index', compact(
            'listPemasukan',
            'listPengeluaran',
            'pemasukan',
            'pengeluaran',
            'saldo',
            'start',
            'end'
        ));
    }

    public function print(Request $request)
    {
        $start = $request->get('start_date');
        $end = $request->get('end_date');

        $dataPemasukan = Pembukuan::where('tipe', 'pemasukan')->whereBetween('tanggal', [$start, $end])->get();
        $dataPengeluaran = Pembukuan::where('tipe', 'pengeluaran')->whereBetween('tanggal', [$start, $end])->get();

        $totalMasuk = $dataPemasukan->sum('nominal');
        $totalKeluar = $dataPengeluaran->sum('nominal');

        $data = compact('dataPemasukan', 'dataPengeluaran', 'totalMasuk', 'totalKeluar', 'start', 'end');

        // Menggunakan DomPDF untuk download file PDF
        $pdf = PDF::loadView('owner.pembukuan.print', $data)->setPaper('a4', 'portrait');
        return $pdf->download('Laporan_Pembukuan_' . $start . '_sd_' . $end . '.pdf');
    }
}
