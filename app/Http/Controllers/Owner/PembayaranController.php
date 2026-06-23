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
        // Ambil data dengan relasi paket dan pembayaran
        $bookings = Booking::with(['paket', 'pembayarans'])
            ->whereIn('status', ['CONFIRMED', 'COMPLETED'])
            ->latest()
            ->get(); // Gunakan get() agar menjadi Collection

        // Filter berdasarkan logika manual
        $filteredData = $bookings->filter(function ($row) use ($request) {
            $totalHarga = $row->paket->harga ?? 0;
            $totalBayar = $row->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');

            // Logika Status: Lunas atau Belum Lunas
            $statusPembayaran = ($totalBayar >= $totalHarga && $totalHarga > 0) ? 'LUNAS' : 'BELUM LUNAS';

            if ($request->filled('status') && $statusPembayaran !== $request->status) return false;
            if ($request->filled('tgl') && Carbon::parse($row->event_date)->format('Y-m-d') !== $request->tgl) return false;

            return true;
        });

        return DataTables::of($filteredData)
            ->addIndexColumn()
            ->addColumn('tanggal_full', fn($row) => Carbon::parse($row->event_date)->format('d M Y'))
            ->addColumn('pengantin', fn($row) => $row->bride_groom_name)
            ->addColumn('paket_nama', fn($row) => $row->paket->nama_paket ?? '-')
            ->addColumn('harga_paket', fn($row) => 'Rp ' . number_format($row->paket->harga ?? 0, 0, ',', '.'))
            ->addColumn('sisa_tagihan', function ($row) {
                $sisa = ($row->paket->harga ?? 0) - $row->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
                return 'Rp ' . number_format(max(0, $sisa), 0, ',', '.');
            })
            ->addColumn('status_pembayaran', function ($row) {
                $totalHarga = $row->paket->harga ?? 0;
                $totalBayar = $row->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
                return ($totalBayar >= $totalHarga && $totalHarga > 0)
                    ? '<span class="badge bg-primary">LUNAS</span>'
                    : '<span class="badge bg-danger">BELUM LUNAS</span>';
            })
            ->addColumn('action', fn($row) => '<a href="' . route('owner.pembayaran.histori', $row->id) . '" class="btn btn-info btn-sm shadow-sm"><i class="bi bi-eye"></i></a>')
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

    /**
     * Menampilkan halaman cetak nota pembayaran
     */
    /**
     * Menampilkan halaman cetak nota pembayaran
     */
    public function cetakNota($pembayaran_id)
    {
        // Mengambil transaksi berdasarkan ID pembayaran
        $transaksi = \App\Models\Pembayaran::with(['booking.paket'])->findOrFail($pembayaran_id);
        $booking = $transaksi->booking;

        return view('owner.pembayaran.nota', compact('transaksi', 'booking'));
    }
}
