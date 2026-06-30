<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotificationCustom;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    

    protected $fillable = [
        'name',
        'jabatan',
        'email',
        'phone',
        'address', // Tambahkan kolom alamat di sini (sesuaikan dengan nama kolom di database Anda)
        'password',
        'role',
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

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotificationCustom($token));
    }
}