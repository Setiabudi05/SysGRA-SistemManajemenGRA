<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ChatbotController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rute bawaan Laravel untuk mengambil data user login
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * --------------------------------------------------------------------------
 * RUTE WEBHOOK GATEWAY MIDTRANS (SysGRA)
 * --------------------------------------------------------------------------
 */
Route::post('/midtrans/notification', [\App\Http\Controllers\User\CheckoutController::class, 'notificationHandler']);

/**
 * --------------------------------------------------------------------------
 * RUTE CHATBOT AI (GEMINI)
 * --------------------------------------------------------------------------
 */
Route::post('/chat', [ChatbotController::class, 'chat']);