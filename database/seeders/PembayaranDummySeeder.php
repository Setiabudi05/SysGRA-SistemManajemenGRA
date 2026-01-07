<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembayaran;
use App\Models\Booking;

class PembayaranDummySeeder extends Seeder
{
    public function run()
    {
        // Ambil data booking pertama sebagai contoh
        $booking = Booking::first();

        if ($booking) {
            // Dummy 1: Pembayaran DP
            Pembayaran::create([
                'booking_id' => $booking->id,
                'bukti_transfer' => 'dummy_dp.jpg',
                'jumlah_bayar' => 2000000,
                'status_pembayaran' => 'valid',
                'catatan_admin' => 'DP Lunas untuk kunci tanggal'
            ]);

            // Dummy 2: Cicilan Fitting
            Pembayaran::create([
                'booking_id' => $booking->id,
                'bukti_transfer' => 'dummy_cicilan.jpg',
                'jumlah_bayar' => 3000000,
                'status_pembayaran' => 'pending',
                'catatan_admin' => 'Cicilan pas fitting baju'
            ]);
        }
    }
}