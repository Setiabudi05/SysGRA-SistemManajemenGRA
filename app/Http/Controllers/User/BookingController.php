<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index() {
        $pakets = Paket::all();
        return view('user.booking.index', compact('pakets'));
    }

    public function storeToBooking(Request $request) {
        $request->validate([
            'customer_name'     => 'required|string|max:255',
            'whatsapp_number'   => 'required|string|max:255',
            'bride_groom_name'  => 'required|string|max:255',
            'paket_id'          => 'required|exists:pakets,id',
            'event_date_range'  => 'required',
            'event_address'     => 'required',
        ]);

        try {
            $paket = Paket::findOrFail($request->paket_id);
            $dateInput = $request->event_date_range;
            $eventDate = str_contains($dateInput, ' to ') ? explode(' to ', $dateInput)[0] : $dateInput;

            Booking::create([
                'customer_name'     => $request->customer_name,
                'whatsapp_number'   => $request->whatsapp_number,
                'bride_groom_name'  => $request->bride_groom_name,
                'parent_name'       => $request->parent_name,
                'facebook_name'     => $request->facebook_name,
                'instagram_name'    => $request->instagram_name,
                'event_address'     => $request->event_address,
                'event_date'        => $eventDate,
                'event_duration'    => (int) ($request->event_duration ?? 1),
                'package_name'      => $paket->nama_paket,
                'package_price'     => (string) $paket->harga,
                'notes'             => $request->notes,
                'status'            => 'draft', // Status awal masuk keranjang
                'another_column_name' => Auth::id(), 
            ]);

            return redirect()->route('user.keranjang')->with('success_booking', 'Berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => $e->getMessage()])->withInput();
        }
    }

    public function keranjang() {
        $carts = Booking::where('another_column_name', Auth::id())
            ->where('status', 'draft') // Filter untuk menu keranjang
            ->orderBy('created_at', 'desc')
            ->get();
        return view('user.keranjang.index', compact('carts'));
    }

    /**
     * Mengubah status dari Draft ke Pending (Tagihan Aktif)
     */
    public function konfirmasi() {
        Booking::where('another_column_name', Auth::id())
            ->where('status', 'draft')
            ->update(['status' => 'pending']);
        return redirect()->route('user.pembayaran')->with('success', 'Konfirmasi berhasil! Tagihan Anda sudah aktif.');
    }

    /**
     * Logic Otomatisasi: Digunakan saat Midtrans memberikan notifikasi sukses
     */
    public function markAsConfirmed($id) {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'confirmed']); // Sah menjadi pesanan terjadwal
        
        return redirect()->route('user.riwayat')->with('success', 'Pembayaran Terverifikasi! Pesanan Anda telah dikonfirmasi.');
    }
}