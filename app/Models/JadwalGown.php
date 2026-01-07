<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalGown extends Model
{
    use HasFactory;

    protected $fillable = [
        'jadwal_pengantin_id',
        'gown',
        'bulan',
        'tahun',
    ];

    // Relasi ke JadwalPengantin
    public function jadwalPengantin()
    {
        return $this->belongsTo(JadwalPengantin::class, 'jadwal_pengantin_id');
    }
}
