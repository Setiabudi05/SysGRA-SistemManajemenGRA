<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'whatsapp_number',
        'bride_groom_name',
        'parent_name',
        'facebook_name',
        'instagram_name',
        'event_address',
        'event_date',
        'event_duration',
        'paket_id',
        'package_name',
        'package_price',
        'add_ons',
        'notes',
        'status',
        'another_column_name'
    ];

    protected $casts = [
        'package_price' => 'integer',
        'event_date' => 'date',
    ];

    protected $appends = ['total_bayar', 'sisa_tagihan'];

    // app/Models/Booking.php

    // app/Models/Booking.php

    protected static function booted()
    {
        static::updated(function ($booking) {
            if (in_array($booking->status, ['CONFIRMED', 'COMPLETED'])) {
                $date = \Carbon\Carbon::parse($booking->event_date);

                \App\Models\JadwalPengantin::updateOrCreate(
                    ['pesanan_id' => $booking->id],
                    [
                        'nama'         => $booking->bride_groom_name,
                        'alamat'       => $booking->event_address,
                        'paket_id'     => $booking->paket_id,
                        'tanggal_awal' => $booking->event_date,
                        'bulan'        => $date->format('F'),
                        'tahun'        => $date->format('Y'),
                        // Hanya ambil catatan pesanan saja, status dihapus
                        'keterangan'   => $booking->notes ?? '-',
                        'is_manual'    => 0
                    ]
                );
            }
        });
    }

    // Relasi ke JadwalPengantin
    public function jadwal()
    {
        return $this->hasOne(JadwalPengantin::class, 'pesanan_id');
    }
    // app/Models/Booking.php
    public function paket()
    {
        // Menghubungkan booking ke tabel paket melalui paket_id
        return $this->belongsTo(Paket::class, 'paket_id', 'id');
    }

    /**
     * Relasi ke model Pembayaran (One to Many)
     */
    public function pembayarans()
    {
        // Diselaraskan menggunakan 'pesanan_id' sesuai foreign key di Class Diagram
        return $this->hasMany(Pembayaran::class, 'pesanan_id');
    }

    public function getTotalBayarAttribute()
    {
        // PERBAIKAN: Hitung semua yang status_pembayaran-nya BUKAN 'pending'
        return (int) $this->pembayarans()->where('status_pembayaran', '!=', 'pending')->sum('jumlah_bayar');
    }

    public function getSisaTagihanAttribute()
    {
        $sisa = (int) $this->package_price - $this->total_bayar;
        return $sisa < 0 ? 0 : $sisa;
    }
}
