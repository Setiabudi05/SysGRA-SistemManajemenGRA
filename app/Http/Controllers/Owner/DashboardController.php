<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Pembukuan;
use App\Models\Pembayaran;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // 1. Occupancy Rate: Persentase hari yang ada acara di bulan ini
        $daysInMonth = $now->daysInMonth;
        $bookedDays = Booking::whereMonth('event_date', $now->month)
            ->whereYear('event_date', $now->year)
            ->distinct('event_date')
            ->count('event_date');
        
        $occupancyRate = ($daysInMonth > 0) ? ($bookedDays / $daysInMonth) * 100 : 0;

        // 2. Pesanan Aktif: Menghitung acara yang berlangsung di bulan berjalan
        // (Beban kerja operasional bulan ini)
        $pesananAktif = Booking::whereMonth('event_date', $now->month)
            ->whereYear('event_date', $now->year)
            ->count();

        // 3. Financial Health: Laba Bersih bulan ini (Pemasukan - Pengeluaran)
        $income = Pembukuan::where('tipe', 'pemasukan')->whereMonth('tanggal', $now->month)->sum('nominal');
        $expense = Pembukuan::where('tipe', 'pengeluaran')->whereMonth('tanggal', $now->month)->sum('nominal');
        $netProfit = $income - $expense;

        // 4. Pending Receivables: Piutang Tertunda (Total Tagihan - Total Pembayaran Masuk)
        $totalTagihan = Booking::sum('package_price');
        $totalTerbayar = Pembayaran::whereIn('status_pembayaran', ['success', 'lunas', null])
                                   ->sum('jumlah_bayar');

        $pendingReceivables = $totalTagihan - $totalTerbayar;

        return view('owner.dashboard', compact(
            'occupancyRate',
            'pesananAktif',
            'netProfit',
            'pendingReceivables'
        ));
    }

    public function allNotifications()
    {
        $allNotif = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('owner.notification.all', compact('allNotif'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->route('owner.notification.all');
    }
}