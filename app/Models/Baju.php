<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Baju extends Model
{
    use HasFactory;

    protected $table = 'baju_pengantins';

    protected $fillable = [
    'kategori', 
    'warna', 
    'ukuran', 
    'stok', 
    'foto'
];
}