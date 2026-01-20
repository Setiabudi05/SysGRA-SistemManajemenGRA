<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        // Mengambil data dari session dan mengubahnya menjadi Collection
        $cartItems = collect(Session::get('cart', [])); 
        
        $total = 0;
        foreach ($cartItems as $item) {
            // Membersihkan format string harga untuk perhitungan
            $priceString = str_replace(['Rp ', ' Jt', '.', ' '], '', $item['package_price']);
            $price = (float)$priceString * 1000000; 
            $total += $price;
        }

        // Menghitung minimal DP 50%
        $minDp = $total * 0.5;

        return view('user.keranjang', [
            'cartItems'  => $cartItems,
            'totalPrice' => $total,
            'minDp'      => $minDp
        ]);
    }
}