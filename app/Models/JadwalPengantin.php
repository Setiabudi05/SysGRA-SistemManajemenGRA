<?php

namespace App\Models;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPengantin extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang diasosiasikan dengan model (Opsional).
     * Jika nama tabel di database adalah 'jadwal_pengantins', 
     * Laravel akan mengenalinya secara otomatis tanpa perlu deklarasi ini.
     */
    protected $table = 'jadwal_pengantins';

    protected $fillable = [
        'pesanan_id',
        'is_manual', // Tambahkan ini
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
        'keterangan'
    ];

    /**
     * Relasi ke model Paket
     */
    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }

    /**
     * Relasi ke model Booking (Pesanan)
     */
    public function pesanan()
    {
        return $this->belongsTo(Booking::class, 'pesanan_id', 'id');
    }
    /**
     * Relasi ke model Pembayaran menggunakan kolom booking_id
     */
    // app/Models/JadwalPengantin.php
    public function pembayarans()
    {
        // 'pesanan_id' sesuai dengan nama kolom di tabel pembayarans
        return $this->hasMany(Pembayaran::class, 'pesanan_id', 'id');
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
        return $this->hasOne(JadwalLayos::class, 'jadwal_pengantin_id');
    }
}
