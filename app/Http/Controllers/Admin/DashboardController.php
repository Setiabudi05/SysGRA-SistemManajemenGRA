<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paket;
use App\Models\Dekorasi;
use App\Models\JadwalPengantin;
use App\Models\User;
use App\Models\Pembukuan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPaket = \App\Models\Paket::count();
        $totalDekorasi = \App\Models\Dekorasi::count(); // Sesuaikan nama model Anda
        $totalPelanggan = \App\Models\Booking::distinct('customer_name')->count();
        $totalBooking = \App\Models\Booking::count();

        // Statistik Finansial dari tabel Pembukuan
        $netIncome = \App\Models\Pembukuan::where('tipe', 'pemasukan')->sum('nominal') -
            \App\Models\Pembukuan::where('tipe', 'pengeluaran')->sum('nominal');

        // Sisa tagihan dari kolom virtual/accessor model Booking
        $pendingPayment = \App\Models\Booking::all()->sum('sisa_tagihan');

        // Mengambil 5 aktivitas terbaru
        $recentBookings = \App\Models\Booking::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalPaket',
            'totalDekorasi',
            'totalPelanggan',
            'totalBooking',
            'netIncome',
            'pendingPayment',
            'recentBookings'
        ));
    }

    private function getBulanIndo($month)
    {
        $map = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];
        return $map[$month] ?? $month;
    }
}
