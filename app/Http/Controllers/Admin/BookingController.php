<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Paket;
use App\Models\Pembayaran;
use App\Models\Pembukuan;
use App\Models\User;
use App\Models\AddOn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Menampilkan halaman utama daftar pesanan admin
     */
    public function index()
    {
        return view('admin.booking.index');
    }

    /**
     * Memproses data DataTables untuk halaman Manajemen Pesanan Admin
     */
    public function data(Request $request)
    {
        // 1. Gunakan subquery untuk menghitung total harga (Paket + Add-ons)
        // agar sisa_tagihan di tabel pesanan selalu akurat & sinkron
        $query = DB::table('bookings')
            ->select([
                'bookings.id',
                'bookings.customer_name',
                'bookings.whatsapp_number',
                'bookings.bride_groom_name',
                'bookings.package_name',
                'bookings.package_price',
                'bookings.event_date',
                'bookings.status',
                // Menghitung total harga: Harga Paket + Total Harga Add-ons
                DB::raw('(SELECT package_price + IFNULL(SUM(add_ons.harga), 0) 
                      FROM add_ons_booking 
                      LEFT JOIN add_ons ON add_ons.id = add_ons_booking.add_on_id 
                      WHERE add_ons_booking.booking_id = bookings.id) as total_tagihan_final')
            ])
            ->latest('bookings.created_at');

        if ($request->filled('status')) {
            $query->where('bookings.status', $request->status);
        }
        if ($request->filled('tgl_acara')) {
            $query->whereDate('bookings.event_date', $request->tgl_acara);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('event_date', function ($row) {
                return $row->event_date ? Carbon::parse($row->event_date)->translatedFormat('d F Y') : '-';
            })
            // Kolom Sisa Tagihan dengan Add-ons
            ->addColumn('sisa_tagihan', function ($row) {
                $terbayar = DB::table('pembayarans')
                    ->where('pesanan_id', $row->id)
                    ->whereIn('status_pembayaran', ['success', 'lunas', null])
                    ->sum('jumlah_bayar');

                // Menggunakan 'total_tagihan_final' dari subquery
                $sisa = (int)$row->total_tagihan_final - (int)$terbayar;
                return 'Rp ' . number_format(max(0, $sisa), 0, ',', '.');
            })
            // Mengedit tampilan harga paket agar sesuai total final
            ->editColumn('package_price', fn($row) => 'Rp ' . number_format($row->total_tagihan_final, 0, ',', '.'))
            ->editColumn('status', function ($row) {
                $class = ['PENDING' => 'bg-light-warning text-warning', 'CONFIRMED' => 'bg-light-primary text-primary', 'COMPLETED' => 'bg-light-success text-success', 'DRAFT' => 'bg-secondary text-white'][strtoupper($row->status)] ?? 'bg-secondary';
                return '<span class="badge ' . $class . ' px-3 py-2 fw-bold">' . strtoupper($row->status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '<div class="btn-group gap-2">
                        <a href="' . route('admin.booking.show', $row->id) . '" class="btn btn-sm btn-info text-white"><i class="bi bi-eye"></i></a>
                        <button class="btn btn-sm btn-outline-danger" onclick="hapusBooking(' . $row->id . ')"><i class="bi bi-trash"></i></button>
                    </div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
    public function create()
    {
        $list_paket = Paket::orderBy('nama_paket', 'asc')->get();
        $list_pelanggan = User::where('role', 'pelanggan')->orderBy('name', 'asc')->get();

        // TAMBAHKAN BARIS INI: Ambil semua data add-ons
        $list_addons = AddOn::orderBy('nama_item', 'asc')->get();

        // KIRIMKAN $list_addons KE VIEW
        return view('admin.booking.create', compact('list_paket', 'list_pelanggan', 'list_addons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'customer_name'    => 'required',
            'whatsapp_number'  => 'required',
            'bride_groom_name' => 'required',
            'event_date'       => 'required|date',
            'event_duration'   => 'required',
            'package_name'     => 'required',
            'total_harga'      => 'required', // Pastikan name di blade sesuai
            'event_address'    => 'required',
        ]);

        $cleanPrice = (int) str_replace('.', '', $request->total_harga);

        // Simpan Booking
        $booking = Booking::create([
            'user_id'          => $request->user_id,
            'customer_name'    => $request->customer_name,
            'whatsapp_number'  => $request->whatsapp_number,
            'bride_groom_name' => $request->bride_groom_name,
            'parent_name'      => $request->parent_names,
            'facebook_name'    => $request->fb_name,
            'instagram_name'   => $request->ig_name,
            'event_date'       => $request->event_date,
            'event_duration'   => $request->event_duration,
            'event_address'    => $request->event_address,
            'package_name'     => $request->package_name,
            'package_price'    => $cleanPrice,
            'notes'            => $request->notes,
            'status'           => 'PENDING',
        ]);

        // Sinkronisasi Add-ons (tabel pivot)
        if ($request->has('add_ons')) {
            $booking->addOns()->sync($request->add_ons);
        }

        return redirect()->route('admin.booking.show', $booking->id)
            ->with('swal_success', 'Pesanan berhasil dibuat!');
    }
    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        return view('admin.booking.detail', compact('booking'));
    }

    public function edit($id)
    {
        $booking = Booking::findOrFail($id);
        $list_paket = Paket::orderBy('nama_paket', 'asc')->get();
        $list_pelanggan = User::where('role', 'pelanggan')->orderBy('name', 'asc')->get();

        // TAMBAHKAN BARIS INI: Ambil semua data add-ons
        $list_addons = \App\Models\AddOn::orderBy('nama_item', 'asc')->get();

        // Tambahkan 'list_addons' ke dalam compact
        return view('admin.booking.edit', compact('booking', 'list_paket', 'list_pelanggan', 'list_addons'));
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'customer_name'    => 'required',
            'whatsapp_number'  => 'required',
            'bride_groom_name' => 'required',
            'event_date'       => 'required',
            'event_duration'   => 'required',
            'package_name'     => 'required',
            'total_harga'      => 'required',
            'event_address'    => 'required',
        ]);

        $cleanPrice = (int) str_replace('.', '', $request->total_harga);

        $booking->update([
            'user_id'          => $request->user_id,
            'customer_name'    => $request->customer_name,
            'whatsapp_number'  => $request->whatsapp_number,
            'bride_groom_name' => $request->bride_groom_name,
            'parent_name'      => $request->parent_names,
            'facebook_name'    => $request->fb_name,
            'instagram_name'   => $request->ig_name,
            'event_date'       => $request->event_date,
            'event_duration'   => $request->event_duration,
            'event_address'    => $request->event_address,
            'package_name'     => $request->package_name,
            'package_price'    => $cleanPrice,
            'notes'            => $request->notes,
        ]);

        // Sinkronisasi Add-ons (Update relasi pivot)
        if ($request->has('add_ons')) {
            $booking->addOns()->sync($request->add_ons);
        } else {
            // Jika tidak ada yang dipilih, hapus semua add-ons lama
            $booking->addOns()->detach();
        }

        return redirect()->route('admin.booking.show', $id)
            ->with('swal_success', 'Data pesanan berhasil diperbarui!');
    }
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $statusInput = strtolower($request->status);

        // LOGIKA: KONFIRMASI DP MANUAL (Hanya diproses jika status saat ini bener-bener masih PENDING)
        if ($statusInput == 'confirmed') {
            if (strtoupper($booking->status) == 'PENDING') {
                $booking->update(['status' => 'CONFIRMED']);
                $this->kirimWhatsApp($booking);

                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan Berhasil Dikonfirmasi Manual & WhatsApp Terkirim!'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pesanan sudah berstatus Terkonfirmasi sebelumnya.'
            ]);
        }

        // LOGIKA: SELESAI / PELUNASAN (COMPLETED)
        if ($statusInput == 'completed') {
            $totalBayar = DB::table('pembayarans')
                ->where('pesanan_id', $booking->id)
                ->where(function ($q) {
                    $q->whereRaw('LOWER(status_pembayaran) = ?', ['success'])
                        ->orWhereRaw('LOWER(status_pembayaran) = ?', ['lunas'])
                        ->orWhereNull('status_pembayaran');
                })
                ->sum('jumlah_bayar');

            $sisa = (int)$booking->package_price - (int)$totalBayar;

            if ($sisa > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Masih ada sisa tagihan Rp ' . number_format($sisa, 0, ',', '.')
                ], 422);
            }

            $booking->update(['status' => 'COMPLETED']);
            return response()->json(['success' => true, 'message' => 'Pesanan Berhasil Diselesaikan!']);
        }

        return response()->json(['success' => false, 'message' => 'Aksi tidak dikenali.']);
    }

    /**
     * Menghapus data pesanan/booking (SINKRON HAPUS BERUNTUN GLOBAL)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $booking = Booking::findOrFail($id);

            // 1. Ambil semua ID pembayaran yang terikat dengan pesanan/booking ini
            $pembayaranIds = DB::table('pembayarans')
                ->where('pesanan_id', $booking->id)
                ->pluck('id');

            // 2. Bersihkan catatan di jurnal pembukuan kas berdasarkan id pembayaran tersebut
            if ($pembayaranIds->isNotEmpty()) {
                DB::table('pembukuans')->whereIn('pembayaran_id', $pembayaranIds)->delete();
            }

            // 3. Bumihanguskan semua riwayat cicilan terkait di tabel pembayarans
            DB::table('pembayarans')->where('pesanan_id', $booking->id)->delete();

            // 4. Hapus data utama di tabel bookings
            $booking->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pesanan dan seluruh riwayat pembayaran terkait berhasil dihapus permanen!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function print($id)
    {
        $booking = Booking::findOrFail($id);
        $pdf = Pdf::loadView('admin.booking.print_pdf', compact('booking'))->setPaper('A4', 'portrait');
        return $pdf->stream("Formulir_Booking_{$booking->customer_name}.pdf");
    }

    private function kirimWhatsApp($booking)
    {
        $token = "TOKEN_FONNTE_ANDA";
        $pesan = "Halo " . $booking->customer_name . ", DP pesanan Anda di Griya Rias Asmara telah kami terima. Status pesanan Anda kini: TERKONFIRMASI. Terima kasih.";

        try {
            Http::withHeaders(['Authorization' => $token])->post('https://api.fonnte.com/send', [
                'target'  => $booking->whatsapp_number,
                'message' => $pesan,
                'role'    => 'pelanggan'
            ]);
        } catch (\Exception $e) {
            // Log error jika diperlukan
        }
    }
}
