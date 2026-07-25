<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    // Pastikan nama tabel benar jika tidak mengikuti konvensi jamak
    protected $table = 'jadwals';

    // Kolom yang boleh diisi (Mass Assignable)
    protected $fillable = [
        'user_id', 
        'event_date', 
        'event_date',
        'tipe',         // Kolom baru untuk GRA/EKSTERNAL
        'nama_vendor',  // Kolom baru untuk nama vendor luar
        'keterangan',    // Kolom baru untuk catatan
    ];

    // Opsional: Jika ingin menambahkan relasi ke User (Kru)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}