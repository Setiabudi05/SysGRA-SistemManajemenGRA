<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPengantin;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\Paket;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalPengantinController extends Controller
{
    /**
     * Menampilkan halaman daftar jadwal pengantin.
     */
    public function index()
    {
        return view('admin.jadwalpengantin.index');
    }

    /**
     * Mengambil data untuk DataTables dengan filter bulan dan tahun.
     */
    public function data(Request $request)
    {
        $query = JadwalPengantin::with('paket');

        // Filter pencarian
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($row) {
                $awal = $row->tanggal_awal ? Carbon::parse($row->tanggal_awal)->format('d') : '-';
                $akhir = $row->tanggal_akhir ? Carbon::parse($row->tanggal_akhir)->format('d') : null;
                return $akhir ? "$awal - $akhir" : $awal;
            })
            ->addColumn('paket', fn($row) => $row->paket?->nama_paket ?? '-')
            ->addColumn('action', function ($row) use ($request) {
                // Ambil filter aktif dari request pencarian
                $params = [
                    'id' => $row->id,
                    'f_bulan' => $request->bulan,
                    'f_tahun' => $request->tahun
                ];

                return '
            <div class="d-flex justify-content-center gap-2">
                <a href="' . route('admin.jadwalpengantin.edit', $params) . '" 
                   class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                   <i class="bi bi-pencil-square"></i> Edit
                </a>
                <button onclick="hapusJadwal(' . $row->id . ')" 
                        class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Menampilkan form tambah jadwal.
     */
    public function create()
    {
        $pakets = Paket::all();
        return view('admin.jadwalpengantin.create', compact('pakets'));
    }

    /**
     * Menyimpan jadwal baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        // Mapping bulan dan tahun otomatis dari input tanggal_awal
        $mapping = $this->mapBulanTahun($request->tanggal_awal);
        $data = array_merge($validated, $mapping);

        JadwalPengantin::create($data);

        // Redirect ke index dengan membawa parameter bulan dan tahun yang baru diinput
        return redirect()->route('admin.jadwalpengantin.index', [
            'bulan' => $mapping['bulan'], // Contoh: "Januari"
            'tahun' => $mapping['tahun']  // Contoh: "2026"
        ])->with('swal_success', 'Jadwal berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit jadwal.
     */
    public function edit($id)
    {
        $jadwal = JadwalPengantin::findOrFail($id);
        $pakets = Paket::all();
        return view('admin.jadwalpengantin.edit', compact('jadwal', 'pakets'));
    }

    /**
     * Memperbarui data jadwal di database.
     */
    public function update(Request $request, $id)
    {
        $validated = $this->validateRequest($request);
        $jadwal = JadwalPengantin::findOrFail($id);

        $data = array_merge($validated, $this->mapBulanTahun($request->tanggal_awal));
        $jadwal->update($data);

        // Ambil parameter filter asal
        $lastBulan = $request->input('last_bulan');
        $lastTahun = $request->input('last_tahun');

        // Redirect kembali ke index dengan parameter filter agar tidak reset ke bulan sekarang
        return redirect()->route('admin.jadwalpengantin.index', [
            'bulan' => $lastBulan,
            'tahun' => $lastTahun
        ])->with('swal_success', 'Jadwal berhasil diperbarui!');
    }

    /**
     * Menghapus jadwal melalui AJAX.
     */
    public function destroy($id)
    {
        try {
            $jadwal = JadwalPengantin::findOrFail($id);
            $jadwal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal pengantin telah berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mencetak jadwal pengantin ke format PDF.
     */
    public function print(Request $request)
    {
        $query = JadwalPengantin::with('paket');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $jadwal = $query->orderBy('tanggal_awal', 'asc')->get()->map(function ($item) {
            $awal = $item->tanggal_awal ? Carbon::parse($item->tanggal_awal)->format('d') : '-';
            $akhir = $item->tanggal_akhir ? Carbon::parse($item->tanggal_akhir)->format('d') : null;
            $item->tanggal_display = $akhir ? "$awal - $akhir" : $awal;
            return $item;
        });

        $pdf = Pdf::loadView('admin.jadwalpengantin.print', [
            'jadwal' => $jadwal,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('jadwal_pengantin.pdf');
    }

    /**
     * Fungsi Helper: Validasi Request (untuk store & update).
     */
    private function validateRequest(Request $request)
    {
        return $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'paket_id' => 'required|exists:pakets,id',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'asisten' => 'nullable|string|max:255',
            'fg' => 'nullable|string|max:255',
            'layos' => 'nullable|string|max:255',
        ]);
    }

    /**
     * Fungsi Helper: Mapping Nama Bulan Indonesia dan Tahun.
     */
    private function mapBulanTahun($date)
    {
        $carbonDate = Carbon::parse($date);
        $mapBulan = [
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
            'December' => 'Desember',
        ];

        return [
            'bulan' => $mapBulan[$carbonDate->format('F')],
            'tahun' => $carbonDate->format('Y'),
        ];
    }
}