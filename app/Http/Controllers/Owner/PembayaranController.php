<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
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
        // 1. Ambil data dalam bentuk Query Builder (tapi jangan get() dulu)
        $query = Booking::with(['paket', 'addOns', 'pembayarans'])
            ->whereIn('status', ['CONFIRMED', 'COMPLETED']);

        // 2. Jika ada filter TANGGAL, lakukan di database (biar cepat)
        if ($request->filled('tgl')) {
            $query->whereDate('event_date', $request->tgl);
        }

        // 3. Eksekusi query menjadi Collection agar kita bisa memfilter hasil perhitungan
        $bookings = $query->get();

        // 4. Lakukan Filter STATUS (LUNAS / BELUM LUNAS) pada collection
        if ($request->filled('status')) {
            $bookings = $bookings->filter(function ($row) use ($request) {
                $totalHarga = ($row->paket->harga ?? 0) + $row->addOns->sum('harga');
                $totalBayar = $row->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
                $statusPembayaran = ($totalBayar >= $totalHarga && $totalHarga > 0) ? 'LUNAS' : 'BELUM LUNAS';

                return $statusPembayaran == $request->status;
            });
        }

        // 5. Kirim data ke DataTables
        return DataTables::of($bookings)
            ->addIndexColumn()
            ->addColumn('tanggal_full', fn($row) => Carbon::parse($row->event_date)->format('d M Y'))
            ->addColumn('pengantin', fn($row) => $row->bride_groom_name)
            ->addColumn('paket_nama', fn($row) => $row->paket->nama_paket ?? '-')
            ->addColumn('harga_paket', function ($row) {
                $total = ($row->paket->harga ?? 0) + $row->addOns->sum('harga');
                return 'Rp ' . number_format($total, 0, ',', '.');
            })
            ->addColumn('sisa_tagihan', function ($row) {
                $totalHarga = ($row->paket->harga ?? 0) + $row->addOns->sum('harga');
                $terbayar = $row->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
                return 'Rp ' . number_format(max(0, $totalHarga - $terbayar), 0, ',', '.');
            })
            ->addColumn('status_pembayaran', function ($row) {
                $totalHarga = ($row->paket->harga ?? 0) + $row->addOns->sum('harga');
                $totalBayar = $row->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
                return ($totalBayar >= $totalHarga && $totalHarga > 0)
                    ? '<span class="badge bg-primary">LUNAS</span>'
                    : '<span class="badge bg-danger">BELUM LUNAS</span>';
            })
            ->addColumn('action', fn($row) => '<a href="' . route('owner.pembayaran.histori', $row->id) . '" class="btn btn-info btn-sm shadow-sm"><i class="bi bi-eye"></i></a>')
            ->rawColumns(['status_pembayaran', 'action'])
            ->make(true);
    }

    public function histori($id)
    {
        $booking = Booking::with(['paket', 'pembayarans', 'addOns'])->findOrFail($id);
        $totalHarga = ($booking->paket->harga ?? 0) + $booking->addOns->sum('harga');
        $totalBayar = $booking->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
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
