<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function index()
    {
        // Mengambil riwayat booking milik user yang login
        $payments = Booking::where('another_column_name', Auth::id())
                    ->orderBy('updated_at', 'desc')
                    ->get();

        return view('user.pembayaran.index', compact('payments'));
    }
}