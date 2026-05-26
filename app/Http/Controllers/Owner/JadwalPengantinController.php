<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPengantin;
use App\Models\User;
use App\Models\Paket;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JadwalPengantinController extends Controller
{
    public function index()
    {
        return view('owner.jadwalpengantin.index');
    }

    public function data(Request $request)
    {
        // Urutkan berdasarkan tanggal awal agar kronologis (dari awal ke akhir bulan)
        $query = JadwalPengantin::with('paket')->orderBy('tanggal_awal', 'asc');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            // Menggabungkan Tanggal, Bulan, dan Tahun
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
            // Menampilkan info catatan pelanggan di index DataTables Owner jika ada
            ->addColumn('keterangan_text', function ($row) {
                if ($row->keterangan) {
                    return '<i class="bi bi-info-circle text-primary me-1"></i>' . $row->keterangan;
                }
                return '<span class="text-muted small italic">Belum ada catatan</span>';
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('owner.jadwalpengantin.edit', $row->id) . '" class="btn btn-warning btn-sm shadow-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>';
            })
            ->rawColumns(['asisten', 'fg', 'layos', 'action', 'keterangan_text'])
            ->make(true);
    }

    public function edit($id)
    {
        $jadwal = JadwalPengantin::findOrFail($id);
        $pakets = Paket::all();

        // Mengambil data kru spesifik sesuai jabatan untuk dropdown terpisah
        $kruAsisten = User::where('role', 'kru')->where('jabatan', 'asisten')->get();
        $kruFG      = User::where('role', 'kru')->where('jabatan', 'fg')->get();
        $kruLayos   = User::where('role', 'kru')->where('jabatan', 'layos')->get();

        return view('owner.jadwalpengantin.edit', compact('jadwal', 'pakets', 'kruAsisten', 'kruFG', 'kruLayos'));
    }

   public function update(Request $request, $id)
{
    $jadwal = JadwalPengantin::findOrFail($id);

    // 1. Validasi awal
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

    // 2. Olah data asisten array ke string
    if ($request->has('asisten')) {
        $validated['asisten'] = implode(',', $request->asisten);
    } else {
        $validated['asisten'] = null;
    }

    // KUNCI PENYELAMAT: Jika request keterangan kosong, ganti dengan string kosong "" (bukan NULL)
    // agar database NOT NULL kamu tidak melontarkan error crash lagi
    if (empty($validated['keterangan'])) {
        $validated['keterangan'] = ""; 
    }

    // 3. Update data ke database
    $jadwal->update($validated);

    // 4. Kirim notifikasi WA
    $this->sendWhatsAppNotification($jadwal);

    return redirect()->route('owner.jadwalpengantin.index')
        ->with('swal_success', 'Jadwal Penugasan berhasil diperbarui!');
}
    /**
     * Logika utama pengiriman WA menggunakan API Fonnte
     */
    private function sendWhatsAppNotification($jadwal)
    {
        $token = "McjqVVgU8QqgqY3TA3z9";

        $asistenList = $jadwal->asisten ? explode(',', $jadwal->asisten) : [];
        $kruTugas = [];

        foreach ($asistenList as $namaAsisten) {
            if (!empty($namaAsisten)) {
                $kruTugas[] = ['jabatan' => 'Asisten Rias', 'nama' => trim($namaAsisten)];
            }
        }

        if (!empty($jadwal->fg)) {
            $kruTugas[] = ['jabatan' => 'Photographer (FG)', 'nama' => $jadwal->fg];
        }
        if (!empty($jadwal->layos)) {
            $kruTugas[] = ['jabatan' => 'Penanggung Jawab Layos', 'nama' => $jadwal->layos];
        }

        foreach ($kruTugas as $tugas) {
            $user = User::where('name', $tugas['nama'])->first();

            if ($user && $user->phone) {
                $pesan = "🔔 *PENUGASAN BARU: SYSGRA*\n\n" .
                    "Halo *{$user->name}*,\n" .
                    "Anda telah ditugaskan sebagai *{$tugas['jabatan']}*.\n\n" .
                    "--- DETAIL ACARA ---\n" .
                    "👰 *Pengantin:* {$jadwal->nama}\n" .
                    "📅 *Tanggal:* " . Carbon::parse($jadwal->tanggal_awal)->translatedFormat('d F Y') . "\n" .
                    "📍 *Lokasi:* {$jadwal->alamat}\n";

                // Sisipkan catatan tambahan konsumen ke pesan WA jika ada isi keterangan
                if ($jadwal->keterangan) {
                    $pesan .= "📝 *Catatan Khusus:* {$jadwal->keterangan}\n";
                }

                $pesan .= "\nMohon segera berkoordinasi.\n© Griya Rias Asmara";

                $response = Http::withoutVerifying()
                    ->withHeaders(['Authorization' => $token])
                    ->post('https://api.fonnte.com/send', [
                        'target' => $user->phone,
                        'message' => $pesan,
                        'delay' => '2',
                    ]);

                Log::info("WA Terkirim ke {$user->name} sebagai {$tugas['jabatan']}");
            }
        }
    }
    public function print(Request $request)
    {
        $query = JadwalPengantin::with('paket');

        // Filter dinamis dari halaman index Owner
        if ($request->filled('bulan')) {
            $query->where('bulan', trim($request->bulan));
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', trim($request->tahun));
        }

        // Ambil data dan konversi tampilan format tanggal
        $jadwal = $query->orderBy('tanggal_awal', 'asc')->get()->map(function ($item) {
            $awal = $item->tanggal_awal ? Carbon::parse($item->tanggal_awal)->format('d') : '-';
            $akhir = $item->tanggal_akhir ? Carbon::parse($item->tanggal_akhir)->format('d') : null;
            $item->tanggal_display = $akhir && $akhir != $awal ? "$awal - $akhir" : $awal;
            return $item;
        });

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Render ke view cetak khusus folder owner
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.jadwalpengantin.print', compact('jadwal', 'bulan', 'tahun'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true
            ]);

        return $pdf->stream('laporan_jadwal_pengantin.pdf');
    }
}
