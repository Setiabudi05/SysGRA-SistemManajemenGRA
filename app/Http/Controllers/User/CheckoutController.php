<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config; 
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Perbaikan: Tambahkan parameter Request untuk menangkap ID dari tombol
     */
    public function process(Request $request) 
    {
        try {
            // 1. Ambil ID dari URL (?id=...)
            $bookingId = $request->id;

            // 2. Cari booking spesifik berdasarkan ID dan User ID
            $booking = Booking::where('another_column_name', Auth::id()) 
                        ->where('id', $bookingId)
                        ->where('status', 'pending')
                        ->firstOrFail();

            // 3. Siapkan Parameter Midtrans
            $params = [
                'transaction_details' => [
                    // Tambahkan timestamp agar Order ID selalu unik di setiap percobaan klik
                    'order_id' => 'GRA-' . $booking->id . '-' . time(),
                    'gross_amount' => (int) $booking->package_price,
                ],
                'customer_details' => [
                    'first_name' => $booking->customer_name,
                    'phone' => $booking->whatsapp_number,
                ],
                'item_details' => [
                    [
                        'id' => $booking->id,
                        'price' => (int) $booking->package_price,
                        'quantity' => 1,
                        'name' => 'Pembayaran ' . $booking->package_name,
                    ]
                ]
            ];

            // 4. Ambil Snap Token
            $snapToken = Snap::getSnapToken($params);
            
            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $params['transaction_details']['order_id']
            ]);

        } catch (\Exception $e) {
            // Jika terjadi error (misal: ID tidak ditemukan), kirim pesan error yang jelas
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}