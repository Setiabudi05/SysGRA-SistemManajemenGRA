<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string ...$roles  Daftar role yang diizinkan (e.g., 'admin', 'editor')
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Periksa apakah pengguna sudah login DAN apakah role pengguna TIDAK diizinkan.
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            // Jika tidak login atau role tidak cocok, kembalikan HTTP 403 (Unauthorized)
            abort(403, 'Unauthorized.');
        }

        // Jika lolos pengecekan, lanjutkan ke rute tujuan.
        return $next($request);
    }
}