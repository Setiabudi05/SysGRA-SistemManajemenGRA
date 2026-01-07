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
        'bulan',   // ✅ tambahkan ini
        'tahun',
        'asisten', // kalau memang ada di tabel
        'fg',      // kalau memang ada di tabel
        'layos',   // kalau memang ada di tabel
    ];

    // Relasi ke Paket
    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }

    // Relasi ke JadwalGown
    public function jadwalGown()
    {
        return $this->hasOne(JadwalGown::class, 'jadwal_pengantin_id');
    }
}
