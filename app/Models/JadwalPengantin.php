<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPengantin extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'paket_id',
        'tanggal_awal',
        'tanggal_akhir',
        'bulan',
        'tahun',
        'asisten',
        'fg',
        'layos',
        'keterangan',
    ];

    // Relasi ke Paket
    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }

    /**
    /**
     * Relasi ke model Pembayaran menggunakan kolom booking_id
     */
    public function pembayarans()
    {
        // Kita arahkan foreign key-nya ke 'booking_id'
        return $this->hasMany(Pembayaran::class, 'booking_id');
    }
    public function jadwalDekor()
    {
        return $this->hasOne(JadwalDekor::class, 'jadwal_pengantin_id');
    }

    public function jadwalGown()
    {
        return $this->hasOne(JadwalGown::class, 'jadwal_pengantin_id');
    }
    public function jadwalLayos()
    {
        // Sesuaikan foreign key jika bukan 'jadwal_pengantin_id'
        return $this->hasOne(JadwalLayos::class, 'jadwal_pengantin_id');
    }
}
