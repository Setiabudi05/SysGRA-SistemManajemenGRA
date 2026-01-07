<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- A. AUTHENTICATION CONTROLLERS ---
use App\Http\Controllers\Auth\SocialiteController;
// Pastikan controller di bawah ini ada jika Anda menggunakan sistem login dari proyek lama
// Jika menggunakan Laravel Breeze, biasanya sudah dihandle oleh auth.php

// --- B. ADMIN CONTROLLERS ---
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaketController;
use App\Http\Controllers\Admin\DekorasiController;
use App\Http\Controllers\Admin\JadwalPengantinController;
use App\Http\Controllers\Admin\JadwalDekorController;
use App\Http\Controllers\Admin\JadwalGownController;
use App\Http\Controllers\Admin\JadwalLayosController;
use App\Http\Controllers\Admin\BajuPengantinController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\PembukuanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;

// --- C. USER CONTROLLERS ---
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\BookingController as UserBookingController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\UserProfileController;

/*
|--------------------------------------------------------------------------
| 1. RUTE AUTENTIKASI (BREEZE & SOCIALITE)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

// RUTE GOOGLE LOGIN (Socialite)
Route::middleware('guest')->group(function () {
    // Mengarahkan ke Google
    Route::get('auth/google', [SocialiteController::class, 'redirectToProvider'])
        ->name('socialite.google.redirect');

    // Callback dari Google
    Route::get('auth/google/callback', [SocialiteController::class, 'handleProviderCallback'])
        ->name('socialite.google.callback');
});

/*
|--------------------------------------------------------------------------
| 2. RUTE PUBLIK (LANDING PAGE)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('user.index');
})->name('home');

Route::post('/booking/store', [UserBookingController::class, 'store'])->name('booking.store');

/*
|--------------------------------------------------------------------------
| 3. RUTE AKSES TERBATAS (USER LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
    Route::get('/keranjang-pesanan', [CartController::class, 'index'])->name('cart.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
});

/*
|--------------------------------------------------------------------------
| 4. RUTE ADMIN (ROLE: ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // CRUD Paket
        Route::prefix('paket')->name('paket.')->group(function () {
            Route::get('/', [PaketController::class, 'index'])->name('index');
            Route::get('/json', [PaketController::class, 'data'])->name('data');
            Route::get('/create', [PaketController::class, 'create'])->name('create');
            Route::post('/store', [PaketController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [PaketController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [PaketController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [PaketController::class, 'destroy'])->name('destroy');
        });

        // CRUD Dekorasi
        Route::prefix('dekorasi')->name('dekorasi.')->group(function () {
            Route::get('/', [DekorasiController::class, 'index'])->name('index');
            Route::get('/data', [DekorasiController::class, 'data'])->name('data');
            Route::get('/create', [DekorasiController::class, 'create'])->name('create');
            Route::post('/store', [DekorasiController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [DekorasiController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [DekorasiController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [DekorasiController::class, 'destroy'])->name('destroy');
        });

        // CRUD Baju Pengantin 
        Route::prefix('baju')->name('baju.')->group(function () {
            Route::get('/', [BajuPengantinController::class, 'index'])->name('index');
            Route::get('/data', [BajuPengantinController::class, 'data'])->name('data');
            Route::get('/create', [BajuPengantinController::class, 'create'])->name('create');
            Route::post('/store', [BajuPengantinController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [BajuPengantinController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [BajuPengantinController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [BajuPengantinController::class, 'destroy'])->name('destroy');
            Route::get('/print', [BajuPengantinController::class, 'print'])->name('print');
        });

        // Jadwal Pengantin
        Route::prefix('jadwalpengantin')->name('jadwalpengantin.')->group(function () {
            Route::get('/', [JadwalPengantinController::class, 'index'])->name('index');
            Route::get('/data', [JadwalPengantinController::class, 'data'])->name('data');
            Route::get('/create', [JadwalPengantinController::class, 'create'])->name('create');
            Route::post('/store', [JadwalPengantinController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [JadwalPengantinController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [JadwalPengantinController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [JadwalPengantinController::class, 'destroy'])->name('destroy');
            Route::get('/print', [JadwalPengantinController::class, 'print'])->name('print');
        });

        // Jadwal Dekor
        Route::prefix('jadwaldekor')->name('jadwaldekor.')->group(function () {
            Route::get('/', [JadwalDekorController::class, 'index'])->name('index');
            Route::get('/data', [JadwalDekorController::class, 'data'])->name('data');
            Route::get('/create', [JadwalDekorController::class, 'create'])->name('create');
            Route::post('/store', [JadwalDekorController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [JadwalDekorController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [JadwalDekorController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [JadwalDekorController::class, 'destroy'])->name('destroy');
            Route::get('/print', [JadwalDekorController::class, 'print'])->name('print');
        });

        // Jadwal Gown 
        Route::prefix('jadwalgown')->name('jadwalgown.')->group(function () {
            Route::get('/', [JadwalGownController::class, 'index'])->name('index');
            Route::get('/data', [JadwalGownController::class, 'data'])->name('data');
            Route::get('/create', [JadwalGownController::class, 'create'])->name('create');
            Route::post('/store', [JadwalGownController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [JadwalGownController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [JadwalGownController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [JadwalGownController::class, 'destroy'])->name('destroy');
            Route::get('/print', [JadwalGownController::class, 'print'])->name('print');
        });

        // Jadwal Layos 
        Route::prefix('jadwallayos')->name('jadwallayos.')->group(function () {
            Route::get('/', [JadwalLayosController::class, 'index'])->name('index');
            Route::get('/data', [JadwalLayosController::class, 'data'])->name('data');
            Route::get('/create', [JadwalLayosController::class, 'create'])->name('create');
            Route::post('/store', [JadwalLayosController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [JadwalLayosController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [JadwalLayosController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [JadwalLayosController::class, 'destroy'])->name('destroy');
            Route::get('/print', [JadwalLayosController::class, 'print'])->name('print');
        });

        // Pembukuan Keuangan
        Route::prefix('pembukuan')->name('pembukuan.')->group(function () {
            Route::get('/', [PembukuanController::class, 'index'])->name('index');
            Route::get('/pemasukan-data', [PembukuanController::class, 'pemasukanData'])->name('pemasukanData');
            Route::get('/pengeluaran-data', [PembukuanController::class, 'pengeluaranData'])->name('pengeluaranData');
            Route::get('/get-saldo', [PembukuanController::class, 'getSaldo'])->name('getSaldo');
            Route::get('/create/pemasukan', [PembukuanController::class, 'createPemasukan'])->name('createPemasukan');
            Route::get('/create/pengeluaran', [PembukuanController::class, 'createPengeluaran'])->name('createPengeluaran');
            Route::post('/store', [PembukuanController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [PembukuanController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [PembukuanController::class, 'update'])->name('update');
            Route::get('/print', [PembukuanController::class, 'print'])->name('print');
            Route::delete('/destroy/{id}', [PembukuanController::class, 'destroy'])->name('destroy');
        });

        // Management Users
        Route::get('users/json', [UserController::class, 'data'])->name('users.data');
        Route::resource('users', UserController::class);

        // --- MANAGEMENT PESANAN (BOOKING) ---
        Route::prefix('booking')->name('booking.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index'); // admin.booking.index
            Route::get('/data', [BookingController::class, 'data'])->name('data');
            Route::get('/create', [BookingController::class, 'create'])->name('create');
            Route::post('/store', [BookingController::class, 'store'])->name('store');
            Route::get('/{id}/detail', [BookingController::class, 'show'])->name('show');
            Route::put('/{id}/status', [BookingController::class, 'updateStatus'])->name('updateStatus');
            Route::delete('/{id}', [BookingController::class, 'destroy'])->name('destroy');
        });
        // --- MANAGEMENT PEMBAYARAN (PISAHKAN DARI BOOKING) ---
        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::get('/', [PembayaranController::class, 'index'])->name('index'); // admin.pembayaran.index
            Route::get('/data', [PembayaranController::class, 'data'])->name('data'); // admin.pembayaran.data
            Route::put('/{id}/status', [PembayaranController::class, 'updateStatus'])->name('status'); // admin.pembayaran.status

            // Perbaikan Baris Ini (Cukup .nota)
            Route::get('/{id}/nota', [PembayaranController::class, 'cetakNota'])->name('nota'); // admin.pembayaran.nota
        });
    });
});