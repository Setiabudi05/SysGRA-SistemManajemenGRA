<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'pembayarans';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'booking_id',
        'bukti_transfer',
        'jumlah_bayar',
        'status_pembayaran',
        'catatan_admin',
    ];

    /**
     * Relasi Balik ke Booking
     * Satu pembayaran dimiliki oleh satu booking.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Accessor untuk mendapatkan URL foto bukti transfer yang valid
     */
    public function getBuktiTransferUrlAttribute()
    {
        if ($this->bukti_transfer) {
            return asset('storage/' . $this->bukti_transfer);
        }
        return asset('assets/images/no-image.png'); // Pastikan ada gambar default jika tidak ada foto
    }
}