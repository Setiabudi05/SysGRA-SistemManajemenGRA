<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'whatsapp_number',
        'bride_groom_name',
        'parent_name',
        'facebook_name',
        'instagram_name',
        'event_address',
        'event_date',
        'event_duration',
        'package_name',
        'package_price',
        'add_ons',
        'notes', // Tambahkan ini agar catatan bisa tersimpan
        'status'
    ];

    protected $casts = [
        'package_price' => 'integer',
        'event_date' => 'date',
    ];

    // Menghubungkan accessor ke model agar bisa dipanggil langsung di Blade
    protected $appends = ['total_bayar', 'sisa_tagihan'];

    // Tambahkan relasi ke tabel Pembayaran
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'booking_id');
    }

    // Menghitung total semua cicilan yang sudah masuk
    public function getTotalTerbayarAttribute()
    {
        return (int) $this->pembayarans()->where('status_pembayaran', 'valid')->sum('jumlah_bayar');
    }

    // Mengambil total bayar (hanya yang valid)
    // Ganti bagian ini di Model Booking Anda
    public function getTotalBayarAttribute()
    {
        return (int) $this->pembayarans()
            ->where('status', 'valid') // Ubah dari 'status_pembayaran' menjadi 'status'
            ->sum('jumlah_bayar');
    }
    // Kalkulasi Sisa Tagihan
    public function getSisaTagihanAttribute()
    {
        // Menghitung selisih harga paket dengan total bayar
        $sisa = (int) $this->package_price - (int) $this->total_bayar;
        return $sisa < 0 ? 0 : $sisa;
    }
}
