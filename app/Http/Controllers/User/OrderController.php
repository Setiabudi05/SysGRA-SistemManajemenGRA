<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking; // Pastikan Model Booking sudah ada sesuai projek SysGRA
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index()
    {
        // Mengambil histori pesanan user yang sedang login
        $pesanan = Booking::where('user_id', Auth::id())
                          ->orderBy('created_at', 'desc')
                          ->get();

        return view('user.pesanan', compact('pesanan'));
    }

    public function uploadBukti(Request $request, $id)
    {
        // Validasi file foto bukti transfer
        $request->validate([
            'bukti_bayar' => 'required|image|mimes:jpg,png,jpeg|max:2048'
        ]);
        
        $booking = Booking::findOrFail($id);

        if ($request->hasFile('bukti_bayar')) {
            // Simpan file ke folder public/bukti_transfer
            $path = $request->file('bukti_bayar')->store('bukti_transfer', 'public');
            
            // Update status pesanan di database
            $booking->update([
                'bukti_pembayaran' => $path,
                'status' => 'Menunggu Konfirmasi'
            ]);
        }

        return back()->with('success', 'Bukti bayar berhasil diunggah! Mohon tunggu konfirmasi admin.');
    }
}