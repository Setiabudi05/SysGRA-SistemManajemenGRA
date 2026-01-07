<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembukuan extends Model
{
    use HasFactory;

    protected $table = 'pembukuan';

    protected $fillable = [
        'tanggal',
        'tipe',
        'customer',
        'keterangan',
        'nominal',
    ];
}
