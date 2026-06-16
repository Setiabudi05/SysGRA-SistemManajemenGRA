<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function index()
    {
        // PERBAIKAN MUTLAK: Mengubah 'another_column_name' menjadi 'user_id' agar mengunci data Naila
        $bookings = Booking::where('user_id', Auth::id())
                    ->whereIn('status', [
                        'pending', 'PENDING', 
                        'confirmed', 'CONFIRMED', 
                        'success', 'SUCCESS', 
                        'terkonfirmasi', 'TERKONFIRMASI',
                        'failed', 'FAILED'
                    ]) 
                    ->orderBy('updated_at', 'desc')
                    ->get();

        // Melempar variabel 'bookings' secara aman ke view pelanggan
        return view('user.pembayaran.index', compact('bookings'));
    }
}