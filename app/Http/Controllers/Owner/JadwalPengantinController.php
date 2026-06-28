<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\JadwalPengantin;
use App\Models\Jadwal;
use App\Models\User;
use App\Models\Paket;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class JadwalPengantinController extends Controller
{
    public function index()
    {
        return view('owner.jadwalpengantin.index');
    }

    public function data(Request $request)
    {
        $query = JadwalPengantin::with('paket')->orderBy('tanggal_awal', 'asc');
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
            ->addColumn('tanggal_full', fn($row) => Carbon::parse($row->tanggal_awal)->translatedFormat('d F Y'))
            ->addColumn('nama_paket', fn($row) => $row->paket ? $row->paket->nama_paket : '-')
            ->addColumn('keterangan_text', fn($row) => $row->keterangan ?? '-')
            ->addColumn('action', fn($row) => '<a href="' . route('owner.jadwalpengantin.edit', $row->id) . '" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i> Edit</a>')
            ->rawColumns(['action'])
            ->make(true);
    }

    public function edit($id)
    {
        $jadwal = JadwalPengantin::findOrFail($id);
        $pakets = Paket::all();
        $kruAsisten = User::where('role', 'kru')->where('jabatan', 'asisten')->get();
        $kruFG = User::where('role', 'kru')->where('jabatan', 'fg')->get();
        $kruLayos = User::where('role', 'kru')->where('jabatan', 'layos')->get();

        return view('owner.jadwalpengantin.edit', compact('jadwal', 'pakets', 'kruAsisten', 'kruFG', 'kruLayos'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPengantin::findOrFail($id);

        $validated = $request->validate([
            'tanggal_awal' => 'required|date',
            'nama' => 'required',
            'paket_id' => 'required',
            'alamat' => 'required',
            'asisten' => 'nullable|array',
            'fg' => 'nullable',
            'layos' => 'nullable',
            'keterangan' => 'nullable'
        ]);

        $d = Carbon::parse($request->tanggal_awal);
        $map = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];

        $validated['asisten'] = $request->has('asisten') ? implode(',', $request->asisten) : null;
        $validated['bulan'] = $map[$d->format('F')];
        $validated['tahun'] = $d->format('Y');

        $jadwal->update($validated);

        // --- LOGIKA KIRIM WA ---
    if ($request->filled('fg')) {
        $namaFG = trim($request->fg); 
        
        // MENCARI USER: Gunakan 'LIKE' agar lebih toleran terhadap perbedaan penulisan
        $userFG = User::where('name', 'LIKE', '%' . $namaFG . '%')->first();

        if ($userFG) {
            // Pastikan kolom yang benar di tabel users Anda (apakah 'phone' atau 'whatsapp_number'?)
            // Jika nama kolom di database Anda adalah 'phone', maka gunakan $userFG->phone
            if (!empty($userFG->phone)) {
                $this->kirimWhatsAppKeKru($userFG, $jadwal, 'Fotografer (FG)');
            } else {
                Log::info("DEBUG: User " . $userFG->name . " ditemukan, tapi kolom 'phone' kosong.");
            }
        } else {
            Log::info("DEBUG: User dengan nama " . $namaFG . " tidak ketemu di tabel users.");
        }
    }

        return redirect()->route('owner.jadwalpengantin.index')->with('swal_success', 'Jadwal diperbarui dan notifikasi dikirim!');
    }
    private function kirimWhatsAppKeKru($user, $jadwal, $posisi)
    {
        $token = "McjqVVgU8QqgqY3TA3z9";

        // Ganti $user->whatsapp_number dengan $user->phone
        // Pastikan kolom 'phone' memang ada di tabel users Anda
        $nomor = trim($user->phone);

        if (empty($nomor)) {
            Log::info("DEBUG: Kru " . $user->name . " tidak punya data di kolom 'phone'.");
            return;
        }

        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }

       $pesan = "🔔 *PENUGASAN BARU: SYSGRA*\n\n" .
             "Halo *" . $user->name . "*,\n" .
             "Anda telah didelegasikan sebagai *" . $posisi . "* untuk agenda pernikahan berikut:\n\n" .
             "--- 📋 DETAIL ACARA 📋 ---\n" .
            "👰 *Pengantin:* " . $jadwal->nama . "\n" .
            "📅 *Tanggal:* " . \Carbon\Carbon::parse($jadwal->tanggal_awal)->translatedFormat('d F Y') . "\n" .
            "📍 *Lokasi:* " . $jadwal->alamat . "\n\n" .
            "Mohon segera cek dashboard untuk detail pekerjaan.\n" .
            "© Griya Rias Asmara";

        try {
            $response = Http::withHeaders(['Authorization' => $token])->post('https://api.fonnte.com/send', [
                'target'  => $nomor,
                'message' => $pesan,
            ]);

            Log::info("DEBUG: Respon Fonnte untuk " . $user->name . ": " . $response->body());
        } catch (\Exception $e) {
            Log::error("DEBUG: Gagal kirim WA ke " . $user->name . ": " . $e->getMessage());
        }
    }
    public function checkKruAvailability(Request $request) // <--- Ubah nama fungsi ke ini
    {
        $tanggalInput = \Carbon\Carbon::parse($request->tanggal)->format('Y-m-d');
        $namaKru = trim($request->nama_kru);
        $jadwalId = $request->jadwal_id;

        $user = \App\Models\User::where('name', $namaKru)->first();
        if (!$user) return response()->json(['is_busy' => false]);

        // Cek di Jadwal GRA
        $isBusyGRA = \App\Models\JadwalPengantin::where('tanggal_awal', $tanggalInput)
            ->where('id', '!=', $jadwalId)
            ->where(function ($q) use ($namaKru) {
                $q->whereRaw("FIND_IN_SET(?, asisten)", [$namaKru])
                    ->orWhere('fg', '=', $namaKru)
                    ->orWhere('layos', '=', $namaKru);
            })->exists();

        // Cek di Jadwal Pribadi (Kru)
        $isBusyPribadi = \App\Models\Jadwal::where('user_id', $user->id)
            ->whereDate('event_date', $tanggalInput)
            ->exists();

        $busy = ($isBusyGRA || $isBusyPribadi);

        \Illuminate\Support\Facades\Log::info("DEBUG: Validasi Kru {$user->name} | Hasil Bentrok: " . ($busy ? 'YA' : 'TIDAK'));

        return response()->json(['is_busy' => $busy]);
    }
    public function print(Request $request)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');

        $query = JadwalPengantin::with('paket');
        if ($bulan) $query->where('bulan', $bulan);
        if ($tahun) $query->where('tahun', $tahun);

        $data = $query->get();

        return Pdf::loadView('owner.jadwalpengantin.print', compact('data', 'bulan', 'tahun'))
            ->setPaper('a4', 'portrait')
            ->download('Laporan_Jadwal_' . ($bulan ?? 'Semua') . '.pdf');
    }
}
