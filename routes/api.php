<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rute bawaan Laravel untuk mengambil data user login (biarkan tetap ada)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * --------------------------------------------------------------------------
 * RUTE WEBHOOK GATEWAY MIDTRANS (SysGRA)
 * --------------------------------------------------------------------------
 * Rute ini ditaruh di api.php agar BEBAS dari proteksi CSRF Token Laravel.
 * URL akses fisik via Ngrok: https://link-ngrok-kamu.ngrok-free.dev/api/midtrans/notification
 */
Route::post('/midtrans/notification', [\App\Http\Controllers\User\CheckoutController::class, 'notificationHandler']);