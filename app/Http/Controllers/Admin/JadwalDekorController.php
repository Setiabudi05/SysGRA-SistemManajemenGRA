<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalDekor;
use App\Models\Paket;
use App\Models\JadwalPengantin;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class JadwalDekorController extends Controller
{
    /**
     * Tampilkan halaman utama jadwal dekorasi.
     */
    public function index()
    {
        return view('admin.jadwaldekor.index');
    }

    /**
     * Ambil data untuk DataTables dengan filter bulan dan tahun.
     */
    public function data(Request $request)
    {
        $query = JadwalDekor::with(['paket', 'jadwalPengantin']);

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($row) {
                if ($row->jadwalPengantin) {
                    $awal = $row->jadwalPengantin->tanggal_awal;
                    $akhir = $row->jadwalPengantin->tanggal_akhir;

                    if ($awal && $akhir) {
                        return Carbon::parse($awal)->format('d') . ' - ' . Carbon::parse($akhir)->format('d');
                    } elseif ($awal) {
                        return Carbon::parse($awal)->format('d');
                    }
                }
                return '-';
            })
            ->editColumn('paket', fn($row) => $row->paket?->nama_paket ?? '-')
            ->editColumn('foto', function ($row) {
                return $row->foto
                    ? '<div class="img-container shadow-sm"><img src="' . asset('storage/' . $row->foto) . '" alt="foto" width="60"></div>'
                    : '-';
            })
            ->addColumn('action', function ($row) use ($request) {
                // Selipkan parameter filter aktif ke URL Edit
                $params = [
                    'id' => $row->id,
                    'f_bulan' => $request->bulan,
                    'f_tahun' => $request->tahun
                ];

                return '
            <div class="d-flex justify-content-center gap-2">
                <a href="' . route('admin.jadwaldekor.edit', $params) . '" 
                   class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                   <i class="bi bi-pencil-square"></i> Edit
                </a>
                <button onclick="hapusJadwal(' . $row->id . ')" 
                        class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>';
            })
            ->rawColumns(['action', 'foto'])
            ->make(true);
    }

    /**
     * Tampilkan form tambah jadwal dekorasi.
     */
    public function create()
    {
        $pakets = Paket::all();

        // Pastikan mengambil SEMUA data tanpa filter tahun
        $jadwals = JadwalPengantin::with('paket')
            ->orderBy('tanggal_awal', 'asc') // Gunakan ASC agar urutan dari yang terdekat
            ->get();

        return view('admin.jadwaldekor.create', compact('pakets', 'jadwals'));
    }

    /**
     * Simpan jadwal dekorasi baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $jadwalPengantin = JadwalPengantin::findOrFail($request->jadwal_pengantin_id);

        $syncData = $this->syncWithJadwalPengantin($jadwalPengantin);
        $data = array_merge($validated, $syncData);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('jadwal_foto', 'public');
        }

        JadwalDekor::create($data);

        // Redirect ke filter sesuai data yang baru ditambah
        return redirect()->route('admin.jadwaldekor.index', [
            'bulan' => $syncData['bulan'],
            'tahun' => $syncData['tahun']
        ])->with('swal_success', 'Jadwal berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit jadwal dekorasi.
     */
    public function edit($id)
    {
        $jadwal = JadwalDekor::with('jadwalPengantin')->findOrFail($id);
        $pakets = Paket::all();
        $jadwals = JadwalPengantin::with('paket')->orderBy('tanggal_awal', 'desc')->get();

        return view('admin.jadwaldekor.edit', compact('jadwal', 'pakets', 'jadwals'));
    }

    /**
     * Perbarui jadwal dekorasi.
     */
    public function update(Request $request, $id)
    {
        $jadwal = JadwalDekor::findOrFail($id);
        $validated = $this->validateRequest($request);

        $jadwalPengantin = JadwalPengantin::findOrFail($request->jadwal_pengantin_id);
        $syncData = $this->syncWithJadwalPengantin($jadwalPengantin);
        $data = array_merge($validated, $syncData);

        if ($request->hasFile('foto')) {
            if ($jadwal->foto) {
                Storage::disk('public')->delete($jadwal->foto);
            }
            $data['foto'] = $request->file('foto')->store('jadwal_foto', 'public');
        }

        $jadwal->update($data);

        // Redirect kembali menggunakan parameter 'last_filter'
        return redirect()->route('admin.jadwaldekor.index', [
            'bulan' => $request->last_bulan,
            'tahun' => $request->last_tahun
        ])->with('swal_success', 'Jadwal berhasil diperbarui!');
    }
    /**
     * Hapus jadwal dekorasi.
     */
    public function destroy($id)
    {
        try {
            $jadwal = JadwalDekor::findOrFail($id);

            // Hapus foto jika ada di storage
            if ($jadwal->foto) {
                Storage::disk('public')->delete($jadwal->foto);
            }

            $jadwal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal dekorasi berhasil dihapus permanen.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Cetak jadwal dekorasi ke PDF.
     */
    public function print(Request $request)
    {
        $query = JadwalDekor::with(['paket', 'jadwalPengantin']);

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $jadwal = $query->get();
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        return Pdf::loadView('admin.jadwaldekor.print', compact('jadwal', 'bulan', 'tahun'))
            ->setPaper('A4', 'portrait')
            ->stream('jadwal-dekorasi.pdf');
    }

    /**
     * Helper: Validasi Request.
     */
    private function validateRequest(Request $request)
    {
        return $request->validate([
            'jadwal_pengantin_id' => 'required|exists:jadwal_pengantins,id',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    }

    /**
     * Helper: Sinkronisasi data otomatis dari Jadwal Pengantin.
     */
    private function syncWithJadwalPengantin($jadwalPengantin)
    {
        $tanggalAwal = Carbon::parse($jadwalPengantin->tanggal_awal);
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
            'bulan' => $mapBulan[$tanggalAwal->format('F')],
            'tahun' => $tanggalAwal->year,
            'nama' => $jadwalPengantin->nama,
            'alamat' => $jadwalPengantin->alamat,
            'paket_id' => $jadwalPengantin->paket_id,
        ];
    }
}