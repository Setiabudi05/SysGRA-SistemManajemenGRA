<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Booking;
use App\Models\Pembukuan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PembayaranController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen pembayaran (Tabel Ringkasan)
     */
    public function index()
    {
        return view('admin.pembayaran.index');
    }

    /**
     * Menampilkan halaman form tambah cicilan baru (Halaman Terpisah)
     * PERBAIKAN SINKRONISASI: Mendukung filter MULTI-STATUS ('success' dan 'lunas') untuk data lama & baru
     */
    public function create()
    {
        // 1. Ambil data pesanan yang belum selesai/batal
        $bookings = Booking::whereNotIn('status', ['completed', 'COMPLETED', 'failed', 'FAILED', 'cancel', 'CANCEL'])
            ->orderBy('bride_groom_name', 'asc')
            ->get();

        // 2. Loop data untuk menyuntikkan nominal 'sisa_tagihan' riil ke dalam object collection
        $list_booking = $bookings->map(function ($booking) {
            $totalTerbayar = DB::table('pembayarans')
                ->where('pesanan_id', $booking->id)
                ->where(function ($q) {
                    $q->whereRaw('LOWER(status_pembayaran) = ?', ['success'])
                        ->orWhereRaw('LOWER(status_pembayaran) = ?', ['lunas']) // SINKRON: Terima data online lama
                        ->orWhereNull('status_pembayaran');
                })
                ->sum('jumlah_bayar');

            $sisa = (int)$booking->package_price - (int)$totalTerbayar;
            $booking->sisa_tagihan = $sisa < 0 ? 0 : $sisa;

            return $booking;
        });

        return view('admin.pembayaran.create', compact('list_booking'));
    }

    /**
     * Memproses data DataTables untuk ringkasan pembayaran per pengantin
     * PERBAIKAN SINKRONISASI: Filter total bayar mendukung multi-status + Badge Kapital Case-Insensitive
     */
    /**
     * Memproses data DataTables untuk ringkasan pembayaran
     */
    /**
     * Memproses data DataTables untuk ringkasan pembayaran
     * FIX: Menggunakan with() agar Booking tanpa pembayaran tetap muncul (Status PENDING)
     */
    public function data(Request $request)
    {
        // 1. Ambil data dengan relasi yang dibutuhkan
        $bookings = Booking::with(['paket', 'pembayarans', 'addOns'])->get();

        // 2. Filter data
        $filteredData = $bookings->filter(function ($row) use ($request) {
            $harga = $row->total_harga;
            $total = $row->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
            $sisa = $harga - $total;

            $status = ($sisa <= 0 && $total > 0) ? 'LUNAS' : (strtoupper($row->status) == 'CONFIRMED' ? 'CONFIRMED' : 'PENDING');

            if ($request->filled('status') && $status !== $request->status) return false;
            if ($request->filled('tgl') && \Carbon\Carbon::parse($row->event_date)->format('Y-m-d') !== $request->tgl) return false;

            return true;
        });
        $filteredData = $bookings->filter(function ($row) use ($request) {
            // PERBAIKAN: Hitung total harga murni (harga paket + total add-ons)
            $hargaPaket = (int) $row->paket->harga;
            $totalAddons = (int) $row->addOns->sum('harga');
            $totalTagihan = $hargaPaket + $totalAddons;

            $totalTerbayar = $row->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
            $sisa = $totalTagihan - $totalTerbayar;

            // Logika Status
            $status = ($sisa <= 0 && $totalTerbayar > 0) ? 'LUNAS' : (strtoupper($row->status) == 'CONFIRMED' ? 'CONFIRMED' : 'PENDING');

            if ($request->filled('status') && $status !== $request->status) return false;
            if ($request->filled('tgl') && \Carbon\Carbon::parse($row->event_date)->format('Y-m-d') !== $request->tgl) return false;

            return true;
        });

        // 3. Setup DataTables
        return DataTables::of($filteredData)
            ->addIndexColumn()

            // --- PERBAIKAN: Format Tanggal ke Bahasa Indonesia (20 Juni 2026) ---
            ->addColumn('tanggal_acara', function ($row) {
                \Carbon\Carbon::setLocale('id'); // Pastikan locale diset ke Indonesia
                return \Carbon\Carbon::parse($row->event_date)->translatedFormat('d F Y');
            })
            // --------------------------------------------------------------------

            ->addColumn('pengantin', fn($row) => $row->bride_groom_name)
            ->addColumn('total_bayar', function ($row) {
                $total = $row->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
                return 'Rp ' . number_format($total, 0, ',', '.');
            })
            ->addColumn('sisa', function ($row) {
                $totalTagihan = (int)$row->paket->harga + (int)$row->addOns->sum('harga');
                $totalTerbayar = $row->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
                return 'Rp ' . number_format(max(0, $totalTagihan - $totalTerbayar), 0, ',', '.');
            })
            ->addColumn('status', function ($row) {
                $harga = $row->total_harga;
                $total = $row->pembayarans->whereIn('status_pembayaran', ['success', 'lunas', null])->sum('jumlah_bayar');
                $sisa = $harga - $total;
                $status = ($sisa <= 0 && $total > 0) ? 'LUNAS' : (strtoupper($row->status) == 'CONFIRMED' ? 'CONFIRMED' : 'PENDING');

                if ($status == 'LUNAS') return '<span class="badge bg-primary px-3 py-2 fw-bold">LUNAS</span>';
                if ($status == 'CONFIRMED') return '<span class="badge bg-success px-3 py-2 fw-bold">CONFIRMED</span>';
                return '<span class="badge bg-warning text-dark px-3 py-2 fw-bold">PENDING</span>';
            })
            ->addColumn('action', fn($row) => '<div class="btn-group gap-1"><a href="' . route('admin.pembayaran.histori', $row->id) . '" class="btn btn-sm btn-info text-white shadow-sm"><i class="bi bi-eye"></i></a></div>')
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
    /**
     * Menyimpan data pembayaran baru secara manual oleh Admin dan otomatis masuk ke pembukuan
     */
    public function store(Request $request)
    {
        $request->validate([
            'pesanan_id'   => 'required|exists:bookings,id',
            'jumlah_bayar' => 'required',
            'keterangan'   => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $nominal = (int) preg_replace('/[^0-9]/', '', $request->jumlah_bayar);
            $booking = Booking::findOrFail($request->pesanan_id);

            // 1. Simpan Pembayaran
            $pembayaran = Pembayaran::create([
                'pesanan_id'        => $booking->id,
                'jumlah_bayar'      => $nominal,
                'keterangan'        => $request->keterangan,
                'status_pembayaran' => 'success',
            ]);

            // 2. Update status booking
            $booking->update(['status' => 'CONFIRMED']);

            // 3. CEK APAKAH SUDAH ADA DI PEMBUKUAN (Mencegah Duplikasi)
            // Jika sudah ada, jangan buat baru lagi
            $pembukuanExist = Pembukuan::where('pembayaran_id', $pembayaran->id)->exists();

            if (!$pembukuanExist) {
                Pembukuan::create([
                    'tanggal'       => now()->toDateString(),
                    'tipe'          => 'pemasukan',
                    'customer'      => $booking->bride_groom_name,
                    'keterangan'    => 'Verifikasi Manual: ' . $request->keterangan,
                    'nominal'       => $nominal,
                    'pembayaran_id' => $pembayaran->id
                ]);
            }

            DB::commit();
            return redirect()->route('admin.pembayaran.histori', $booking->id)
                ->with('swal_success', 'Pesanan Berhasil Diverifikasi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['pesanan_id' => 'Sistem Gagal Memproses: ' . $e->getMessage()]);
        }
    }

    /**
     * Handler untuk notifikasi otomatis dari Midtrans
     */
    public function notificationHandler(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        // Verifikasi tanda tangan (Signature Key) untuk keamanan
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        // Ambil data pembayaran
        $pembayaran = Pembayaran::find($request->order_id);

        if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
            // 1. Update status pembayaran menjadi success
            $pembayaran->update(['status_pembayaran' => 'success']);

            // 2. Update status booking menjadi CONFIRMED
            $booking = Booking::findOrFail($pembayaran->pesanan_id);
            $booking->update(['status' => 'CONFIRMED']);

            // 3. Masukkan ke Pembukuan secara otomatis
            Pembukuan::create([
                'tanggal' => now(),
                'tipe' => 'pemasukan',
                'customer' => $booking->bride_groom_name,
                'keterangan' => 'Pembayaran Otomatis Midtrans - Order: ' . $request->order_id,
                'nominal' => $request->gross_amount,
                'pembayaran_id' => $pembayaran->id
            ]);
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Melihat rincian riwayat cicilan untuk satu pesanan spesifik
     */
    public function histori($id)
    {
        $booking = Booking::with('pembayarans')->findOrFail($id);
        return view('admin.pembayaran.histori', compact('booking'));
    }

    /**
     * Fungsi Cetak Nota PDF Per Transaksi Cicilan
     */
    public function cetakNota($id)
    {
        $pembayaran = Pembayaran::with('booking')->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pembayaran.nota_pdf', compact('pembayaran'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => public_path()
            ])
            ->setPaper('A4', 'portrait');

        return $pdf->stream("Nota_GRA_{$pembayaran->booking->customer_name}.pdf");
    }

    /**
     * Menghapus data pembayaran cicilan dan sinkronisasi otomatis status booking (VERSI ANTI-NULL KAPITAL)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // 1. Cari data pembayaran yang mau dihapus
            $pembayaran = Pembayaran::findOrFail($id);
            $bookingId = $pembayaran->pesanan_id;

            // 2. Hapus otomatis jurnal pembukuan kas yang terkait agar laporan keuangan bersih
            if (Schema::hasTable('pembukuans')) {
                DB::table('pembukuans')->where('pembayaran_id', $pembayaran->id)->delete();
            }

            // 3. Hapus data pembayaran ini dari tabel pembayarans
            $pembayaran->delete();

            // 4. Ambil data booking terkait
            $booking = Booking::findOrFail($bookingId);

            // 5. Hitung jumlah BARIS data pembayaran yang tersisa untuk booking ini
            $jumlahDataPembayaranSisa = DB::table('pembayarans')
                ->where('pesanan_id', $bookingId)
                ->count();

            // 6. LOGIKA SINKRONISASI PASCA-HAPUS:
            if ($jumlahDataPembayaranSisa <= 0) {
                $booking->update([
                    'status' => 'PENDING'
                ]);
            } else {
                // Hitung nominal sisa menggunakan filter multi-status sah ('success' dan 'lunas')
                $totalTerbayarSisa = DB::table('pembayarans')
                    ->where('pesanan_id', $bookingId)
                    ->where(function ($q) {
                        $q->whereRaw('LOWER(status_pembayaran) = ?', ['success'])
                            ->orWhereRaw('LOWER(status_pembayaran) = ?', ['lunas'])
                            ->orWhereNull('status_pembayaran');
                    })
                    ->sum('jumlah_bayar');

                if ((int)$totalTerbayarSisa <= 0) {
                    $booking->update(['status' => 'PENDING']);
                } else {
                    $booking->update(['status' => 'CONFIRMED']);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data pembayaran berhasil dihapus dan status booking otomatis diperbarui!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}
