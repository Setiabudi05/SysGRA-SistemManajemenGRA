<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\Booking;
use App\Models\Pembayaran;
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
    /**
     * Memproses permintaan Snap Token untuk Nominal DP / Cicilan Tertentu
     */
    public function process(Request $request)
    {
        $this->initMidtrans();

        try {
            $bookingId = $request->id;

            // 1. Ambil data booking beserta relasi paketnya
            $booking = Booking::with('paket')->where('id', $bookingId)
                ->whereIn('status', ['pending', 'PENDING', 'menunggu verifikasi', 'terkonfirmasi', 'CONFIRMED', 'success'])
                ->firstOrFail();

            // 2. Tentukan nominal bayar
            $nominalBayar = $request->nominal_pembayaran;

            // Jika user tidak mengirim nominal manual (DP Awal)
            if (!$nominalBayar) {
                // Ambil nama paket, pastikan lower case agar mudah dicek
                $namaPaket = strtolower($booking->paket->nama_paket ?? '');

                // Logika DP: All-in = 5jt, Paket Lain = 2jt
                if (str_contains($namaPaket, 'all in') || str_contains($namaPaket, 'all-in')) {
                    $nominalBayar = 5000000;
                } else {
                    $nominalBayar = 2000000;
                }
            }

            // 3. Susun parameter Midtrans
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
    public function notificationHandler(Request $request)
    {
        try {
            $payload = $request->all();
            info('Midtrans Webhook Masuk:', $payload);

            $orderIdRaw = $payload['order_id'] ?? '';
            $transaction = $payload['transaction_status'] ?? '';

            // Ekstraksi ID Booking
            $orderIdParts = explode('-', $orderIdRaw);
            $bookingId = ($orderIdParts[0] === 'GRA') ? $orderIdParts[1] : end($orderIdParts);

            if ($bookingId && is_numeric($bookingId)) {
                $booking = Booking::find($bookingId);

                if ($booking) {
                    if ($transaction == 'settlement' || $transaction == 'capture') {

                        // 1. Cek apakah transaksi sudah pernah diproses (mencegah duplikasi)
                        $sudahDiproses = Pembayaran::where('pesanan_id', $booking->id)
                            ->where('jumlah_bayar', (int)($payload['gross_amount'] ?? 0))
                            ->where('created_at', '>=', now()->subMinutes(5))
                            ->exists();

                        if (!$sudahDiproses) {
                            // 2. Hitung logika keterangan
                            $jumlahTransaksiSebelumnya = Pembayaran::where('pesanan_id', $booking->id)->count();
                            $nominalSekarang = (int) ($payload['gross_amount'] ?? 0);
                            $totalTerbayarSebelumnya = Pembayaran::where('pesanan_id', $booking->id)->sum('jumlah_bayar');
                            $sisaTagihanSekarang = $booking->package_price - ($totalTerbayarSebelumnya + $nominalSekarang);

                            if ($sisaTagihanSekarang <= 0) {
                                $keteranganOtomatis = 'Pelunasan otomatis via Midtrans';
                            } elseif ($jumlahTransaksiSebelumnya == 0) {
                                $keteranganOtomatis = 'DP Awal otomatis via Midtrans';
                            } else {
                                $keteranganOtomatis = 'Cicilan ke-' . ($jumlahTransaksiSebelumnya + 1) . ' otomatis via Midtrans';
                            }

                            // 3. UPDATE STATUS BOOKING
                            $booking->update(['status' => 'CONFIRMED']);

                            // 4. SIMPAN PEMBAYARAN (Ini akan memicu 'boot' di Model Pembayaran)
                            Pembayaran::create([
                                'pesanan_id'        => $booking->id,
                                'jumlah_bayar'      => $nominalSekarang,
                                'keterangan'        => $keteranganOtomatis,
                                'status_pembayaran' => 'success',
                                'bukti_transfer'    => null,
                            ]);

                            return response()->json(['status' => 'success', 'message' => 'Data berhasil dicatat otomatis.']);
                        }
                    }
                    // JIKA TRANSAKSI GAGAL
                    elseif (in_array($transaction, ['deny', 'expire', 'cancel'])) {
                        if (strtoupper($booking->status) == 'PENDING') {
                            $booking->update(['status' => 'failed']);
                        }
                        return response()->json(['status' => 'success', 'message' => 'Transaksi gagal.']);
                    }
                }
            }

            return response()->json(['status' => 'ignored', 'message' => 'ID tidak ditemukan.'], 200);
        } catch (\Exception $e) {
            logger()->error('Error Webhook: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }
}
