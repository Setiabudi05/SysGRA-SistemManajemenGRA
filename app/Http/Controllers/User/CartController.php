<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // Fungsi ini akan dipanggil saat user klik ikon keranjang
    public function index()
    {
        // 1. Ambil semua item dari session 'cart'
        $cartItems = Session::get('cart', []);
        
        // 2. Hitung total harga
        $total = 0;
        foreach ($cartItems as $item) {
            // Kita perlu membersihkan 'Rp' dan 'Jt' untuk menghitung
            // Hati-hati jika ada format harga lain (misal "Rp 23.500.000")
            // Kode ini mengasumsikan format "Rp 23 Jt"
            $priceString = str_replace(['Rp ', ' Jt', '.'], '', $item['package_price']);
            $price = (float)$priceString * 1000000; // Asumsi 'Jt' = Juta
            $total += $price;
        }

        // 3. DITAMBAHKAN: Hitung minimal DP (50% dari total)
        $minDp = $total * 0.5;

        // 4. Tampilkan view keranjang dan kirim datanya
        return view('user.keranjang', [
            'cartItems'  => $cartItems,
            'totalPrice' => $total,
            'minDp'      => $minDp // <-- DITAMBAHKAN: Kirim minDp ke view
        ]);
    }
}