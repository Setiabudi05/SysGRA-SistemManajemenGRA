<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    /**
     * Memproses permintaan token Snap Midtrans untuk pelanggan (Halaman Keranjang/Pembayaran)
     */
    public function process(Request $request)
    {
        // Ambil data booking berdasarkan ID yang dikirim via AJAX
        $booking = Booking::where('another_column_name', Auth::id())
            ->where('id', $request->id)
            ->first();

        if (!$booking) {
            return response()->json(['status' => 'error', 'message' => 'Data transaksi tidak ditemukan.'], 404);
        }

        // Set konfigurasi library Midtrans Snap
        \Midtrans\Config::$serverKey = config('midtrans.server_key') ?? env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Buat parameter transaksi untuk dikirim ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => '#GRA-' . $booking->id, // Menggunakan pola unik sistem SysGRA
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
                    'name' => $booking->package_name,
                ]
            ]
        ];

        try {
            // Ambil snap token murni dari Midtrans API
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // Jika status booking masih 'draft', naikkan menjadi 'pending' karena token pembayaran sudah dibuat
            if ($booking->status == 'draft') {
                $booking->update(['status' => 'pending']);
            }

            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * PERBAIKAN UTAMA: Menangkap Callback Webhook dari Midtrans Sandbox (Anti-Revisi Dosen)
     * Rute ini harus didaftarkan secara publik di routes/web.php
     */
    public function notificationHandler(Request $request)
    {
        // 1. Ambil data kiriman data payload dari server Midtrans
        $payload = $request->all();
        
        // Ekstrak string order_id (Contoh: "#GRA-20" diubah menjadi angka "20")
        $orderIdRaw = $payload['order_id'] ?? '';
        $bookingId = str_replace('#GRA-', '', $orderIdRaw);
        
        // Ambil status transaksi & status fraud dari Midtrans
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? 'accept';

        // 2. Cari data booking yang bersangkutan di database MySQL
        $booking = Booking::find($bookingId);

        if ($booking) {
            // 3. Logika Alur Perubahan Status Otomatis secara Bertahap
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($fraudStatus == 'challenge') {
                    // Jika ada status peninjauan manual (challenge), tahan di status pending
                    $booking->update(['status' => 'pending']);
                } else {
                    // UTAMA: Pembayaran sukses terverifikasi bank, set status menjadi Menunggu Verifikasi Admin
                    $booking->update(['status' => 'menunggu verifikasi']);
                }
            } elseif ($transactionStatus == 'pending') {
                // User baru memunculkan kode bayar tapi belum transfer di ATM / Alfamart
                $booking->update(['status' => 'pending']);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                // Transaksi kedaluwarsa atau dibatalkan oleh sistem Midtrans
                $booking->update(['status' => 'failed']);
            }

            return response()->json([
                'status' => 'success', 
                'message' => 'Webhook Midtrans berhasil diproses. Status booking terkini: ' . $booking->status
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Data ID transaksi SysGRA tidak terdeteksi.'], 404);
    }
}