<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPengantin;
use App\Models\User;
use App\Models\Paket;
use App\Notifications\SistemNotifikasi; // Memastikan Notifikasi Ter-import
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Memastikan PDF Ter-import

class JadwalPengantinController extends Controller
{
    public function index()
    {
        return view('admin.jadwalpengantin.index');
    }

    public function data(Request $request)
    {
        $query = JadwalPengantin::with('paket')->orderBy('tanggal_awal', 'asc');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_full', function ($row) {
                $tglAwal = Carbon::parse($row->tanggal_awal)->format('d');
                $tglAkhir = $row->tanggal_akhir ? Carbon::parse($row->tanggal_akhir)->format('d') : null;
                $bulanTahun = $row->bulan . ' ' . $row->tahun;

                if ($tglAkhir && $tglAwal != $tglAkhir) {
                    return "$tglAwal-$tglAkhir $bulanTahun";
                }
                return "$tglAwal $bulanTahun";
            })
            ->editColumn('asisten', function ($row) {
                return $row->asisten ?? '<span class="text-muted small">Belum diplot</span>';
            })
            ->editColumn('fg', function ($row) {
                return $row->fg ?? '<span class="text-muted small">Belum diplot</span>';
            })
            ->editColumn('layos', function ($row) {
                return $row->layos ?? '<span class="text-muted small">Belum diplot</span>';
            })
            ->addColumn('keterangan_text', function ($row) {
                if ($row->keterangan) {
                    return '<i class="bi bi-info-circle text-primary me-1"></i>' . $row->keterangan;
                }
                return '<span class="text-muted small italic">Belum ada catatan</span>';
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('admin.jadwalpengantin.edit', $row->id) . '" class="btn btn-warning btn-sm shadow-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>';
            })
            ->rawColumns(['asisten', 'fg', 'layos', 'action', 'keterangan_text'])
            ->make(true);
    }

    /**
     * Menampilkan form tambah jadwal.
     */
    public function create()
    {
        $pakets = Paket::all();

        // Pastikan 3 baris pemisahan kru ini ada di fungsi create()
        $kruAsisten = User::where('role', 'kru')->where('jabatan', 'asisten')->get();
        $kruFG      = User::where('role', 'kru')->where('jabatan', 'fg')->get();
        $kruLayos   = User::where('role', 'kru')->where('jabatan', 'layos')->get();

        // Kirimkan semua variabel kru ke dalam view
        return view('admin.jadwalpengantin.create', compact('pakets', 'kruAsisten', 'kruFG', 'kruLayos'));
    }

    /**
     * Menyimpan jadwal baru ke database DAN mengirim notifikasi.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $mapping = $this->mapBulanTahun($request->tanggal_awal);

        // Gabungkan array asisten menjadi string sebelum disimpan (Sama seperti logika update)
        if ($request->has('asisten') && is_array($request->asisten)) {
            $validated['asisten'] = implode(',', $request->asisten);
        }

        $data = array_merge($validated, $mapping);
        $jadwal = JadwalPengantin::create($data);

        // --- LOGIKA NOTIFIKASI OTOMATIS INTERNAL SYSTEM ---
        // 1. Notif untuk Fotografer (FG)
        if ($request->filled('fg')) {
            $userFg = User::where('name', $request->fg)->first();
            if ($userFg) {
                $userFg->notify(new SistemNotifikasi([
                    'judul' => 'Penugasan Baru (FG)',
                    'pesan' => 'Jadwal baru: ' . $jadwal->nama . ' di ' . $jadwal->alamat,
                    'icon'  => 'bi-camera',
                    'link'  => route('kru.jadwal.index')
                ]));
            }
        }

        // 2. Notif untuk Asisten (Mendukung Multi-Asisten String)
        if ($jadwal->asisten) {
            $asistenList = explode(',', $jadwal->asisten);
            foreach ($asistenList as $namaAsisten) {
                $userAsisten = User::where('name', trim($namaAsisten))->first();
                if ($userAsisten) {
                    $userAsisten->notify(new SistemNotifikasi([
                        'judul' => 'Penugasan Baru (Asisten)',
                        'pesan' => 'Kamu ditugaskan di acara ' . $jadwal->nama,
                        'icon'  => 'bi-person-badge',
                        'link'  => route('kru.jadwal.index')
                    ]));
                }
            }
        }

        return redirect()->route('admin.jadwalpengantin.index', [
            'bulan' => $mapping['bulan'],
            'tahun' => $mapping['tahun']
        ])->with('swal_success', 'Jadwal berhasil ditambahkan & Notifikasi terkirim!');
    }

    /**
     * Menampilkan form edit jadwal.
     */
    public function edit($id)
    {
        $jadwal = JadwalPengantin::findOrFail($id);
        $pakets = Paket::all();

        // Mengambil data kru spesifik sesuai jabatan untuk dropdown terpisah
        $kruAsisten = User::where('role', 'kru')->where('jabatan', 'asisten')->get();
        $kruFG      = User::where('role', 'kru')->where('jabatan', 'fg')->get();
        $kruLayos   = User::where('role', 'kru')->where('jabatan', 'layos')->get();

        return view('admin.jadwalpengantin.edit', compact('jadwal', 'pakets', 'kruAsisten', 'kruFG', 'kruLayos'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPengantin::findOrFail($id);

        $validated = $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'nullable|date',
            'nama' => 'required|string',
            'paket_id' => 'required',
            'alamat' => 'required|string',
            'asisten' => 'nullable|array',
            'fg' => 'nullable|string',
            'layos' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->has('asisten')) {
            $validated['asisten'] = implode(',', $request->asisten);
        } else {
            $validated['asisten'] = null;
        }

        // Sinkronisasi pemetaan ulang bulan dan tahun jika tanggal awal diganti saat edit
        $mapping = $this->mapBulanTahun($request->tanggal_awal);
        $data = array_merge($validated, $mapping);

        $jadwal->update($data);

        return redirect()->route('admin.jadwalpengantin.index')
            ->with('swal_success', 'Jadwal dan Penugasan berhasil diperbarui oleh Admin!');
    }

    /**
     * Menghapus jadwal.
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
     * Cetak ke PDF.
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
            $awal = $item->tanggal_awal ? \Carbon\Carbon::parse($item->tanggal_awal)->format('d') : '-';
            $akhir = $item->tanggal_akhir ? \Carbon\Carbon::parse($item->tanggal_akhir)->format('d') : null;
            $item->tanggal_display = $akhir ? "$awal - $akhir" : $awal;
            return $item;
        });

        // PERBAIKAN UTAMA: Tambahkan setOptions untuk mematikan paksa deteksi image dari DOMPDF core
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.jadwalpengantin.print', [
            'jadwal' => $jadwal,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ])
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true
            ]);

        return $pdf->stream('jadwal_pengantin.pdf');
    }

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
} // Penutup Class Utama yang Benar