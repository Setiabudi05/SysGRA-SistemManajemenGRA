<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menampilkan riwayat pesanan aktif & completed beserta rincian lembar cicilannya
     */
    public function riwayat()
    {
        // Tambahkan 'paket' di dalam array with()
        $historyBookings = Booking::with(['paket', 'addOns', 'pembayarans' => function ($query) {
            $query->where('status_pembayaran', 'LIKE', '%success%')
                ->orWhere('status_pembayaran', 'LIKE', '%lunas%')
                ->orWhere('status_pembayaran', 'LIKE', '%confirmed%')
                ->orWhereNull('status_pembayaran')
                ->orderBy('created_at', 'asc');
        }])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'COMPLETED', 'success', 'SUCCESS', 'confirmed', 'CONFIRMED'])
            ->orderBy('event_date', 'desc')
            ->get();

        $historyBookings->map(function ($booking) {
            $booking->total_terbayar = $booking->pembayarans->sum('jumlah_bayar');
            return $booking;
        });

        return view('user.riwayat.index', compact('historyBookings'));
    }
}
