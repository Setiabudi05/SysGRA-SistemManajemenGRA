<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPengantin;
use App\Models\User;
use App\Models\Paket;
use App\Models\Booking;
use App\Notifications\SistemNotifikasi;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalPengantinController extends Controller
{
    public function index()
    {
        return view('admin.jadwalpengantin.index');
    }
    /**
     * Menampilkan data pesanan di tabel admin
     */
    public function data(Request $request)
    {
        $query = JadwalPengantin::with(['paket', 'pesanan'])->orderBy('tanggal_awal', 'asc');

        // Prioritaskan filter tanggal spesifik
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_awal', $request->tanggal);
        } else {
            // Hanya gunakan bulan & tahun jika tanggal tidak dipilih
            if ($request->filled('bulan')) {
                $query->where('bulan', $request->bulan);
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
                // Array pemetaan bulan ke bahasa Indonesia
                $bulanIndo = [
                    'January' => 'Januari',
                    'February' => 'Februari',
                    'March' => 'Maret',
                    'April' => 'April',
                    'May' => 'Mei',
                    'June' => 'Juni',
                    'July' => 'Juli',
                    'August' => 'Agustus',
                    'September' => 'September',
                    'October' => 'Oktober',
                    'November' => 'November',
                    'December' => 'Desember'
                ];

                // Ambil tanggal
                $tgl = \Carbon\Carbon::parse($row->tanggal_awal)->format('d');

                // Ganti nama bulan dari database (kolom $row->bulan) dengan array di atas
                // Jika $row->bulan sudah dalam bahasa Inggris (August), ini akan mengubahnya ke (Agustus)
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

        // TAMBAHKAN TIGA BARIS INI UNTUK MENGIRIM DATA KRU
        $kruAsisten = User::where('role', 'kru')->where('jabatan', 'asisten')->get();
        $kruFG = User::where('role', 'kru')->where('jabatan', 'fg')->get();
        $kruLayos = User::where('role', 'kru')->where('jabatan', 'layos')->get();

        // TAMBAHKAN VARIABLE KE DALAM COMPACT
        return view('admin.jadwalpengantin.edit', compact('jadwal', 'pakets', 'kruAsisten', 'kruFG', 'kruLayos'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPengantin::findOrFail($id);
        $validated = $this->validateRequest($request);
        $mapping = $this->mapBulanTahun($request->tanggal_awal);
        $jadwal->update(array_merge($validated, $mapping));
        return redirect()->route('admin.jadwalpengantin.index')->with('swal_success', 'Jadwal diperbarui!');
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
            'asisten'       => 'nullable|string',
            'fg'            => 'nullable|string',
            'layos'         => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);
    }

    private function mapBulanTahun($date)
    {
        $d = Carbon::parse($date);
        // Pastikan menyimpan format Bahasa Indonesia
        $map = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
        return ['bulan' => $map[$d->format('F')], 'tahun' => $d->format('Y')];
    }

    public function print(Request $request)
    {
        // GANTI 'jadwalPengantin' menjadi 'pesanan' (atau nama relasi yang benar di Model)
        $query = JadwalPengantin::with(['paket', 'pesanan']);

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $jadwal = $query->get();
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        return Pdf::loadView('admin.jadwalpengantin.print', compact('jadwal', 'bulan', 'tahun'))
            ->setPaper('A4', 'portrait')
            ->stream('jadwal-pengantin.pdf');
    }
}
