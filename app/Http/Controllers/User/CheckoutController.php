<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Booking; // <-- Model yang akan kita gunakan
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Memproses checkout dari keranjang.
     * 1. Ambil data dari Session.
     * 2. Simpan ke database (tabel bookings).
     * 3. Hapus session.
     * 4. Redirect ke halaman 'sukses' atau 'payment'.
     */
    public function process(Request $request)
    {
        // 1. Validasi input DP
        $request->validate([
            'down_payment' => 'required|numeric|min:1000' // Contoh validasi DP
        ]);

        // 2. Ambil data dari keranjang (Session)
        $cartItems = Session::get('cart', []);

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        // 3. Mulai "Transaksi" Database
        DB::beginTransaction();

        try {
            $createdBookings = []; // Untuk menyimpan booking yang baru dibuat

            // 4. Looping setiap item di keranjang dan simpan ke database
            foreach ($cartItems as $item) {
                $booking = new Booking();
                
                // Isi data dari session
                $booking->customer_name = $item['customer_name'];
                $booking->whatsapp_number = $item['whatsapp_number'];
                $booking->bride_groom_name = $item['bride_groom_name'] ?? null;
                $booking->parent_name = $item['parent_name'] ?? null;
                $booking->facebook_name = $item['facebook_name'] ?? null;
                $booking->instagram_name = $item['instagram_name'] ?? null;
                $booking->event_address = $item['event_address'];
                $booking->event_date = $item['event_date'];
                $booking->event_duration = $item['event_duration'] ?? null;
                $booking->package_name = $item['package_name'];
                $booking->package_price = $item['package_price'];
                $booking->add_ons = $item['add_ons'] ?? null;
                $booking->gown_notes = $item['gown_notes'] ?? null;
                $booking->other_notes = $item['other_notes'] ?? null;
                
                // (PENTING) Ambil data DP dari form
                $booking->down_payment = $request->down_payment;
                $booking->status = 'pending'; // Status awal
                
                $booking->save();
                
                $createdBookings[] = $booking;
            }

            // 5. Jika semua berhasil disimpan, konfirmasi transaksi
            DB::commit();

            // 6. Kosongkan keranjang
            Session::forget('cart');

            // 7. Arahkan ke halaman sukses/invoice
            // Nanti Anda perlu membuat route dan view untuk 'booking.success'
            // return redirect()->route('booking.success', ['id' => $createdBookings[0]->id]);
            
            // Untuk SEKARANG, kita redirect ke home dengan pesan sukses
            return redirect('/user/index')->with('success', 'Booking Anda telah dikonfirmasi! Silakan cek WA untuk detail pembayaran DP.');


        } catch (\Exception $e) {
            // 8. Jika ada error, batalkan semua yang sudah disimpan
            DB::rollBack();
            Log::error('Gagal Checkout: ' . $e->getMessage());

            return redirect()->route('cart.index')->with('error', 'Terjadi kesalahan saat checkout. Silakan coba lagi.');
        }
    }
}