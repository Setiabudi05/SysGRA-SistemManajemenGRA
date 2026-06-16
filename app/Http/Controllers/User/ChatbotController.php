<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class CheckoutController extends Controller
{
    /**
     * Set inisialisasi konfigurasi dasar Midtrans Sandbox
     */
    private function initMidtrans()
    {
        Config::$serverKey = config('midtrans.server_key') ?? env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Memproses permintaan Snap Token untuk Nominal DP / Cicilan Tertentu
     */
    public function process(Request $request)
    {
        $this->initMidtrans();

        try {
            $bookingId = $request->id;
            $nominalBayar = $request->nominal_pembayaran;

            $booking = Booking::where('id', $bookingId)
                ->whereIn('status', ['pending', 'menunggu verifikasi', 'terkonfirmasi'])
                ->firstOrFail();

            if (!$nominalBayar) {
                $nominalBayar = $booking->status == 'pending' ? 5000000 : $booking->package_price;
            }

            $params = [
                'transaction_details' => [
                    'order_id' => 'GRA-' . $booking->id . '-' . time(),
                    'gross_amount' => (int) $nominalBayar,
                ],
                'customer_details' => [
                    'first_name' => $booking->customer_name,
                    'phone' => $booking->whatsapp_number,
                ],
                'item_details' => [
                    [
                        'id' => $booking->id,
                        'price' => (int) $nominalBayar,
                        'quantity' => 1,
                        'name' => 'Cicilan SysGRA ID #' . $booking->id,
                    ]
                ]
            ];

            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $params['transaction_details']['order_id']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menangani Webhook/Callback Notifikasi Otomatis dari Midtrans via Ngrok
     */
    /**
     * Menangani Webhook/Callback Notifikasi Otomatis dari Midtrans via Ngrok
     */
    public function notificationHandler(Request $request)
    {
        try {
            $payload = $request->all();

            info('Midtrans Webhook Masuk:', $payload);

            $orderIdRaw = $payload['order_id'] ?? '';
            $transaction = $payload['transaction_status'] ?? '';

            $orderIdParts = explode('-', $orderIdRaw);
            $bookingId = null;

            if (count($orderIdParts) >= 2) {
                if ($orderIdParts[0] === 'GRA') {
                    $bookingId = $orderIdParts[1];
                } else {
                    $bookingId = end($orderIdParts);
                }
            } else {
                $bookingId = $orderIdRaw;
            }

            if ($bookingId && is_numeric($bookingId)) {

                $booking = Booking::find($bookingId);

                if ($booking) {
                    // JIKA TRANSAKSI SUKSES (SETTLEMENT / CAPTURE)
                    if ($transaction == 'settlement' || $transaction == 'capture') {

                        // 1. Hitung histori cicilan yang sudah masuk sebelumnya
                        $jumlahTransaksiSebelumnya = DB::table('pembayarans')
                            ->where('pesanan_id', $booking->id)
                            ->count();

                        $totalTerbayarSebelumnya = DB::table('pembayarans')
                            ->where('pesanan_id', $booking->id)
                            ->sum('jumlah_bayar');

                        // 2. Ambil nominal uang dari Midtrans sekarang
                        $nominalSekarang = (int) ($payload['gross_amount'] ?? 0);

                        // 3. Hitung sisa tagihan real-time
                        $sisaTagihanSekarang = $booking->package_price - ($totalTerbayarSebelumnya + $nominalSekarang);

                        // 4. LOGIKA DETEKSI KETERANGAN OTOMATIS
                        if ($sisaTagihanSekarang <= 0) {
                            $keteranganOtomatis = 'Pelunasan otomatis via Midtrans Sandbox';
                        } elseif ($jumlahTransaksiSebelumnya == 0) {
                            $keteranganOtomatis = 'DP Awal otomatis via Midtrans Sandbox';
                        } else {
                            $keBerapa = $jumlahTransaksiSebelumnya + 1;
                            $keteranganOtomatis = 'Cicilan ke-' . $keBerapa . ' otomatis via Midtrans Sandbox';
                        }

                        // 5. Ubah status booking jika ini pembayaran pertama (DP)
                        if ($booking->status == 'pending') {
                            $booking->status = 'menunggu verifikasi';
                            $booking->save();
                        }

                        // 6. INJECT OTOMATIS: Mengisi kolom baru sesuai struktur phpMyAdmin kamu
                        DB::table('pembayarans')->insert([
                            'pesanan_id'        => $booking->id,
                            'jumlah_bayar'      => $nominalSekarang,
                            'keterangan'        => $keteranganOtomatis,
                            'status_pembayaran' => 'Lunas', // <-- Mengisi kolom status_pembayaran barumu
                            'bukti_transfer'    => null,   // <-- Sudah aman karena diset Nullable
                            'catatan_admin'     => 'Sistem Otomatis Midtrans Gateway',
                            'created_at'        => now(),
                            'updated_at'        => now(),
                        ]);

                        return response()->json(['status' => 'success', 'message' => 'Otomatisasi Berhasil: ' . $keteranganOtomatis]);
                    }

                    // JIKA TRANSAKSI GAGAL
                    elseif (in_array($transaction, ['deny', 'expire', 'cancel'])) {
                        if ($booking->status == 'pending') {
                            $booking->status = 'failed';
                            $booking->save();
                        }
                        return response()->json(['status' => 'success', 'message' => 'Transaksi gagal.']);
                    }
                }
            }

            return response()->json(['status' => 'ignored', 'message' => 'Order ID tidak cocok.'], 200);
        } catch (\Exception $e) {
            logger()->error('Eror Webhook Midtrans: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }
}
