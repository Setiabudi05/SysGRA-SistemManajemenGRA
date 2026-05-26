<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Menggunakan 'another_column_name' sesuai struktur tabel kamu
        $latestBooking = Booking::where('another_column_name', $userId)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->latest()
            ->first();

        $total_pesanan = Booking::where('another_column_name', $userId)
            ->where('status', '!=', 'draft')
            ->count();

        $status_terakhir = $latestBooking ? $latestBooking->status : 'Belum Ada';

        // Hitung sisa tagihan (package_price dikurangi total pembayaran)
        // Pastikan kamu punya logika untuk menghitung total_bayar, di sini saya set 0 jika belum ada
        $sisa_tagihan = 0;
        if ($latestBooking) {
            $sisa_tagihan = (int)$latestBooking->package_price - ($latestBooking->total_bayar ?? 0);
        }

        return view('user.dashboard', compact(
            'total_pesanan',
            'status_terakhir',
            'sisa_tagihan'
        ));
    }
}
