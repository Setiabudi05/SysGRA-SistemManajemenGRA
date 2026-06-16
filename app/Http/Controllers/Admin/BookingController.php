<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Paket;
use App\Models\Pembayaran;
use App\Models\Pembukuan;
use App\Models\User; // Tambahan import Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

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
    public function data()
    {
        // Ambil query murni menggunakan DB Builder
        $query = DB::table('bookings')
            ->select([
                'id',
                'customer_name',
                'whatsapp_number',
                'bride_groom_name',
                'package_name',
                'package_price',
                'event_date',
                'status',
                'created_at'
            ])
            ->latest('created_at');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('id', function ($row) {
                return $row->id;
            })
            ->addColumn('pengantin', function ($row) {
                return $row->bride_groom_name;
            })
            ->editColumn('event_date', function ($row) {
                if (!$row->event_date) return '-';
                return date('Y-m-d', strtotime($row->event_date));
            })
            // ====================================================================
            // STEP 1: HITUNG SISA TAGIHAN DULU (Saat package_price masih berupa ANGKA MURNI)
            // ====================================================================
            ->addColumn('sisa_tagihan', function ($row) {
                // Hitung nominal uang masuk dari tabel pembayarans
                $totalTerbayar = DB::table('pembayarans')
                    ->where('pesanan_id', $row->id)
                    ->where(function ($q) {
                        $q->whereRaw('LOWER(status_pembayaran) = ?', ['success'])
                            ->orWhereRaw('LOWER(status_pembayaran) = ?', ['lunas'])
                            ->orWhereRaw('LOWER(status_pembayaran) = ?', ['valid'])
                            ->orWhereRaw('LOWER(status_pembayaran) = ?', ['confirmed'])
                            ->orWhereRaw('LOWER(status_pembayaran) = ?', ['confirmed (dp)'])
                            ->orWhereNull('status_pembayaran');
                    })
                    ->sum('jumlah_bayar');

                $hargaAsli = (int) $row->package_price;
                $sudahBayar = (int) $totalTerbayar;

                $sisa = $hargaAsli - $sudahBayar;
                $sisaAman = $sisa < 0 ? 0 : $sisa;

                return 'Rp ' . number_format($sisaAman, 0, ',', '.');
            })
            // ====================================================================
            // STEP 2: BARU FORMAT HARGA PAKET MENJADI RUPIAH
            // ====================================================================
            ->editColumn('package_price', function ($row) {
                return 'Rp ' . number_format($row->package_price, 0, ',', '.');
            })
            ->editColumn('status', function ($row) {
                $statusNormalized = strtoupper($row->status);

                $badges = [
                    'PENDING'             => 'bg-light-warning text-warning',
                    'CONFIRMED'           => 'bg-light-primary text-primary',
                    'MENUNGGU VERIFIKASI' => 'bg-light-info text-info',
                    'COMPLETED'           => 'bg-light-success text-success',
                    'SUCCESS'             => 'bg-light-success text-success',
                    'FAILED'              => 'bg-light-danger text-danger',
                ];

                $badgeClass = $badges[$statusNormalized] ?? 'bg-secondary text-white';

                return '<span class="badge ' . $badgeClass . ' px-3 py-2 fw-bold">' . $statusNormalized . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '<div class="btn-group gap-2">
                            <a href="' . route('admin.booking.show', $row->id) . '" class="btn btn-sm btn-info text-white shadow-sm"><i class="bi bi-eye"></i></a>
                            <button type="button" class="btn btn-sm btn-outline-danger shadow-sm" onclick="hapusBooking(' . $row->id . ')"><i class="bi bi-trash"></i></button>
                        </div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function create()
    {
        $list_paket = Paket::orderBy('nama_paket', 'asc')->get();
        // PERBAIKAN: Ambil semua data user ber-role pelanggan untuk opsi dropdown select
        $list_pelanggan = User::where('role', 'pelanggan')->orderBy('name', 'asc')->get();
        
        return view('admin.booking.create', compact('list_paket', 'list_pelanggan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id', // Kunci Validasi Integrasi Global
            'customer_name'    => 'required',
            'whatsapp_number'  => 'required',
            'bride_groom_name' => 'required',
            'event_date'       => 'required|date',
            'duration'         => 'required',
            'package_name'     => 'required',
            'package_price'    => 'required',
            'event_address'    => 'required',
        ]);

        $cleanDuration = (int) preg_replace('/[^0-9]/', '', $request->duration);
        $cleanPrice    = (int) preg_replace('/[^0-9]/', '', $request->package_price);

        // PERBAIKAN: Menyertakan 'user_id' agar terikat otomatis sepanjang masa ke dashboard pelanggan
        $booking = Booking::create([
            'user_id'          => $request->user_id, 
            'customer_name'    => $request->customer_name,
            'whatsapp_number'  => $request->whatsapp_number,
            'bride_groom_name' => $request->bride_groom_name,
            'parent_name'      => $request->parent_names,
            'facebook_name'    => $request->fb_name,
            'instagram_name'   => $request->ig_name,
            'event_date'       => $request->event_date,
            'event_duration'   => $cleanDuration,
            'event_address'    => $request->event_address,
            'package_name'     => $request->package_name,
            'package_price'    => $cleanPrice,
            'add_ons'          => $request->additional_package,
            'notes'            => $request->notes,
            'status'           => 'PENDING',
        ]);

        return redirect()->route('admin.booking.show', $booking->id)
            ->with('swal_success', 'Pesanan baru berhasil disimpan dan terintegrasi otomatis dengan akun pelanggan!');
    }

    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        return view('admin.booking.detail', compact('booking'));
    }

    public function edit($id)
    {
        $booking = Booking::findOrFail($id);
        $list_paket = Paket::orderBy('tahun', 'desc')->get();
        // PERBAIKAN: Ambil data pelanggan juga untuk form edit
        $list_pelanggan = User::where('role', 'pelanggan')->orderBy('name', 'asc')->get();

        return view('admin.booking.edit', compact('booking', 'list_paket', 'list_pelanggan'));
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'user_id'          => 'required|exists:users,id', // Kunci Validasi Edit
            'customer_name'    => 'required',
            'whatsapp_number'  => 'required',
            'bride_groom_name' => 'required',
            'event_date'       => 'required',
            'duration'         => 'required',
            'package_name'     => 'required',
            'package_price'    => 'required',
            'event_address'    => 'required',
        ]);

        $cleanDuration = (int) preg_replace('/[^0-9]/', '', $request->duration);
        $cleanPrice    = (int) preg_replace('/[^0-9]/', '', $request->package_price);

        // PERBAIKAN: Menyertakan 'user_id' pas update data
        $booking->update([
            'user_id'          => $request->user_id,
            'customer_name'    => $request->customer_name,
            'whatsapp_number'  => $request->whatsapp_number,
            'bride_groom_name' => $request->bride_groom_name,
            'parent_name'      => $request->parent_names,
            'facebook_name'    => $request->fb_name,
            'instagram_name'   => $request->ig_name,
            'event_date'       => $request->event_date,
            'event_duration'   => $cleanDuration,
            'event_address'    => $request->event_address,
            'package_name'     => $request->package_name,
            'package_price'    => $cleanPrice,
            'add_ons'          => $request->additional_package,
            'notes'            => $request->notes,
        ]);

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