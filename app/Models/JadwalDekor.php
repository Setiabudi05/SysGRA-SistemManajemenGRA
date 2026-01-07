<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalDekor extends Model
{
    use HasFactory;

    protected $table = 'jadwal_dekors';

    protected $fillable = [
        'jadwal_pengantin_id',
        'bulan',
        'tahun',
        'nama',
        'alamat',
        'paket_id',
        'foto',
        'deskripsi',
    ];

    // Relasi ke tabel Paket
    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }

    // ✅ Tambahkan relasi ke JadwalPengantin
    public function jadwalPengantin()
    {
        return $this->belongsTo(JadwalPengantin::class, 'jadwal_pengantin_id');
    }
}
