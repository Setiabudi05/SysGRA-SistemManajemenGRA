<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; // Tambahkan ini
use Illuminate\Http\Response; // Tambahkan ini

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Jika request BUKAN dari AJAX dan user tidak terotentikasi,
        // alihkan ke rute 'login' dan kirimkan pesan 'status'.
        if (! $request->expectsJson()) {
            // Kita gunakan Redirect::route() sebagai ganti helper route()
            return Redirect::route('login')->with('status', 'Anda harus login untuk mengakses halaman ini.');
        }

        return null;
    }
}