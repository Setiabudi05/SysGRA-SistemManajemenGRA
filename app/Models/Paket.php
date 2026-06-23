<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    use HasFactory;

    protected $fillable = ['nama_paket', 'tahun', 'makeup', 'dekorasi', 'dokumentasi', 'harga', 'include', 'bonus'];

    public function dekorasis()
    {
        return $this->hasMany(Dekorasi::class);
    }
    public function bajuPengantins()
    {
        return $this->hasMany(BajuPengantin::class, 'paket_id');
    }
}
