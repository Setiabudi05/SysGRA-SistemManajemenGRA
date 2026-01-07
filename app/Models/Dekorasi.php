<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dekorasi extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'paket_id',
        'foto',
        'deskripsi'
    ];

    // Relasi: dekorasi milik satu paket
    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }
}
