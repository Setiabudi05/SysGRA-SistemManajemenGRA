<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- A. AUTHENTICATION CONTROLLERS ---
use App\Http\Controllers\Auth\SocialiteController;

// --- B. OWNER CONTROLLERS (Manajerial & Pembagian Kru) ---
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\BookingController as OwnerBookingController;
use App\Http\Controllers\Owner\PembayaranController as OwnerPembayaranController;
use App\Http\Controllers\Owner\LaporanPembukuanController;
use App\Http\Controllers\Owner\ProfileController;
use App\Http\Controllers\Owner\UserController as OwnerUserController;
use App\Http\Controllers\Owner\JadwalPengantinController as OwnerJadwalPengantin;
use App\Http\Controllers\Owner\JadwalDekorController as OwnerJadwalDekor;
use App\Http\Controllers\Owner\JadwalLayosController as OwnerJadwalLayos;

// --- C. ADMIN CONTROLLERS (Operasional & Master Data) ---
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaketController;
use App\Http\Controllers\Admin\DekorasiController;
use App\Http\Controllers\Admin\JadwalPengantinController;
use App\Http\Controllers\Admin\JadwalDekorController;
use App\Http\Controllers\Admin\JadwalGownController;
use App\Http\Controllers\Admin\BajuPengantinController;
use App\Http\Controllers\Admin\AddOnController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\PembukuanController;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaranController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;

// --- D. USER / PELANGGAN CONTROLLERS ---
use App\Http\Controllers\User\BookingController as PelangganBooking;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\ProfileController as PelangganProfile;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\LandingController;
use App\Http\Controllers\User\PembayaranController as PelangganBayar;
use App\Http\Controllers\User\ChatbotController;

// --- E. KRU CONTROLLERS ---
use App\Http\Controllers\Kru\DashboardController as KruDashboardController;
use App\Http\Controllers\Kru\JadwalPengantinController as KruJadwal;
use App\Http\Controllers\Kru\RiwayatController;
use App\Http\Controllers\Kru\ProfileController as KruProfileController;

/*
|--------------------------------------------------------------------------
| 1. RUTE AUTENTIKASI & REDIRECT CERDAS
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

Route::middleware('guest')->group(function () {
    Route::get('auth/google', [SocialiteController::class, 'redirectToProvider'])->name('socialite.google.redirect');
    Route::get('auth/google/callback', [SocialiteController::class, 'handleProviderCallback'])->name('socialite.google.callback');
});

// Redirect Beranda Berdasarkan Role
Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::check() ? Auth::user()->role : null;
        return match ($role) {
            'owner'     => redirect()->route('owner.dashboard'),
            'admin'     => redirect()->route('admin.dashboard'),
            'kru'       => redirect()->route('kru.dashboard'),
            'pelanggan' => redirect()->route('user.dashboard'),
            default     => redirect()->route('user.dashboard'),
        };
    }
    return app(LandingController::class)->index();
})->name('landing');

Route::get('/home', function () {
    $role = Auth::user()->role;
    return match ($role) {
        'owner'     => redirect()->route('owner.dashboard'),
        'admin'     => redirect()->route('admin.dashboard'),
        'kru'       => redirect()->route('kru.dashboard'),
        'pelanggan' => redirect()->route('user.dashboard'),
        default     => redirect()->route('user.dashboard'),
    };
})->name('home');


/*
|--------------------------------------------------------------------------
| SPECIAL NOTIFICATION ROUTE (Universal - Bisa Diakses Semua Role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notification/{id}/read', [KruJadwal::class, 'readNotification'])->name('notification.read');
    Route::post('/notification/{id}/respond', [KruDashboardController::class, 'respondNotification'])->name('notification.respond');

    // PERBAIKAN UTAMA: Mendaftarkan nama rute notification.all agar layout navigasi tidak error 500
    Route::get('/notifications/all', function () {
        return back()->with('info', 'Riwayat log notifikasi sistem.');
    })->name('notification.all');
});

/*
|--------------------------------------------------------------------------
| 2. RUTE OWNER (Prefix 'owner.') - STRATEGIS & PEMBAGIAN KRU
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('users/json', [OwnerUserController::class, 'data'])->name('users.data');
    Route::resource('users', OwnerUserController::class);
    Route::get('/laporan-pembukuan', [LaporanPembukuanController::class, 'index'])->name('pembukuan.index');
    Route::get('/pembukuan/print', [LaporanPembukuanController::class, 'print'])->name('pembukuan.print');

    Route::prefix('jadwalpengantin')->name('jadwalpengantin.')->group(function () {
        Route::get('/', [OwnerJadwalPengantin::class, 'index'])->name('index');
        Route::get('/data', [OwnerJadwalPengantin::class, 'data'])->name('data');
        Route::get('/print', [OwnerJadwalPengantin::class, 'print'])->name('print');
        Route::get('/{id}/edit', [OwnerJadwalPengantin::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [OwnerJadwalPengantin::class, 'update'])->name('update');
        Route::get('/check-kru', [OwnerJadwalPengantin::class, 'checkKruAvailability'])->name('check-kru');
        Route::get('/check-status', [OwnerJadwalPengantin::class, 'checkKruStatus'])->name('check-status');
    });

    Route::prefix('jadwaldekor')->name('jadwaldekor.')->group(function () {
        Route::get('/', [OwnerJadwalDekor::class, 'index'])->name('index');
        Route::get('/data', [OwnerJadwalDekor::class, 'data'])->name('data');
        Route::get('/print', [OwnerJadwalDekor::class, 'print'])->name('print');
    });

    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/', [OwnerBookingController::class, 'index'])->name('index');
        Route::get('/data', [OwnerBookingController::class, 'data'])->name('data');
        Route::get('/{id}/detail', [OwnerBookingController::class, 'show'])->name('show');
        Route::get('/print', [OwnerBookingController::class, 'print_all'])->name('print_all');
        Route::get('/print/{id}', [OwnerBookingController::class, 'print'])->name('print');
    });

    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('/', [OwnerPembayaranController::class, 'index'])->name('index');
        Route::get('/data', [OwnerPembayaranController::class, 'data'])->name('data');
        Route::get('/{id}/histori', [OwnerPembayaranController::class, 'histori'])->name('histori');
        // GUNAKAN RUTE INI:
        Route::get('/nota/{pembayaran_id}', [OwnerPembayaranController::class, 'cetakNota'])->name('nota');
        Route::delete('/{id}', [OwnerPembayaranController::class, 'destroy'])->name('destroy');
    });

    Route::get('/profile', [App\Http\Controllers\Owner\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [App\Http\Controllers\Owner\ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| 3. RUTE KRU (Prefix 'kru.')
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:kru'])->prefix('kru')->name('kru.')->group(function () {
    Route::get('/dashboard', [KruDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [KruDashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/profile', [KruProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [KruProfileController::class, 'update'])->name('profile.update');

    // Rute Jadwal Tugas GRA
    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/', [KruJadwal::class, 'index'])->name('index');
        Route::get('/data', [KruJadwal::class, 'data'])->name('data');
        Route::get('/print', [KruJadwal::class, 'print'])->name('print');
    });

    // Rute Jadwal Pribadi (Disini kita perbaiki agar tidak double prefix/name)
    // Sekarang route-nya akan menjadi: kru.jadwal.pribadi.index, dsb.
    Route::prefix('jadwal/pribadi')->name('jadwal.pribadi.')->group(function () {
        Route::get('/', [App\Http\Controllers\Kru\JadwalPribadiController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Kru\JadwalPribadiController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Kru\JadwalPribadiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\Kru\JadwalPribadiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Kru\JadwalPribadiController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Kru\JadwalPribadiController::class, 'destroy'])->name('destroy');
        Route::get('/data', [App\Http\Controllers\Kru\JadwalPribadiController::class, 'getData'])->name('data');
    });

    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/', [RiwayatController::class, 'index'])->name('index');
        Route::get('/data', [RiwayatController::class, 'data'])->name('data');
        Route::get('/print', [RiwayatController::class, 'print'])->name('print');
    });

    Route::get('/job-detail/{id}', function ($id) {
        return "Halaman Detail Pekerjaan ID: " . $id;
    })->name('job.detail');
});
/*
|--------------------------------------------------------------------------
| 4. RUTE USER / PELANGGAN (Prefix 'user.')
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:pelanggan'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\User\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/booking', [PelangganBooking::class, 'index'])->name('booking');
    Route::post('/booking/store', [PelangganBooking::class, 'storeToBooking'])->name('booking.store');

    Route::get('/keranjang', [PelangganBooking::class, 'keranjang'])->name('keranjang');
    Route::delete('/keranjang/{id}', [PelangganBooking::class, 'destroy'])->name('keranjang.destroy');
    Route::post('/keranjang/konfirmasi', [PelangganBooking::class, 'konfirmasi'])->name('keranjang.konfirmasi');
    Route::get('/keranjang/detail/{id}', [PelangganBooking::class, 'show'])->name('keranjang.show');

    // ==============================================================================
    // RUTE PEMBAYARAN & UPLOAD BUKTI (Sisi Pelanggan)
    // ==============================================================================
    Route::get('/pembayaran', [PelangganBayar::class, 'index'])->name('pembayaran');
    Route::post('/pembayaran/upload-bukti/{id}', [PelangganBayar::class, 'uploadBukti'])->name('pembayaran.upload-bukti');
    Route::get('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

    Route::get('/riwayat', [OrderController::class, 'riwayat'])->name('riwayat');

    Route::get('/profile', [PelangganProfile::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [PelangganProfile::class, 'update'])->name('profile.update');

    // ==============================================================================
    // RUTE CETAK PDF MANDIRI (Sisi Pelanggan)
    // ==============================================================================
    Route::get('/booking/print/{id}', [AdminBookingController::class, 'print'])->name('booking.print');
    Route::get('/pembayaran/nota/{id}', [AdminPembayaranController::class, 'cetakNota'])->name('pembayaran.cetakNota');
});
/*
|--------------------------------------------------------------------------
| 5. RUTE ADMIN (Prefix 'admin.') - OPERASIONAL (Admin & Owner)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');

    Route::prefix('paket')->name('paket.')->group(function () {
        Route::get('/', [PaketController::class, 'index'])->name('index');
        Route::get('/json', [PaketController::class, 'data'])->name('data');
        Route::get('/create', [PaketController::class, 'create'])->name('create');
        Route::post('/store', [PaketController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PaketController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PaketController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [PaketController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('dekorasi')->name('dekorasi.')->group(function () {
        Route::get('/', [DekorasiController::class, 'index'])->name('index');
        Route::get('/print', [DekorasiController::class, 'print'])->name('print');
        Route::get('/data', [DekorasiController::class, 'data'])->name('data');
        Route::get('/create', [DekorasiController::class, 'create'])->name('create');
        Route::post('/store', [DekorasiController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [DekorasiController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [DekorasiController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [DekorasiController::class, 'destroy'])->name('destroy');
    });

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

    // RUTE ADD-ONS (TAMBAHAN)
    Route::prefix('addons')->name('addons.')->group(function () {
        Route::get('/', [AddOnController::class, 'index'])->name('index');
        Route::get('/data', [AddOnController::class, 'data'])->name('data');
        Route::get('/create', [AddOnController::class, 'create'])->name('create');
        Route::post('/store', [AddOnController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [AddOnController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [AddOnController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [AddOnController::class, 'destroy'])->name('destroy');
    });

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

    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/', [AdminBookingController::class, 'index'])->name('index');
        Route::get('/data', [AdminBookingController::class, 'data'])->name('data');
        Route::get('/create', [AdminBookingController::class, 'create'])->name('create');
        Route::post('/store', [AdminBookingController::class, 'store'])->name('store');
        Route::get('/{id}/detail', [AdminBookingController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AdminBookingController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [AdminBookingController::class, 'update'])->name('update');
        Route::put('/{id}/status', [AdminBookingController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/destroy/{id}', [AdminBookingController::class, 'destroy'])->name('destroy');
        Route::get('/print/{id}', [AdminBookingController::class, 'print'])->name('print');
    });

    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('/', [AdminPembayaranController::class, 'index'])->name('index');
        Route::get('/data', [AdminPembayaranController::class, 'data'])->name('data');
        Route::get('/create', [AdminPembayaranController::class, 'create'])->name('create');
        Route::post('/store', [AdminPembayaranController::class, 'store'])->name('store');
        Route::get('/{id}/histori', [AdminPembayaranController::class, 'histori'])->name('histori');
        Route::get('/{id}/nota', [AdminPembayaranController::class, 'cetakNota'])->name('nota');
        Route::delete('/{id}', [AdminPembayaranController::class, 'destroy'])->name('destroy');
        Route::post('/upload-bukti/{id}', [AdminPembayaranController::class, 'uploadBukti'])->name('upload-bukti');
    });
});

// PERBAIKAN: Jalur Webhook Web Midtrans Terisolasi Bebas CSRF (Dipanggil diluar pembungkus auth middleware)
Route::post('midtrans/notification', [\App\Http\Controllers\User\CheckoutController::class, 'notificationHandler'])->name('midtrans.notification');
