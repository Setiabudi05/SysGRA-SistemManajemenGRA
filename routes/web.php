<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- A. AUTHENTICATION CONTROLLERS ---
use App\Http\Controllers\Auth\SocialiteController;

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
use App\Http\Controllers\User\OrderController;

/*
|--------------------------------------------------------------------------
| 1. RUTE AUTENTIKASI (BREEZE & SOCIALITE)
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

// RUTE GOOGLE LOGIN (Socialite)
Route::middleware('guest')->group(function () {
    Route::get('auth/google', [SocialiteController::class, 'redirectToProvider'])
        ->name('socialite.google.redirect');

    Route::get('auth/google/callback', [SocialiteController::class, 'handleProviderCallback'])
        ->name('socialite.google.callback');
});

/*
|--------------------------------------------------------------------------
| 2. RUTE PUBLIK (Kini Terproteksi)
|--------------------------------------------------------------------------
*/
// Rute Home kini wajib verifikasi agar user dipaksa cek Gmail dulu
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return view('user.index');
    })->name('home');
});

// Rute ini tetap bisa diakses untuk memproses booking (jika logic store Anda mengizinkan)
Route::post('/booking/store', [UserBookingController::class, 'store'])->name('booking.store');
/*
|--------------------------------------------------------------------------
| 3. RUTE AKSES TERBATAS (USER LOGIN)
|--------------------------------------------------------------------------
*/
// USER (PENGANTIN) TETAP WAJIB VERIFIKASI
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
    Route::get('/keranjang-pesanan', [CartController::class, 'index'])->name('cart.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    // PESANAN SAYA
    Route::get('/pesanan-saya', [OrderController::class, 'index'])->name('pesanan.index');
    Route::post('/pesanan/upload-bukti/{id}', [OrderController::class, 'uploadBukti'])->name('pesanan.upload');
});

/*
|--------------------------------------------------------------------------
| 4. RUTE ADMIN (ROLE: ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- PROFIL ADMIN ---
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');

    // --- MASTER DATA ---

    // Paket
    Route::prefix('paket')->name('paket.')->group(function () {
        Route::get('/', [PaketController::class, 'index'])->name('index');
        Route::get('/json', [PaketController::class, 'data'])->name('data');
        Route::get('/create', [PaketController::class, 'create'])->name('create');
        Route::post('/store', [PaketController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PaketController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PaketController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [PaketController::class, 'destroy'])->name('destroy');
    });

    // Dekorasi
    Route::prefix('dekorasi')->name('dekorasi.')->group(function () {
        Route::get('/', [DekorasiController::class, 'index'])->name('index');
        Route::get('/data', [DekorasiController::class, 'data'])->name('data');
        Route::get('/create', [DekorasiController::class, 'create'])->name('create');
        Route::post('/store', [DekorasiController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [DekorasiController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [DekorasiController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [DekorasiController::class, 'destroy'])->name('destroy');
    });

    // Baju Pengantin
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

    // --- JADWAL OPERASIONAL ---

    // Jadwal Pengantin
    Route::prefix('jadwalpengantin')->name('jadwalpengantin.')->group(function () {
        Route::get('/', [JadwalPengantinController::class, 'index'])->name('index');
        Route::get('/data', [JadwalPengantinController::class, 'data'])->name('data');
        Route::get('/create', [JadwalPengantinController::class, 'create'])->name('create');
        Route::post('/store', [JadwalPengantinController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [JadwalPengantinController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [JadwalPengantinController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [JadwalPengantinController::class, 'destroy'])->name('destroy');
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

    // --- KEUANGAN & PEMBUKUAN ---

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

    // --- MANAJEMEN TRANSAKSI ---

    // Booking (Pesanan)
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/data', [BookingController::class, 'data'])->name('data');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/store', [BookingController::class, 'store'])->name('store');
        Route::get('/{id}/detail', [BookingController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [BookingController::class, 'update'])->name('update');
        Route::put('/{id}/status', [BookingController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [BookingController::class, 'destroy'])->name('destroy');
        Route::get('/print/{id}', [BookingController::class, 'print'])->name('print');
    });

    // Pembayaran & Cicilan
    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('/', [PembayaranController::class, 'index'])->name('index');
        Route::get('/data', [PembayaranController::class, 'data'])->name('data');
        Route::get('/create', [PembayaranController::class, 'create'])->name('create');
        Route::post('/store', [PembayaranController::class, 'store'])->name('store');
        Route::get('/{id}/histori', [PembayaranController::class, 'histori'])->name('histori');
        Route::get('/{id}/nota', [PembayaranController::class, 'cetakNota'])->name('nota');
        Route::delete('/{id}', [PembayaranController::class, 'destroy'])->name('destroy');
    });

    // --- SYSTEM USERS ---
    Route::get('users/json', [UserController::class, 'data'])->name('users.data');
    Route::resource('users', UserController::class);
});

use Illuminate\Support\Facades\Mail;

Route::get('/tes-email', function () {
    try {
        Mail::raw('Halo SysGRA! Email ini dikirim untuk tes koneksi SMTP.', function ($message) {
            $message->to('stiabdii2@gmail.com') // <--- Isi email Anda sendiri
                ->subject('Tes SMTP SysGRA Berhasil');
        });
        return "Email berhasil dikirim! Cek inbox atau spam.";
    } catch (\Exception $e) {
        return "Gagal mengirim email. Error: " . $e->getMessage();
    }
});
