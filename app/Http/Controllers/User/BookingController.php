<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\Booking;
use App\Models\AddOn;
use App\Models\User;
use App\Notifications\SistemNotifikasi;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class BookingController extends Controller
{
    public function index()
    {
        $pakets = Paket::where('tahun', 2026)->get();
        $addOns = AddOn::all(); // Mengambil semua add-ons untuk form
        return view('user.booking.index', compact('pakets', 'addOns'));
    }

    public function storeToBooking(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir.');
        }

        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'whatsapp_number'  => 'required|string|max:255',
            'bride_groom_name' => 'required|string|max:255',
            'paket_id'         => 'required|exists:pakets,id',
            'event_date_range' => 'required|string',
            'event_address'    => 'required',
            'addons'           => 'nullable|array',
            'addons.*'         => 'exists:add_ons,id',
        ]);

        try {
            // 1. Ambil data paket
            $paket = Paket::findOrFail($request->paket_id);

            // 2. Hitung Durasi (tetap dihitung untuk kebutuhan data simpan)
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

            // 3. LOGIKA HARGA FLAT (TIDAK ADA PENGALI DURASI)
            $hargaAddons = AddOn::whereIn('id', $request->addons ?? [])->sum('harga');

            // Total = Harga Paket (Flat) + Harga Add-ons (Flat)
            $totalHarga = $paket->harga + $hargaAddons;

            // 4. Validasi Kuota
            $maxQuotaPerDay = 5;
            $totalJobOnDay = Booking::whereIn('status', ['pending', 'confirmed', 'success'])
                ->where('event_date', $eventDate)
                ->count();

            if ($totalJobOnDay >= $maxQuotaPerDay) {
                return back()->withErrors(['event_date_range' => 'Kuota penuh untuk tanggal tersebut.'])->withInput();
            }

            // 5. Simpan ke Database
            $booking = Booking::create([
                'customer_name'    => $request->customer_name,
                'whatsapp_number'  => $request->whatsapp_number,
                'bride_groom_name' => $request->bride_groom_name,
                'parent_name'      => $request->parent_name,
                'facebook_name'    => $request->facebook_name,
                'instagram_name'   => $request->instagram_name,
                'event_address'    => $request->event_address,
                'event_date'       => $eventDate,
                'event_duration'   => $duration,
                'paket_id'         => $request->paket_id,
                'package_name'     => $paket->nama_paket,
                'package_price'    => (string) $totalHarga, // Nilai sudah flat
                'notes'            => $request->notes,
                'status'           => 'draft',
                'user_id'          => Auth::id(),
            ]);

            // 6. Simpan Relasi Add-ons
            if ($request->has('addons')) {
                $booking->addOns()->attach($request->addons);
            }
            $admins = User::where('role', 'admin')->get(); // Menggunakan User yang sudah di-import
            foreach ($admins as $admin) {
                $admin->notify(new SistemNotifikasi([
                    'judul'      => '🛒 Pesanan Masuk (Draft)',
                    'pesan'      => 'Pelanggan ' . Auth::user()->name . ' menyimpan booking untuk acara ' . $paket->nama_paket,
                    'icon'       => 'bi-cart-plus',
                    'link'       => route('admin.booking.index'),
                    'booking_id' => $booking->id
                ]));
            }
            return redirect()->route('user.keranjang')->with('success_booking', 'Berhasil disimpan ke keranjang!');
        } catch (\Exception $e) {
            return back()->withErrors(['event_date_range' => 'Gagal menyimpan pesanan: ' . $e->getMessage()])->withInput();
        }
    }
    public function keranjang()
    {
        // Menggunakan with('addOns') untuk efisiensi query (Eager Loading)
        $carts = Booking::with('addOns')
            ->where('user_id', Auth::id())
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();



        return view('user.keranjang.index', compact('carts'));
    }

    public function konfirmasi()
    {
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

        Booking::where('user_id', Auth::id())
            ->where('status', 'draft')
            ->update(['status' => 'pending']);

        $admins = User::where('role', 'admin')->get(); // Pastikan variabel $admins didefinisikan kembali di sini
        foreach ($admins as $admin) {
            $notif = $admin->notifications()
                ->where('data->booking_id', $item->id)
                ->first();

            if ($notif) {
                $notif->update([
                    'data' => [
                        'judul'      => '🔔 Booking Perlu Diproses!',
                        'pesan'      => 'Pelanggan ' . Auth::user()->name . ' telah melakukan konfirmasi booking.',
                        'icon'       => 'bi-cart-check',
                        'link'       => route('admin.booking.index'),
                        'booking_id' => $item->id
                    ]
                ]);
            }
        }

        return redirect()->route('user.pembayaran')->with('success', 'Konfirmasi berhasil!');
    }

    public function destroy($id)
    {
        // Cari data booking berdasarkan ID yang milik user yang sedang login
        // dan statusnya masih 'draft' agar user tidak bisa menghapus pesanan orang lain atau yang sudah diproses
        $booking = Booking::where('user_id', Auth::id())
            ->where('status', 'draft')
            ->findOrFail($id);

        // Hapus relasi di tabel pivot terlebih dahulu agar tidak ada data yatim
        $booking->addOns()->detach();

        // Hapus data bookingnya
        $booking->delete();

        return redirect()->route('user.keranjang')->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}
