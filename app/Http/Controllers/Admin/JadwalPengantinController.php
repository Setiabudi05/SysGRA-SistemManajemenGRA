<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPengantin;
use App\Models\User;
use App\Models\Paket;
use App\Models\Booking;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalPengantinController extends Controller
{
    public function index()
    {
        return view('admin.jadwalpengantin.index');
    }

    public function data(Request $request)
    {
        $query = JadwalPengantin::with(['paket', 'pesanan'])->orderBy('tanggal_awal', 'asc');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_awal', $request->tanggal);
        } else {
            if ($request->filled('bulan')) {
                // Fleksibel: mendukung filter nama bulan Indo atau Inggris
                $bulanInput = $request->bulan;
                $query->where(function ($q) use ($bulanInput) {
                    $q->where('bulan', $bulanInput)
                        ->orWhere('bulan', $this->translateBulan($bulanInput));
                });
            }
            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nama', fn($row) => $row->pesanan ? $row->pesanan->bride_groom_name : $row->nama)
            ->editColumn('alamat', fn($row) => $row->pesanan ? $row->pesanan->event_address : $row->alamat)
            ->addColumn('tanggal_full', function ($row) {
                $bulanIndo = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
                $tgl = Carbon::parse($row->tanggal_awal)->format('d');
                $namaBulan = isset($bulanIndo[$row->bulan]) ? $bulanIndo[$row->bulan] : $row->bulan;
                return "$tgl $namaBulan $row->tahun";
            })
            ->editColumn('asisten', fn($row) => $row->asisten ?? '-')
            ->editColumn('fg', fn($row) => $row->fg ?? '-')
            ->editColumn('layos', fn($row) => $row->layos ?? '-')
            ->addColumn('keterangan_text', fn($row) => $row->keterangan ?? '-')
            ->addColumn('action', function ($row) {
                return '
                <a href="' . route('admin.jadwalpengantin.edit', $row->id) . '" class="btn btn-warning btn-sm shadow-sm"><i class="bi bi-pencil-square"></i></a>
                <button onclick="hapusJadwal(' . $row->id . ')" class="btn btn-danger btn-sm shadow-sm"><i class="bi bi-trash"></i></button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Fungsi bantu untuk sinkronisasi bahasa
    private function translateBulan($namaBulan)
    {
        $map = ['Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March', 'April' => 'April', 'Mei' => 'May', 'Juni' => 'June', 'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September', 'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December'];
        return $map[$namaBulan] ?? $namaBulan;
    }

    public function create()
    {
        $pakets = Paket::all();
        return view('admin.jadwalpengantin.create', compact('pakets'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $mapping = $this->mapBulanTahun($request->tanggal_awal);
        JadwalPengantin::create(array_merge($validated, $mapping));
        return redirect()->route('admin.jadwalpengantin.index')->with('swal_success', 'Jadwal berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $jadwal = JadwalPengantin::findOrFail($id);
        $pakets = Paket::all();
        $kruAsisten = User::where('role', 'kru')->where('jabatan', 'asisten')->get();
        $kruFG = User::where('role', 'kru')->where('jabatan', 'fg')->get();
        $kruLayos = User::where('role', 'kru')->where('jabatan', 'layos')->get();

        return view('admin.jadwalpengantin.edit', compact('jadwal', 'pakets', 'kruAsisten', 'kruFG', 'kruLayos'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPengantin::findOrFail($id);
        $validated = $this->validateRequest($request);

        if ($request->has('asisten') && is_array($request->asisten)) {
            $validated['asisten'] = implode(',', $request->asisten);
        }

        $mapping = $this->mapBulanTahun($request->tanggal_awal);
        $jadwal->update(array_merge($validated, $mapping));

        return redirect()->route('admin.jadwalpengantin.index')->with('swal_success', 'Jadwal diperbarui!');
    }

    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $oldStatus = $booking->status;

        $booking->update(['status' => $request->status]);

        if ($request->status == 'confirmed' && $oldStatus != 'confirmed') {
            $mapping = $this->mapBulanTahun($booking->event_date);
            \App\Models\JadwalPengantin::updateOrCreate(
                ['booking_id' => $booking->id],
                array_merge([
                    'tanggal_awal' => $booking->event_date,
                    'nama'         => $booking->bride_groom_name,
                    'alamat'       => $booking->event_address,
                    'paket_id'     => $booking->paket_id,
                ], $mapping)
            );
        } elseif ($request->status != 'confirmed' && $oldStatus == 'confirmed') {
            \App\Models\JadwalPengantin::where('booking_id', $booking->id)->delete();
        }

        return redirect()->route('admin.booking.index')->with('success', 'Status berhasil diperbarui!');
    }

    public function destroy($id)
    {
        JadwalPengantin::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'tanggal_awal'  => 'required|date',
            'nama'          => 'required|string|max:255',
            'paket_id'      => 'required',
            'alamat'        => 'required|string',
            'asisten'       => 'nullable',
            'fg'            => 'nullable|string',
            'layos'         => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);
    }

    private function mapBulanTahun($date)
    {
        $d = Carbon::parse($date);
        $map = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
        return ['bulan' => $map[$d->format('F')], 'tahun' => $d->format('Y')];
    }

    public function print(Request $request)
    {
        $query = JadwalPengantin::with(['paket', 'pesanan']);

        // Gunakan filter yang sama dengan fungsi 'data' agar konsisten
        if ($request->filled('bulan')) {
            $bulanInput = $request->bulan;
            $bulanInggris = $this->translateBulan($bulanInput);

            $query->where(function ($q) use ($bulanInput, $bulanInggris) {
                $q->where('bulan', $bulanInput)
                    ->orWhere('bulan', $bulanInggris);
            });
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $jadwal = $query->get();

        // Pastikan variabel ini diteruskan ke view
        return Pdf::loadView('admin.jadwalpengantin.print', [
            'jadwal' => $jadwal,
            'bulan'  => $request->bulan ?? 'Semua',
            'tahun'  => $request->tahun ?? ''
        ])->setPaper('A4', 'portrait')
            ->stream('jadwal-pengantin.pdf');
    }
}
