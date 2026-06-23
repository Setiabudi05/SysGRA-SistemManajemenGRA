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
        // Menggunakan Booking sebagai sumber data utama
        $query = Booking::with(['paket'])
            ->orderBy('event_date', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tgl_acara')) {
            $query->whereDate('event_date', $request->tgl_acara);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_full', function ($row) {
                return Carbon::parse($row->event_date)->format('d M Y');
            })
            ->addColumn('nama', function ($row) {
                return $row->bride_groom_name;
            })
            ->addColumn('paket_nama', function ($row) {
                return $row->paket->nama_paket ?? '-';
            })
            ->addColumn('harga_paket', function ($row) {
                return $row->paket->harga ?? 0;
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
