<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Paket;
use App\Models\Pembayaran;
use App\Models\Pembukuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        return view('admin.booking.index');
    }

    public function data()
    {
        $query = Booking::with('pembayarans')->latest();
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                $badges = [
                    'pending'   => 'bg-light-warning text-warning',
                    'confirmed' => 'bg-light-primary text-primary',
                    'completed' => 'bg-light-success text-success',
                ];
                $label = $row->status == 'confirmed' ? 'TERKONFIRMASI' : strtoupper($row->status);
                return '<span class="badge ' . ($badges[$row->status] ?? 'bg-secondary') . ' px-3 py-2 fw-bold">' . $label . '</span>';
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
        return view('admin.booking.create', compact('list_paket'));
    }

    public function store(Request $request)
    {
        $request->validate([
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

        $booking = Booking::create([
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
            'status'           => 'pending',
        ]);

        return redirect()->route('admin.booking.show', $booking->id)
            ->with('swal_success', 'Pesanan baru berhasil disimpan!');
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
        return view('admin.booking.edit', compact('booking', 'list_paket'));
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
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

        $booking->update([
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

        // LOGIKA: KONFIRMASI DP (HANYA UPDATE STATUS)
        if ($request->status == 'confirmed') {
            // Cukup update status menjadi confirmed karena nominal uang sudah diinput manual sebelumnya
            $booking->update(['status' => 'confirmed']);

            // Kirim WhatsApp Notifikasi konfirmasi
            $this->kirimWhatsApp($booking);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan Berhasil Dikonfirmasi!'
            ]);
        }

        // LOGIKA: SELESAI / PELUNASAN
        if ($request->status == 'completed') {
            // Menggunakan accessor total_bayar untuk mengecek pelunasan
            $totalBayar = $booking->total_bayar;
            $sisa = $booking->package_price - $totalBayar;

            if ($sisa > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Masih ada sisa tagihan Rp ' . number_format($sisa, 0, ',', '.')
                ], 422);
            }

            $booking->update(['status' => 'completed']);
            return response()->json(['success' => true, 'message' => 'Pesanan Berhasil Diselesaikan!']);
        }

        return response()->json(['success' => false, 'message' => 'Aksi tidak dikenali.']);
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();
        return response()->json(['success' => true, 'message' => 'Pesanan berhasil dihapus!']);
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
            ]);
        } catch (\Exception $e) {
            // Log error jika diperlukan agar proses update status tidak berhenti karena API WA gagal
        }
    }
}
