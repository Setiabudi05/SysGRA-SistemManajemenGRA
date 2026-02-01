<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Booking;
use App\Models\Pembukuan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
     */
    public function create()
    {
        // Mengambil data pesanan yang belum lunas agar muncul di dropdown
        $list_booking = Booking::where('status', '!=', 'LUNAS')
            ->orderBy('bride_groom_name', 'asc')
            ->get();

        return view('admin.pembayaran.create', compact('list_booking'));
    }

    /**
     * Memproses data DataTables untuk ringkasan pembayaran per pengantin
     */
    public function data()
    {
        // Mengambil data booking yang sudah punya riwayat pembayaran
        $query = Booking::has('pembayarans')->with('pembayarans')->latest();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('pengantin', fn($row) => $row->bride_groom_name)
            ->addColumn('total_bayar', function ($row) {
                return 'Rp ' . number_format($row->total_bayar, 0, ',', '.');
            })
            ->addColumn('sisa', function ($row) {
                return 'Rp ' . number_format($row->sisa_tagihan, 0, ',', '.');
            })
            ->addColumn('status', function ($row) {
                /**
                 * LOGIKA OTOMATIS STATUS
                 * Jika Total Bayar >= 2.000.000 maka status CONFIRMED (Hijau)
                 * Jika kurang dari itu tetap PENDING (Kuning/Biru)
                 */
                if ($row->total_bayar >= 2000000) {
                    return '<span class="badge bg-success">CONFIRMED</span>';
                }

                return '<span class="badge bg-warning text-dark">PENDING</span>';
            })
            ->addColumn('action', function ($row) {
                return '
            <div class="btn-group gap-2">
                <a href="' . route('admin.pembayaran.histori', $row->id) . '" class="btn btn-sm btn-info text-white shadow-sm">
                    <i class="bi bi-eye"></i> Detail
                </a>
            </div>';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    /**
     * Menyimpan data pembayaran baru dan otomatis masuk ke pembukuan
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'jumlah_bayar' => 'required',
            'keterangan' => 'required|string|max:255'
        ]);

        // Membersihkan format titik dari input rupiah
        $nominal = (int) preg_replace('/[^0-9]/', '', $request->jumlah_bayar);

        // 1. Simpan ke Tabel Pembayaran
        $pembayaran = Pembayaran::create([
            'booking_id' => $request->booking_id,
            'jumlah_bayar' => $nominal,
            'keterangan' => $request->keterangan,
            'status' => 'valid'
        ]);

        // 2. OTOMATIS: Simpan ke Tabel Pembukuan (Revisi Dosen)
        Pembukuan::create([
            'tanggal' => now()->toDateString(),
            'tipe' => 'pemasukan',
            'customer' => $pembayaran->booking->bride_groom_name,
            'keterangan' => 'Cicilan: ' . $request->keterangan,
            'nominal' => $nominal,
            'pembayaran_id' => $pembayaran->id
        ]);

        // Redirect ke halaman HISTORI (agar user langsung lihat hasilnya) dengan pesan sukses
        return redirect()->route('admin.pembayaran.histori', $request->booking_id)
            ->with('swal_success', 'Pembayaran & Pembukuan Berhasil Dicatat!');
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
                'chroot' => public_path() // Akses logo di folder public
            ])
            ->setPaper('A4', 'portrait');

        return $pdf->stream("Nota_GRA_{$pembayaran->booking->customer_name}.pdf");
    }

    /**
     * Menghapus data pembayaran cicilan
     */
    public function destroy($id)
    {
        // Penghapusan pembayaran otomatis akan memicu penghapusan pembukuan jika relasi diatur cascade
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->delete();

        return response()->json(['success' => true]);
    }
}
