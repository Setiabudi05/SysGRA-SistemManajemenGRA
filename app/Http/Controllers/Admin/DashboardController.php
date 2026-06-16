<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Paket;
use App\Models\Dekorasi;
use App\Models\Booking;
use App\Models\JadwalPengantin;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data Statistik (Total Angka)
        $totalPaket = Paket::count();
        $totalDekorasi = Dekorasi::count(); 
        $totalPelanggan = Booking::distinct('bride_groom_name')->count();
        $totalBooking = Booking::count();

        // 2. Data untuk Tabel Booking
        $allBookings = Booking::latest()->get();

        // 3. Data Jadwal Operasional Mendatang (Bulan berjalan)
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        $upcomingSchedules = JadwalPengantin::whereMonth('tanggal_awal', $currentMonth)
            ->whereYear('tanggal_awal', $currentYear)
            ->orderBy('tanggal_awal', 'asc')
            ->get();

        // 4. Data untuk Donut Chart
        $statusCounts = Booking::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Mengirimkan semua variabel ke view admin.dashboard
        return view('admin.dashboard', compact(
            'totalPaket', 
            'totalDekorasi', 
            'totalPelanggan', 
            'totalBooking', 
            'allBookings', 
            'upcomingSchedules', 
            'statusCounts'
        ));
    }
}