<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $pakets = Paket::all();
        return view('user.booking.index', compact('pakets'));
    }

    public function storeToBooking(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir.');
        }

        $request->validate([
            'customer_name'     => 'required|string|max:255',
            'whatsapp_number'   => 'required|string|max:255',
            'bride_groom_name'  => 'required|string|max:255',
            'paket_id'          => 'required|exists:pakets,id',
            'event_date_range'  => 'required|string',
            'event_address'     => 'required',
        ]);

        try {
            $paket = Paket::findOrFail($request->paket_id);
            $dateRangeString = $request->event_date_range;
            $duration = 1;

            if (str_contains($dateRangeString, ' to ')) {
                $dates = explode(' to ', $dateRangeString);
                $startDate = Carbon::parse($dates[0]);
                $endDate = Carbon::parse($dates[1]);
                $duration = $startDate->diffInDays($endDate) + 1;
                $eventDate = $startDate->format('Y-m-d');
            } else {
                $eventDate = Carbon::parse($dateRangeString)->format('Y-m-d');
            }

            // Validasi Kuota
            $maxQuotaPerDay = 5;
            $totalJobOnDay = Booking::whereIn('status', ['pending', 'confirmed', 'success'])
                ->where('event_date', $eventDate)
                ->count();

            if ($totalJobOnDay >= $maxQuotaPerDay) {
                return back()->withErrors(['event_date_range' => 'Kuota penuh untuk tanggal tersebut.'])->withInput();
            }

            // PERBAIKAN: Gunakan 'user_id' bukan 'another_column_name'
            Booking::create([
                'customer_name'     => $request->customer_name,
                'whatsapp_number'   => $request->whatsapp_number,
                'bride_groom_name'  => $request->bride_groom_name,
                'parent_name'       => $request->parent_name,
                'facebook_name'     => $request->facebook_name,
                'instagram_name'    => $request->instagram_name,
                'event_address'     => $request->event_address,
                'event_date'        => $eventDate,
                'event_duration'    => $duration,

                // Keduanya disimpan
                'paket_id'          => $request->paket_id,
                'package_name'      => $paket->nama_paket,
                'package_price'     => (string) $paket->harga,

                'notes'             => $request->notes,
                'status'            => 'draft',
                'user_id'           => Auth::id(),
            ]);

            return redirect()->route('user.keranjang')->with('success_booking', 'Berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->withErrors(['event_date_range' => 'Gagal: ' . $e->getMessage()])->withInput();
        }
    }

    public function keranjang()
    {
        // PERBAIKAN: Gunakan 'user_id'
        $carts = Booking::where('user_id', Auth::id())
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.keranjang.index', compact('carts'));
    }

    public function konfirmasi()
    {
        // PERBAIKAN: Gunakan 'user_id'
        $cartItems = Booking::where('user_id', Auth::id())
            ->where('status', 'draft')
            ->get();

        $maxQuotaPerDay = 5;

        foreach ($cartItems as $item) {
            $eventDate = Carbon::parse($item->event_date)->format('Y-m-d');
            $totalJobOnDay = Booking::whereIn('status', ['pending', 'confirmed', 'success'])
                ->where('event_date', $eventDate)
                ->count();

            if ($totalJobOnDay >= $maxQuotaPerDay) {
                return redirect()->route('user.keranjang')->withErrors(['msg' => 'Gagal checkout! Kuota penuh.']);
            }
        }

        // PERBAIKAN: Gunakan 'user_id'
        Booking::where('user_id', Auth::id())
            ->where('status', 'draft')
            ->update(['status' => 'pending']);

        return redirect()->route('user.pembayaran')->with('success', 'Konfirmasi berhasil!');
    }
}
