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
        'notes',
        'status',
        'another_column_name' // WAJIB DITAMBAHKAN agar ID User tersimpan
    ];

    protected $casts = [
        'package_price' => 'integer',
        'event_date' => 'date',
    ];

    protected $appends = ['total_bayar', 'sisa_tagihan'];

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'booking_id');
    }

    public function getTotalBayarAttribute()
    {
        return (int) $this->pembayarans()
            ->where('status', 'valid')
            ->sum('jumlah_bayar');
    }

    public function getSisaTagihanAttribute()
    {
        $sisa = (int) $this->package_price - (int) $this->total_bayar;
        return $sisa < 0 ? 0 : $sisa;
    }
}
