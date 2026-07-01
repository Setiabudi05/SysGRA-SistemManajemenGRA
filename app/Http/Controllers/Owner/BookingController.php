<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * 1. Menampilkan halaman index laporan pesanan
     */
    public function index()
    {
        return view('owner.booking.index');
    }

    /**
     * 2. Mengambil data JSON untuk DataTables (Server-Side)
     */
    public function data(Request $request)
    {
        // 1. Ambil data dengan eager loading
        $query = Booking::with(['paket', 'addOns', 'pembayarans'])
            ->orderBy('event_date', 'asc');

        // 2. Filter Tanggal (Ini aman dilakukan di Database)
        if ($request->filled('tgl_acara')) {
            $query->whereDate('event_date', $request->tgl_acara);
        }

        $bookings = $query->get();

        // 3. Filter STATUS (Ini dilakukan setelah data ditarik ke memory)
        if ($request->filled('status')) {
            $status = $request->status;
            $bookings = $bookings->filter(function ($row) use ($status) {
                // Kita hitung ulang statusnya di sini
                $totalHarga = ($row->paket->harga ?? 0) + $row->addOns->sum('harga');
                $totalTerbayar = $row->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
                $sisa = $totalHarga - $totalTerbayar;

                $statusPembayaran = ($sisa <= 0 && $totalHarga > 0) ? 'LUNAS' : 'BELUM LUNAS';

                // Sesuaikan dengan logic status Anda (PENDING/CONFIRMED dll)
                return strtoupper($row->status) === strtoupper($status);
            });
        }

        return DataTables::of($bookings)
            ->addIndexColumn()
            ->addColumn('tanggal_full', fn($row) => Carbon::parse($row->event_date)->format('d M Y'))
            ->addColumn('nama', fn($row) => $row->bride_groom_name)
            ->addColumn('paket_nama', fn($row) => $row->paket->nama_paket ?? '-')

            // PERBAIKAN: Hitung Harga Murni (Paket + AddOns) Tanpa Durasi
            // Di dalam method data() pada BookingController
            ->addColumn('harga_total_final', function ($row) { // Ubah nama kolom agar jelas
                $hargaPaket = (int) ($row->paket ? $row->paket->harga : 0);
                $totalAddons = (int) $row->addOns->sum('harga');
                return $hargaPaket + $totalAddons; // Ini akan dikirim ke JS sebagai angka
            })
            ->editColumn('status', function ($row) {
                $status = strtoupper($row->status);
                $color = [
                    'DRAFT'     => 'secondary',
                    'PENDING'   => 'warning text-dark',
                    'CONFIRMED' => 'success',
                    'COMPLETED' => 'primary'
                ][$status] ?? 'secondary';

                return '<span class="badge bg-' . $color . ' px-3 py-2 fw-bold">' . $status . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('owner.booking.show', $row->id) . '" class="btn btn-info btn-sm shadow-sm">
                            <i class="bi bi-eye"></i>
                        </a>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * 3. Menampilkan detail pesanan (show)
     */
    public function show($id)
    {
        // Mengambil data Booking dengan relasi paket dan pembayaran
        $booking = Booking::with(['paket', 'pembayarans'])->findOrFail($id);

        $totalHarga = $booking->paket->harga ?? 0;
        $totalBayar = $booking->pembayarans
            ->whereIn('status_pembayaran', ['success', 'lunas', null])
            ->sum('jumlah_bayar');
        $sisaTagihan = $totalHarga - $totalBayar;

        return view('owner.booking.show', compact('booking', 'totalHarga', 'totalBayar', 'sisaTagihan'));
    }
}
