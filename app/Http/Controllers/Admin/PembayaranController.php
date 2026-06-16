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
    public function data(Request $request)
    {
        $query = Booking::has('pembayarans')
            ->with(['paket', 'pembayarans'])
            ->latest();

        if ($request->has('bulan') && !empty($request->bulan)) {
            $query->whereMonth('event_date', $request->bulan);
        }
        if ($request->has('tahun') && !empty($request->tahun)) {
            $query->whereYear('event_date', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_acara', function ($row) {
                return \Carbon\Carbon::parse($row->event_date)->format('d M Y');
            })
            ->addColumn('pengantin', function ($row) {
                return $row->bride_groom_name;
            })
            ->addColumn('total_bayar', function ($row) {
                $total_terbayar = $row->pembayarans
                    ->whereIn('status_pembayaran', ['success', 'lunas', null])
                    ->sum('jumlah_bayar');
                return 'Rp ' . number_format($total_terbayar, 0, ',', '.');
            })
            ->addColumn('sisa', function ($row) {
                $hargaPaket = $row->paket->harga ?? 0;
                $total_terbayar = $row->pembayarans
                    ->whereIn('status_pembayaran', ['success', 'lunas', null])
                    ->sum('jumlah_bayar');
                $sisa = $hargaPaket - $total_terbayar;
                return 'Rp ' . number_format(max(0, $sisa), 0, ',', '.');
            })
            ->addColumn('status', function ($row) {
                $hargaPaket = $row->paket->harga ?? 0;
                $total_terbayar = $row->pembayarans
                    ->whereIn('status_pembayaran', ['success', 'lunas', null])
                    ->sum('jumlah_bayar');
                $sisa = $hargaPaket - $total_terbayar;

                // Logika Status Dinamis
                if ($sisa <= 0) {
                    return '<span class="badge bg-primary px-3 py-2 fw-bold">LUNAS</span>';
                } elseif (strtoupper($row->status) == 'CONFIRMED') {
                    return '<span class="badge bg-success px-3 py-2 fw-bold">CONFIRMED</span>';
                } else {
                    return '<span class="badge bg-warning text-dark px-3 py-2 fw-bold">PENDING</span>';
                }
            })
            ->addColumn('action', function ($row) {
                return '<div class="btn-group gap-2">
                <a href="' . route('admin.pembayaran.histori', $row->id) . '" class="btn btn-sm btn-info text-white shadow-sm">
                    <i class="bi bi-eye"></i> Detail
                </a>
            </div>';
            })
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

            // VALIDASI CONSTRAINT-BASED SCHEDULING (CBS) REVISI DOSEN
            $maxQuotaPerDay = 6;
            $totalJobOnDay = Booking::whereIn('status', ['terkonfirmasi', 'CONFIRMED', 'LUNAS', 'success', 'completed'])
                ->where('event_date', $booking->event_date)
                ->where('id', '!=', $booking->id)
                ->count();

            if ($totalJobOnDay >= $maxQuotaPerDay) {
                DB::rollBack();
                return back()->withErrors([
                    'pesanan_id' => 'Gagal Verifikasi! Kuota pelayanan pada tanggal tersebut mendadak sudah penuh terisi oleh pesanan offline/langsung lain.'
                ])->withInput();
            }

            // 1. Simpan ke Tabel Pembayaran dengan status standarisasi 'success'
            $pembayaran = Pembayaran::create([
                'pesanan_id'        => $booking->id,
                'jumlah_bayar'      => $nominal,
                'keterangan'        => $request->keterangan,
                'status_pembayaran' => 'success',
                'bukti_transfer'    => null,
                'catatan_admin'     => 'Diverifikasi Manual oleh Admin'
            ]);

            // 2. Ubah status booking pelanggan menjadi CONFIRMED (KAPITAL)
            $booking->update([
                'status' => 'CONFIRMED'
            ]);

            // 3. Otomatis masuk ke jurnal Pembukuan kas masuk (G ganda aman)
            Pembukuan::create([
                'tanggal'       => now()->toDateString(),
                'tipe'          => 'pemasukan',
                'customer'      => $booking->bride_groom_name,
                'keterangan'    => 'Verifikasi Manual: ' . $request->keterangan,
                'nominal'       => $nominal,
                'pembayaran_id' => $pembayaran->id
            ]);

            DB::commit();
            return redirect()->route('admin.pembayaran.histori', $booking->id)
                ->with('swal_success', 'Pesanan Berhasil Diverifikasi & Dijadwalkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['pesanan_id' => 'Sistem gagal memproses: ' . $e->getMessage()])->withInput();
        }
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
