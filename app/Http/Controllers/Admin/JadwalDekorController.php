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
        // Mengambil data master dari Jadwal Pengantin dengan urutan tanggal awal
        $query = JadwalPengantin::with(['paket', 'jadwalDekor'])->orderBy('tanggal_awal', 'asc');

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
            ->addColumn('tanggal_full', function ($row) {
                // Format: "23 - 24 Mei 2026"
                $awal = $row->tanggal_awal ? Carbon::parse($row->tanggal_awal)->format('d') : '-';
                $akhir = $row->tanggal_akhir ? Carbon::parse($row->tanggal_akhir)->format('d') : null;
                $bulanTahun = $row->bulan . ' ' . $row->tahun;

                if ($akhir && $awal != $akhir) {
                    return "$awal - $akhir $bulanTahun";
                }
                return "$awal $bulanTahun";
            })
            ->addColumn('nama', fn($row) => $row->nama)
            ->addColumn('paket', fn($row) => $row->paket?->nama_paket ?? '-')
            ->addColumn('foto', function ($row) {
                if ($row->jadwalDekor && $row->jadwalDekor->foto) {
                    return '<div class="img-container shadow-sm text-center"><img src="' . asset('storage/' . $row->jadwalDekor->foto) . '" alt="foto" width="60" class="rounded"></div>';
                }
                return '<div class="text-center"><span class="badge bg-light-secondary text-muted">Belum ada foto</span></div>';
            })
            ->addColumn('deskripsi', function ($row) {
                return $row->jadwalDekor->deskripsi ?? '<span class="text-muted italic">Rincian belum diisi...</span>';
            })
            ->addColumn('action', function ($row) {
                $editUrl = route('admin.jadwaldekor.edit', ['id' => $row->id]);
                $dekorId = $row->jadwalDekor ? $row->jadwalDekor->id : 'null';

                return '
                <div class="d-flex justify-content-center gap-2">
                    <a href="' . $editUrl . '" class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <button onclick="hapusJadwal(' . $dekorId . ')" class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>';
            })
            ->rawColumns(['action', 'foto', 'deskripsi'])
            ->make(true);
    }

    /**
     * Tampilkan form tambah jadwal dekorasi.
     */
    public function create(Request $request)
    {
        $pakets = Paket::all();

        // Ambil ID dari tombol "Lengkapi Dekor" jika ada
        $selectedPengantinId = $request->get('pengantin_id');

        // Query cerdas untuk dropdown jadwals
        $jadwals = JadwalPengantin::with('paket')
            ->where(function ($query) use ($selectedPengantinId) {
                // Kondisi A: Tampilkan yang hari ini ke depan DAN belum punya dekorasi
                $query->where('tanggal_awal', '>=', now()->toDateString())
                    ->whereDoesntHave('jadwalDekor'); // Sesuaikan 'jadwalDekor' dengan nama relasi di Model JadwalPengantin
            })
            // Kondisi B: ATAU jika dia diklik dari tombol "Lengkapi Dekor", paksa dia tetap muncul
            ->when($selectedPengantinId, function ($query, $id) {
                return $query->orWhere('id', $id);
            })
            ->orderBy('tanggal_awal', 'asc')
            ->get();

        // Cek data pengantin pilihan untuk auto-fill bawaan kamu
        $selectedPengantin = null;
        if ($selectedPengantinId) {
            $selectedPengantin = JadwalPengantin::find($selectedPengantinId);
        }

        return view('admin.jadwaldekor.create', compact('pakets', 'jadwals', 'selectedPengantin'));
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
        // Cek dulu apakah $id ini milik JadwalPengantin yang belum punya dekor, atau sudah ada
        $pengantin = JadwalPengantin::with(['paket', 'jadwalDekor'])->findOrFail($id);

        // Jika rincian dekorasi belum ada, kita buat objek kosong agar form tetap bisa tampil
        $jadwal = $pengantin->jadwalDekor ?? new JadwalDekor();

        // Kirim data master pengantin agar form bisa auto-fill rincian nama/tanggal
        $jadwals = JadwalPengantin::with('paket')->orderBy('tanggal_awal', 'desc')->get();
        $pakets = Paket::all();

        return view('admin.jadwaldekor.edit', compact('jadwal', 'jadwals', 'pakets', 'pengantin'));
    }

    /**
     * Perbarui jadwal dekorasi.
     */
    public function update(Request $request, $id)
    {
        $validated = $this->validateRequest($request);
        $jadwalPengantin = JadwalPengantin::findOrFail($request->jadwal_pengantin_id);
        $syncData = $this->syncWithJadwalPengantin($jadwalPengantin);

        $data = array_merge($validated, $syncData);

        // Cari data dekorasi berdasarkan pengantin_id
        $jadwal = JadwalDekor::where('jadwal_pengantin_id', $id)->first();

        if ($request->hasFile('foto')) {
            if ($jadwal && $jadwal->foto) {
                Storage::disk('public')->delete($jadwal->foto);
            }
            $data['foto'] = $request->file('foto')->store('jadwal_foto', 'public');
        }

        // Gunakan updateOrCreate untuk fleksibilitas
        JadwalDekor::updateOrCreate(
            ['jadwal_pengantin_id' => $id],
            $data
        );

        return redirect()->route('admin.jadwaldekor.index', [
            'bulan' => $request->last_bulan,
            'tahun' => $request->last_tahun
        ])->with('swal_success', 'Jadwal dekorasi berhasil diperbarui!');
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
        // KUNCI: Query dari JadwalPengantin agar semua data muncul
        $query = \App\Models\JadwalPengantin::with(['paket', 'jadwalDekor']);

        // Filter berdasarkan bulan dan tahun yang ada di tabel JadwalPengantin
        if ($request->filled('bulan')) {
            $query->where('bulan', trim($request->bulan));
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', trim($request->tahun));
        }

        $jadwal = $query->orderBy('tanggal_awal', 'asc')->get();
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Pastikan view Anda menggunakan variabel $jadwal yang berisi kumpulan JadwalPengantin
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
