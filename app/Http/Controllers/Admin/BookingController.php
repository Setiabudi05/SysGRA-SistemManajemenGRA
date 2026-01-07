<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller
{
    public function index() {
        return view('admin.booking.index');
    }

    public function data() {
        $query = Booking::query()->latest();

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
                $btn = '<div class="btn-group gap-2">';
                // Diarahkan ke halaman detail terpisah
                $btn .= '<a href="' . route('admin.booking.show', $row->id) . '" class="btn btn-sm btn-info text-white"><i class="bi bi-eye"></i> Detail</a>';
                $btn .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusBooking(' . $row->id . ')"><i class="bi bi-trash"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function create() {
        return view('admin.booking.create');
    }

    public function store(Request $request) {
        $request->validate([
            'customer_name'   => 'required',
            'whatsapp_number' => 'required',
            'event_address'   => 'required',
            'event_date'      => 'required|date',
            'package_name'    => 'required',
            'package_price'   => 'required',
        ]);

        // Simpan dengan nama kolom sesuai migrasi terbaru
        Booking::create(array_merge($request->all(), ['status' => 'confirmed']));

        return redirect()->route('admin.booking.index')->with('success', 'Pesanan berhasil ditambahkan!');
    }

    public function show($id) {
        $booking = Booking::findOrFail($id);
        return view('admin.booking.detail', compact('booking'));
    }

    public function updateStatus(Request $request, $id) {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status Berhasil Diperbarui!']);
    }

    public function destroy($id) {
        Booking::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Pesanan berhasil dihapus.']);
    }
}