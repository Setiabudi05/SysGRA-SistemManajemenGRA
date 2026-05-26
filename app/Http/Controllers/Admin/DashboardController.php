<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paket;
use App\Models\Dekorasi;
use App\Models\JadwalPengantin;
use App\Models\User;
use App\Models\Pembukuan;
use App\Models\Booking; // Tambahkan ini agar tidak perlu menulis alamat lengkap
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Peringatan "Simplified" hilang karena kita langsung panggil nama Modelnya
        $totalPaket = Paket::count();
        $totalDekorasi = Dekorasi::count(); 
        $totalPelanggan = Booking::distinct('customer_name')->count();
        $totalBooking = Booking::count();

        // Statistik Finansial
        $netIncome = Pembukuan::where('tipe', 'pemasukan')->sum('nominal') -
                     Pembukuan::where('tipe', 'pengeluaran')->sum('nominal');

        // Sisa tagihan
        $pendingPayment = Booking::all()->sum('sisa_tagihan');

        // Aktivitas terbaru
        $recentBookings = Booking::latest()->take(5)->get();

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
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        return $map[$month] ?? $month;
    }
}