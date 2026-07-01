<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BajuPengantin extends Model
{
    use HasFactory;
    protected $fillable = [
        'paket', // Ubah dari paket_id menjadi paket
        'nama_gown',
        'foto_gown',
        'deskripsi_gown',
        'stok'
    ];
}
