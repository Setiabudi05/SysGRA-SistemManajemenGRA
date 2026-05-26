<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\JadwalPengantin;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    public function index()
    {
        return view('owner.pembayaran.index');
    }

    public function data(Request $request)
    {
        // Ambil data dari JadwalPengantin urut berdasarkan tanggal awal (Ascending)
        $query = JadwalPengantin::with(['paket', 'pembayarans'])->orderBy('tanggal_awal', 'asc');

        // Filter berdasarkan Bulan dan Tahun
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_full', function ($row) {
                $tglAwal = Carbon::parse($row->tanggal_awal)->format('d');
                $tglAkhir = $row->tanggal_akhir ? Carbon::parse($row->tanggal_akhir)->format('d') : null;
                $bulanTahun = $row->bulan . ' ' . $row->tahun;

                if ($tglAkhir && $tglAwal != $tglAkhir) {
                    return "$tglAwal-$tglAkhir $bulanTahun";
                }
                return "$tglAwal $bulanTahun";
            })
            ->addColumn('paket_nama', function($row) {
                return $row->paket->nama_paket ?? '-';
            })
            ->addColumn('harga_paket', function($row) {
                return $row->paket->harga ?? 0;
            })
            ->addColumn('sisa_tagihan', function($row) {
                $totalHarga = $row->paket->harga ?? 0;
                $totalBayar = $row->pembayarans->sum('nominal') ?? 0;
                $sisa = $totalHarga - $totalBayar;
                return 'Rp ' . number_format(max(0, $sisa), 0, ',', '.');
            })
            ->addColumn('status_pembayaran', function($row) {
                $totalHarga = $row->paket->harga ?? 0;
                $totalBayar = $row->pembayarans->sum('nominal') ?? 0;
                $sisa = $totalHarga - $totalBayar;

                if ($sisa <= 0 && $totalHarga > 0) {
                    return '<span class="badge bg-light-success text-success fw-bold">LUNAS</span>';
                }
                return '<span class="badge bg-light-danger text-danger fw-bold">BELUM LUNAS</span>';
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('owner.pembayaran.histori', $row->id) . '" class="btn btn-info btn-sm shadow-sm">
                            <i class="bi bi-eye"></i>
                        </a>';
            })
            ->rawColumns(['status_pembayaran', 'action'])
            ->make(true);
    }
    public function histori($id)
{
    // Mengambil data jadwal pengantin beserta histori pembayarannya
    $jadwal = JadwalPengantin::with(['paket', 'pembayarans'])->findOrFail($id);
    
    // Hitung ringkasan
    $totalHarga = $jadwal->paket->harga ?? 0;
    $totalBayar = $jadwal->pembayarans->sum('nominal');
    $sisaTagihan = $totalHarga - $totalBayar;

    return view('owner.pembayaran.histori', compact('jadwal', 'totalHarga', 'totalBayar', 'sisaTagihan'));
}
}