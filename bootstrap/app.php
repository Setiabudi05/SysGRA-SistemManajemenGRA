<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 1. Middleware alias bawaan kamu untuk cek role Admin/Owner/Pelanggan/Kru
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // 2. PERBAIKAN: Meloloskan rute webhook Midtrans dari blokir keamanan CSRF Token
        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
            '/midtrans/notification' // <-- TAMBAHKAN INI JUGA BIAR AMAN DUA-DUANYA, FIP!
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
