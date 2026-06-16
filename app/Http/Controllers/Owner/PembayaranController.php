<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    /**
     * Menampilkan halaman utama laporan pembayaran (Owner)
     */
    public function index()
    {
        return view('owner.pembayaran.index');
    }

    /**
     * Mengambil data untuk DataTables (Server-Side)
     */
    public function data(Request $request)
    {
        $query = Booking::with(['paket', 'pembayarans'])
            ->whereIn('status', ['CONFIRMED', 'COMPLETED'])
            ->latest();

        // 1. Filter Bulan (Mengubah nama bulan menjadi angka)
        if ($request->filled('bulan')) {
            $monthMap = [
                'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4, 
                'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8, 
                'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
            ];
            $bulanAngka = $monthMap[$request->bulan] ?? null;
            if ($bulanAngka) {
                $query->whereMonth('event_date', $bulanAngka);
            }
        }

        // 2. Filter Tahun
        if ($request->filled('tahun')) {
            $query->whereYear('event_date', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_full', function ($row) {
                return Carbon::parse($row->event_date)->format('d M Y');
            })
            ->addColumn('pengantin', function ($row) {
                return $row->bride_groom_name;
            })
            ->addColumn('paket_nama', function ($row) {
                return $row->paket->nama_paket ?? '-';
            })
            ->addColumn('harga_paket', function ($row) {
                return $row->paket->harga ?? 0;
            })
            ->addColumn('sisa_tagihan', function ($row) {
                $totalHarga = $row->paket->harga ?? 0;
                $totalBayar = $row->pembayarans
                    ->whereIn('status_pembayaran', ['success', 'lunas', null])
                    ->sum('jumlah_bayar');
                $sisa = $totalHarga - $totalBayar;
                return 'Rp ' . number_format(max(0, $sisa), 0, ',', '.');
            })
            ->addColumn('status_pembayaran', function ($row) {
                $totalHarga = $row->paket->harga ?? 0;
                $totalBayar = $row->pembayarans
                    ->whereIn('status_pembayaran', ['success', 'lunas', null])
                    ->sum('jumlah_bayar');

                if ($totalHarga > 0 && $totalBayar >= $totalHarga) {
                    return '<span class="badge bg-primary">LUNAS</span>';
                }
                return '<span class="badge bg-danger">BELUM LUNAS</span>';
            })
            ->addColumn('action', function ($row) {
                return '<a href="'.route('owner.pembayaran.histori', $row->id).'" class="btn btn-info btn-sm shadow-sm">
                            <i class="bi bi-eye"></i>
                        </a>';
            })
            ->rawColumns(['status_pembayaran', 'action'])
            ->make(true);
    }

    /**
     * Menampilkan detail riwayat pembayaran per pesanan
     */
    public function histori($id)
    {
        $booking = Booking::with(['paket', 'pembayarans'])->findOrFail($id);
        
        $totalHarga = $booking->paket->harga ?? 0;
        $totalBayar = $booking->pembayarans
            ->whereIn('status_pembayaran', ['success', 'lunas', null])
            ->sum('jumlah_bayar');
        $sisaTagihan = $totalHarga - $totalBayar;

        return view('owner.pembayaran.histori', compact('booking', 'totalHarga', 'totalBayar', 'sisaTagihan'));
    }
}