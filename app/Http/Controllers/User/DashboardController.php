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
        $userId = Auth::id();

        $total_pesanan = Booking::where('user_id', $userId)->count();

        $pesananTerakhir = Booking::where('user_id', $userId)->latest('created_at')->first();
        $status_terakhir = $pesananTerakhir ? strtoupper($pesananTerakhir->status) : 'BELUM ADA';

        $sisa_tagihan = 0;
        if ($pesananTerakhir) {
            // MENGAMBIL TOTAL HARGA FINAL DARI DATABASE (SUDAH TERMASUK ADD-ONS)
            $hargaTotal = (int) $pesananTerakhir->total_harga;

            $totalTerbayar = DB::table('pembayarans')
                ->where('pesanan_id', $pesananTerakhir->id)
                ->where(function ($q) {
                    $q->whereRaw('LOWER(status_pembayaran) = ?', ['success'])
                        ->orWhereRaw('LOWER(status_pembayaran) = ?', ['lunas'])
                        ->orWhereRaw('LOWER(status_pembayaran) = ?', ['confirmed'])
                        ->orWhereNull('status_pembayaran');
                })
                ->sum('jumlah_bayar');

            $hitungSisa = $hargaTotal - (int) $totalTerbayar;
            $sisa_tagihan = $hitungSisa < 0 ? 0 : $hitungSisa;
        }

        return view('user.dashboard', compact('total_pesanan', 'status_terakhir', 'sisa_tagihan'));
    }
}
