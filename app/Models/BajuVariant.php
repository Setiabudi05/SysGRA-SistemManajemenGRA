<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BajuVariant extends Model
{
    use HasFactory;

    // Nama tabel detail yang baru kita rancang
    protected $table = 'baju_variants';

    protected $fillable = [
        'baju_id', 
        'warna', 
        'ukuran', 
        'stok'
    ];

    /**
     * Relasi ke Master Baju
     */
    public function baju()
    {
        return $this->belongsTo(Baju::class, 'baju_id');
    }
}