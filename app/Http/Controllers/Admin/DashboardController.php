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
        // Statistik Utama (Kartu Atas)
        $totalPaket = Paket::count();
        $totalDekorasi = Dekorasi::count();
        
        // Mengambil jumlah user dengan role 'USER' agar sinkron dengan menu Kelola User
        $totalPelanggan = User::where('role', 'USER')->count();
        
        // Mengambil total jadwal (Booking)
        $totalBooking = JadwalPengantin::count();

        // Statistik Keuangan (Sidebar Kanan)
        $totalPemasukan = Pembukuan::where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = Pembukuan::where('tipe', 'pengeluaran')->sum('nominal');
        $pendapatanBersih = $totalPemasukan - $totalPengeluaran;

        // Ambil data jadwal bulan berjalan untuk tabel dashboard
        $bulanIndo = $this->getBulanIndo(Carbon::now()->format('F'));
        $tahun = Carbon::now()->year;

        $jadwalSekarang = JadwalPengantin::where('bulan', $bulanIndo)
            ->where('tahun', $tahun)
            ->orderBy('tanggal_awal', 'asc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPaket', 
            'totalDekorasi', 
            'totalPelanggan', 
            'totalBooking', 
            'pendapatanBersih',
            'jadwalSekarang'
        ));
    }

    private function getBulanIndo($month) {
        $map = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        return $map[$month] ?? $month;
    }
}