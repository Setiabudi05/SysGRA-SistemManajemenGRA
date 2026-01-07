<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan ini ada

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  ...$roles  // Menerima satu atau banyak role (e.g., 'admin', 'user')
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Periksa apakah pengguna sudah login
        if (!Auth::check()) {
            // Jika belum login, lempar ke halaman login
            return redirect('login');
        }

        // 2. Ambil data pengguna yang sedang login
        $user = Auth::user();

        // 3. Periksa apakah role pengguna ada di dalam daftar $roles yang diizinkan
        if (in_array($user->role, $roles)) {
            // 4. Jika diizinkan (role-nya cocok), lanjutkan ke halaman yang dituju
            return $next($request);
        }

        // 5. Jika tidak diizinkan, tampilkan halaman "403 Forbidden"
        // Ini adalah praktik terbaik.
        abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
    }
}