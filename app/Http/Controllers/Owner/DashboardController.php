<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pembukuan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // 1. Occupancy Rate (Menggunakan event_date)
        $daysInMonth = $now->daysInMonth;
        $bookedDays = Booking::whereMonth('event_date', $now->month)
            ->whereYear('event_date', $now->year)
            ->distinct('event_date')
            ->count('event_date');
        $occupancyRate = ($bookedDays / $daysInMonth) * 100;

        // 2. Customer Acquisition
        $newCustomers = Booking::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        // 3. Financial Health (Pemasukan - Pengeluaran)
        $income = Pembukuan::where('tipe', 'pemasukan')->whereMonth('tanggal', $now->month)->sum('nominal');
        $expense = Pembukuan::where('tipe', 'pengeluaran')->whereMonth('tanggal', $now->month)->sum('nominal');
        $netProfit = $income - $expense;

        // 4. Pending Receivables
        // Karena kolom sisa_tagihan tidak ada, kita lewati dulu atau hitung dari kolom lain jika ada.
        $pendingReceivables = 0;

        return view('owner.dashboard', compact(
            'occupancyRate',
            'newCustomers',
            'netProfit',
            'pendingReceivables'
        ));
    }
}
