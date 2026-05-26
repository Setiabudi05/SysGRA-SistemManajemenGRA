<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paket;
use App\Models\Dekorasi;
use App\Models\Booking;
use App\Models\Pembukuan;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Menggunakan nama class langsung (Simplified)
        $totalPaket = Paket::count();
        $totalDekorasi = Dekorasi::count(); 
        $totalPelanggan = Booking::distinct('customer_name')->count();
        $totalBooking = Booking::count();

        $netIncome = Pembukuan::where('tipe', 'pemasukan')->sum('nominal') -
                     Pembukuan::where('tipe', 'pengeluaran')->sum('nominal');

        $pendingPayment = Booking::all()->sum('sisa_tagihan');

        $recentBookings = Booking::latest()->take(5)->get();

        return view('owner.dashboard', compact(
            'totalPaket',
            'totalDekorasi',
            'totalPelanggan',
            'totalBooking',
            'netIncome',
            'pendingPayment',
            'recentBookings'
        ));
    }
}