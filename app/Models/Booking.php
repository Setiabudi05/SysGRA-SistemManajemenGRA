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
        'status'
    ];

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'booking_id');
    }

    // Menghitung total yang sudah dibayar (Hanya status VALID)
    public function getTotalBayarAttribute()
    {
        return $this->pembayarans()->where('status_pembayaran', 'valid')->sum('jumlah_bayar');
    }

    // Menghitung sisa tagihan
    public function getSisaTagihanAttribute()
    {
        // Pastikan package_price bersih dari titik/karakter non-numeric jika disimpan sebagai string
        $price = (int) preg_replace('/[^0-9]/', '', $this->package_price);
        return $price - $this->total_bayar;
    }

}