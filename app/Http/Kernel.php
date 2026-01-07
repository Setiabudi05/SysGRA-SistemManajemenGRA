<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
    // ... middleware bawaan lainnya
    
    'role' => \App\Http\Middleware\CheckRole::class, // Tambahkan baris ini
];

}
