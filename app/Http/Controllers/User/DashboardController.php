<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil ID User yang sedang login saat ini (Naila)
        $userId = Auth::id();

        // 1. HITUNG TOTAL PESANAN: Disamakan dengan variabel di view blade ($total_pesanan)
        $total_pesanan = Booking::where('user_id', $userId)->count();

        // 2. AMBIL STATUS TERAKHIR PESANAN
        $pesananTerakhir = Booking::where('user_id', $userId)->latest('created_at')->first();
        $status_terakhir = $pesananTerakhir ? strtoupper($pesananTerakhir->status) : 'BELUM ADA';

        // 3. HITUNG SISA TAGIHAN SECARA REAL-TIME DARI TABEL PEMBAYARAN
        $sisa_tagihan = 0;
        if ($pesananTerakhir) {
            $hargaPaket = (int) $pesananTerakhir->package_price;

            $totalTerbayar = DB::table('pembayarans')
                ->where('pesanan_id', $pesananTerakhir->id)
                ->where(function ($q) {
                    $q->whereRaw('LOWER(status_pembayaran) = ?', ['success'])
                      ->orWhereRaw('LOWER(status_pembayaran) = ?', ['lunas'])
                      ->orWhereRaw('LOWER(status_pembayaran) = ?', ['confirmed'])
                      ->orWhereNull('status_pembayaran');
                })
                ->sum('jumlah_bayar');

            $hitungSisa = $hargaPaket - (int) $totalTerbayar;
            $sisa_tagihan = $hitungSisa < 0 ? 0 : $hitungSisa;
        }

        // Passing semua variabel dengan nama yang sinkron 100% menggunakan garis bawah
        return view('user.dashboard', compact('total_pesanan', 'status_terakhir', 'sisa_tagihan'));
    }
}