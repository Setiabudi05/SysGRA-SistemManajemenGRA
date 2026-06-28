<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    use HasFactory;

    // Menentukan tabel jika namanya tidak standar (Opsional, gunakan jika perlu)
    protected $table = 'add_ons';

    // Mengizinkan kolom untuk diisi secara massal
    protected $fillable = [
        'nama_item', 
        'deskripsi', 
        'harga'
    ];

// Pastikan di kedua Model (Booking & AddOn)
public function addOns()
{
    return $this->belongsToMany(AddOn::class, 'add_ons_booking', 'booking_id', 'add_on_id');
}
}