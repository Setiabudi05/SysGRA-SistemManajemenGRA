<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotificationCustom;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. TAMBAHKAN BARIS INI

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes; // 2. TAMBAHKAN SoftDeletes DI SINI
    
    protected $fillable = [
        'name',
        'jabatan',
        'email',
        'phone',
        'address', 
        'password',
        'role',
        'email_verified_at'
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