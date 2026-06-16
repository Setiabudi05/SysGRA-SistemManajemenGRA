<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pembukuan;
use App\Models\Booking; // Pastikan import ini ada

class Pembayaran extends Model
{
    protected $fillable = [
        'pesanan_id',
        'jumlah_bayar',
        'keterangan',
        'status_pembayaran',
        'bukti_transfer',
        'catatan_admin'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($pembayaran) {
            // PERBAIKAN: Menggunakan relasi yang di-refresh untuk memastikan data booking ada
            $booking = $pembayaran->booking()->first();

            Pembukuan::create([
                'tanggal'       => now()->toDateString(),
                'tipe'          => 'pemasukan',
                // Perbaikan: Jika booking tidak ditemukan, kita ambil dari keterangan/input lain
                'customer'      => $booking ? $booking->bride_groom_name : 'Pelanggan Online',
                'keterangan'    => $pembayaran->keterangan ?? 'Pembayaran melalui Midtrans',
                'nominal'       => (float) $pembayaran->jumlah_bayar,
                'pembayaran_id' => $pembayaran->id
            ]);
        });
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'pesanan_id');
    }

    // WAJIB: Tambahkan ini agar perintah doesntHave('pembukuan') di Tinker berfungsi!
    public function pembukuan()
    {
        return $this->hasOne(Pembukuan::class, 'pembayaran_id');
    }
}
