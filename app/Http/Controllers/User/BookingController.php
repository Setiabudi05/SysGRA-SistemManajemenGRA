<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session; // <-- Pastikan ini ada

class BookingController extends Controller
{
    /**
     * Menyimpan data booking ke SESSION (Keranjang)
     */
    public function store(Request $request)
    {
        // 1. Validasi data tetap sama
        $validator = Validator::make($request->all(), [
            'customer_name'   => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'event_date'      => 'required|date',
            'event_address'   => 'required|string',
            'package_name'    => 'required|string',
            'package_price'   => 'required|string',
            'agreement'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validasi gagal. Pastikan semua data wajib diisi.'
            ], 422);
        }

        // --- Ini adalah logika "Add to Cart" ---

        // 2. Ambil semua data form, kecuali token dan agreement
        $bookingData = $request->except('_token', 'agreement');
        
        // 3. Beri ID unik untuk item keranjang (opsional tapi bagus)
        $bookingData['cart_item_id'] = uniqid('booking_'); 

        // 4. Masukkan data booking ini ke dalam array 'cart' di Session
        //    MENGGUNAKAN 'put' UNTUK MENGGANTI, BUKAN 'push'
        //
        //    Ini adalah PERBAIKAN UTAMA.
        //    Kita bungkus $bookingData dengan [ ] agar 'cart' selalu
        //    menjadi array yang HANYA berisi 1 item booking baru.
        Session::put('cart', [$bookingData]);

        // 5. Hitung jumlah item di keranjang (sekarang akan selalu 1)
        $cartCount = count(Session::get('cart', []));

        // 6. Kirim respons JSON baru ke JavaScript
        return response()->json([
            'success'     => true,
            'message'     => 'Berhasil ditambahkan ke keranjang!',
            'cart_count'  => $cartCount // <-- Kirim jumlah item terbaru
        ]);
    }
}