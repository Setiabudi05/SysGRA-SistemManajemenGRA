<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\JadwalPengantin;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller
{
    public function index()
    {
        return view('owner.booking.index');
    }

    public function data(Request $request)
    {
        // Gunakan orderBy untuk mengurutkan berdasarkan tanggal acara terdekat
        $query = JadwalPengantin::with('paket')->orderBy('tanggal_awal', 'asc');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_full', function ($row) {
                $tglAwal = \Carbon\Carbon::parse($row->tanggal_awal)->format('d');
                $tglAkhir = $row->tanggal_akhir ? \Carbon\Carbon::parse($row->tanggal_akhir)->format('d') : null;
                $bulanTahun = $row->bulan . ' ' . $row->tahun;

                if ($tglAkhir && $tglAwal != $tglAkhir) {
                    return "$tglAwal-$tglAkhir $bulanTahun";
                }
                return "$tglAwal $bulanTahun";
            })
            ->addColumn('harga_paket', function ($row) {
                return $row->paket->harga ?? 0;
            })
            ->editColumn('status', function ($row) {
                return '<span class="badge bg-light-primary text-primary px-3 py-2 fw-bold">TERKONFIRMASI</span>';
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('owner.booking.show', $row->id) . '" class="btn btn-info btn-sm shadow-sm">
                        <i class="bi bi-eye"></i>
                    </a>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        // Mengambil data jadwal beserta paket dan pembayarannya
        $jadwal = JadwalPengantin::with(['paket', 'pembayarans'])->findOrFail($id);

        // Hitung total bayar dan sisa tagihan
        $totalBayar = $jadwal->pembayarans->sum('nominal');
        $sisaTagihan = ($jadwal->paket->harga ?? 0) - $totalBayar;

        return view('owner.booking.show', compact('jadwal', 'totalBayar', 'sisaTagihan'));
    }
    public function print_all(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        // Logic ambil data berdasarkan filter untuk dicetak...
    }
}
