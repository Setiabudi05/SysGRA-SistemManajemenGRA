<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 
        'jumlah_bayar', 
        'keterangan', 
        'status'
    ];

    // Relasi balik ke Booking
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}