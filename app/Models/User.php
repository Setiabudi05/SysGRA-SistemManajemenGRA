<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// 1. IMPORT NOTIFIKASI KUSTOM ANDA DISINI
use App\Notifications\ResetPasswordNotificationCustom;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * 2. TAMBAHKAN FUNGSI INI UNTUK MENGIRIM EMAIL RESET KUSTOM
     * Fungsi ini akan menimpa (override) bawaan Laravel agar menggunakan 
     * template Bahasa Indonesia yang sudah Anda buat di ResetPasswordNotificationCustom.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotificationCustom($token));
    }
}