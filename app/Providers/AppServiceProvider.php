<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Tambahan wajib agar fungsi URL::forceScheme dikenali

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Sinkronisasi waktu lokal untuk pencatatan jadwal pengantin
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');

        // 2. Paksa semua aset CSS/JS menggunakan HTTPS jika diakses lewat jalur Ngrok
        if (str_contains(request()->getHttpHost(), 'ngrok-free.dev')) {
            URL::forceScheme('https');
        }
    }
}