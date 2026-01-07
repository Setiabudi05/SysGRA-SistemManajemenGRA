<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan ini ada

class PembayaranController extends Controller
{
    public function index()
    {
        return view('admin.pembayaran.index');
    }

    public function data()
    {
        $query = Pembayaran::with('booking')->select('pembayarans.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('pengantin', function ($row) {
                return $row->booking->bride_groom_name ?? '-';
            })
            ->addColumn('harga', function ($row) {
                $harga = $row->booking->package_price ?? 0;
                $cleanHarga = is_numeric($harga) ? $harga : (int) preg_replace('/[^0-9]/', '', $harga);
                return 'Rp ' . number_format($cleanHarga, 0, ',', '.');
            })
            ->addColumn('total_masuk', function ($row) {
                return 'Rp ' . number_format($row->booking->total_bayar ?? 0, 0, ',', '.');
            })
            ->addColumn('sisa_tagihan', function ($row) {
                return 'Rp ' . number_format($row->booking->sisa_tagihan ?? 0, 0, ',', '.');
            })
            ->editColumn('jumlah_bayar', function ($row) {
                return 'Rp ' . number_format($row->jumlah_bayar, 0, ',', '.');
            })
            ->editColumn('bukti_transfer', function ($row) {
                if ($row->bukti_transfer) {
                    $url = asset('storage/' . $row->bukti_transfer);
                    return '<img src="' . $url . '" width="70" class="rounded img-tf shadow-sm" onclick="viewFoto(\'' . $url . '\')" style="cursor:pointer">';
                }
                return '<span class="text-muted small">No Image</span>';
            })
            ->editColumn('status_pembayaran', function ($row) {
                $badges = [
                    'pending' => 'bg-light-warning text-warning',
                    'valid' => 'bg-light-success text-success',
                    'invalid' => 'bg-light-danger text-danger',
                ];
                $status = $row->status_pembayaran ?? 'pending';
                $badgeClass = $badges[$status] ?? 'bg-light-secondary text-secondary';
                return '<span class="badge ' . $badgeClass . ' px-3 py-2 fw-bold">' . strtoupper($status) . '</span>';
            })
            ->addColumn('aksi', function ($row) {
                $btn = '<div class="btn-group gap-1">';
                $btn .= '<button onclick="verifikasi(' . $row->id . ', \'valid\')" class="btn btn-sm btn-success shadow-sm" title="Setujui"><i class="bi bi-check-lg"></i></button>';
                $btn .= '<button onclick="verifikasi(' . $row->id . ', \'invalid\')" class="btn btn-sm btn-danger shadow-sm" title="Tolak"><i class="bi bi-x-lg"></i></button>';
                $btn .= '<a href="' . route('admin.pembayaran.nota', $row->id) . '" target="_blank" class="btn btn-sm btn-dark shadow-sm" title="Cetak Nota"><i class="bi bi-printer"></i></a>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['bukti_transfer', 'status_pembayaran', 'aksi'])
            ->orderColumn('pengantin', false)
            ->orderColumn('harga', false)
            ->orderColumn('total_masuk', false)
            ->orderColumn('sisa_tagihan', false)
            ->make(true);
    }

    public function updateStatus(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->update(['status_pembayaran' => $request->status]);

        if ($request->status === 'valid' && $pembayaran->booking) {
            $pembayaran->booking->update(['status' => 'confirmed']);
        }

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui!']);
    }

    // FUNGSI CETAK (Sama alurnya dengan Jadwal Dekor)
    public function cetakNota($id)
    {
        $pembayaran = Pembayaran::with(['booking.pembayarans' => function ($q) {
            $q->where('status_pembayaran', 'valid');
        }])->findOrFail($id);

        $booking = $pembayaran->booking;

        // LoadView mengarah ke file yang akan kita buat di bawah
        $pdf = Pdf::loadView('admin.pembayaran.nota', compact('pembayaran', 'booking'))
                  ->setPaper('A4', 'portrait');

        return $pdf->stream('Nota-Pembayaran-' . ($booking->bride_groom_name ?? $id) . '.pdf');
    }
}