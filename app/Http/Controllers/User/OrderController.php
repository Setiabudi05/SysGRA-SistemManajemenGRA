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
        // Tarik data booking milik user beserta semua data riwayat cicilannya
        $historyBookings = Booking::with(['pembayarans' => function($query) {
                $query->where('status_pembayaran', 'LIKE', '%success%')
                      ->orWhere('status_pembayaran', 'LIKE', '%lunas%')
                      ->orWhere('status_pembayaran', 'LIKE', '%confirmed%') // Tambah pelacak status pembayaran confirmed
                      ->orWhereNull('status_pembayaran')
                      ->orderBy('created_at', 'asc');
            }])
            // PERBAIKAN 1: Ganti 'another_column_name' menjadi 'user_id' agar mengunci ID akun Naila
            ->where('user_id', Auth::id())
            // PERBAIKAN 2: Masukkan 'CONFIRMED' dan 'confirmed' agar pesanan yang baru masuk/DP langsung terdeteksi di halaman riwayat
            ->whereIn('status', ['completed', 'COMPLETED', 'success', 'SUCCESS', 'confirmed', 'CONFIRMED'])
            ->orderBy('event_date', 'desc')
            ->get();

        // Hitung total akumulasi uang masuk secara bersih
        $historyBookings->map(function ($booking) {
            $booking->total_terbayar = $booking->pembayarans->sum('jumlah_bayar');
            return $booking;
        });

        // Diarahkan ke view riwayat index milik user
        return view('user.riwayat.index', compact('historyBookings'));
    }
}