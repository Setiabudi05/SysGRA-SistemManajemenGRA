<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pembayaran; // Pastikan Model Pembayaran di-import
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index()
    {
        // Pastikan memuat relasi paket dan addOns
        $bookings = Booking::with(['paket', 'addOns', 'pembayarans'])
            ->where('user_id', Auth::id())
            ->whereIn('status', [
                'pending',
                'PENDING',
                'confirmed',
                'CONFIRMED',
                'success',
                'SUCCESS',
                'terkonfirmasi',
                'TERKONFIRMASI',
                'failed',
                'FAILED'
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Tambahkan logika hitung total di sini jika ingin lebih bersih
        $bookings->each(function ($booking) {
            $hargaPaket = (float) ($booking->paket->harga ?? 0);
            $totalAddons = (float) $booking->addOns->sum('harga');
            // Simpan ke atribut virtual agar mudah dipanggil di Blade
            $booking->total_bersih = $hargaPaket + $totalAddons;
        });

        return view('user.pembayaran.index', compact('bookings'));
    }
    public function uploadBukti(Request $request, $id)
    {
        // Validasi file agar hanya gambar yang diizinkan
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Cari pembayaran berdasarkan ID
        $pembayaran = Pembayaran::findOrFail($id);

        // Proses penyimpanan file
        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $filename = 'BUKTI_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Simpan ke public/bukti_transfer
            $file->move(public_path('bukti_transfer'), $filename);

            // Update database
            $pembayaran->update([
                'bukti_transfer' => $filename
            ]);
        }

        return redirect()->back()->with('success_bukti', 'Bukti pembayaran berhasil diupload!');
    }
}
