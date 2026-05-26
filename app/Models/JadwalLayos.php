<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalLayos extends Model
{
    use HasFactory;

    protected $table = 'jadwal_layos';

    protected $fillable = [
        'jadwal_pengantin_id',
        'bulan',
        'tahun',
        'layos',
    ];

    // Relasi ke JadwalPengantin
    public function pengantin()
    {
        return $this->belongsTo(JadwalPengantin::class, 'jadwal_pengantin_id');
    }

    public function jadwalPengantin()
    {
        return $this->belongsTo(JadwalPengantin::class, 'jadwal_pengantin_id');
    }
}
