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
    public function index()
    {
        return view('admin.jadwaldekor.index');
    }

    public function data(Request $request)
    {
        $query = JadwalPengantin::with(['paket', 'jadwalDekor'])->orderBy('tanggal_awal', 'asc');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_awal', $request->tanggal);
        } else {
            if ($request->filled('bulan')) {
                // Perbaikan: Menggunakan fleksibilitas bahasa agar data tetap muncul
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
            ->addColumn('tanggal_full', function ($row) {
                $awal = $row->tanggal_awal ? Carbon::parse($row->tanggal_awal)->format('d') : '-';
                $akhir = $row->tanggal_akhir ? Carbon::parse($row->tanggal_akhir)->format('d') : null;
                $bulanTahun = $row->bulan . ' ' . $row->tahun;
                return ($akhir && $awal != $akhir) ? "$awal - $akhir $bulanTahun" : "$awal $bulanTahun";
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
                    <a href="' . $editUrl . '" class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm"><i class="bi bi-pencil-square"></i> Edit</a>
                    <button onclick="hapusJadwal(' . $dekorId . ')" class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm"><i class="bi bi-trash"></i> Hapus</button>
                </div>';
            })
            ->rawColumns(['action', 'foto', 'deskripsi'])
            ->make(true);
    }

    // Fungsi pembantu untuk sinkronisasi bahasa (PENTING)
    private function translateBulan($namaBulan)
    {
        $map = ['Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March', 'April' => 'April', 'Mei' => 'May', 'Juni' => 'June', 'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September', 'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December'];
        return $map[$namaBulan] ?? $namaBulan;
    }

    public function create(Request $request)
    {
        $pakets = Paket::all();
        $selectedPengantinId = $request->get('pengantin_id');
        $jadwals = JadwalPengantin::with('paket')
            ->where(function ($query) use ($selectedPengantinId) {
                $query->where('tanggal_awal', '>=', now()->toDateString())
                      ->whereDoesntHave('jadwalDekor');
            })
            ->when($selectedPengantinId, function ($query, $id) {
                return $query->orWhere('id', $id);
            })
            ->orderBy('tanggal_awal', 'asc')
            ->get();

        $selectedPengantin = $selectedPengantinId ? JadwalPengantin::find($selectedPengantinId) : null;
        return view('admin.jadwaldekor.create', compact('pakets', 'jadwals', 'selectedPengantin'));
    }

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
        return redirect()->route('admin.jadwaldekor.index', ['bulan' => $syncData['bulan'], 'tahun' => $syncData['tahun']])->with('swal_success', 'Jadwal berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pengantin = JadwalPengantin::with(['paket', 'jadwalDekor'])->findOrFail($id);
        $jadwal = $pengantin->jadwalDekor ?? new JadwalDekor();
        $jadwals = JadwalPengantin::with('paket')->orderBy('tanggal_awal', 'desc')->get();
        $pakets = Paket::all();
        return view('admin.jadwaldekor.edit', compact('jadwal', 'jadwals', 'pakets', 'pengantin'));
    }

    public function update(Request $request, $id)
    {
        $validated = $this->validateRequest($request);
        $jadwalPengantin = JadwalPengantin::findOrFail($request->jadwal_pengantin_id);
        $syncData = $this->syncWithJadwalPengantin($jadwalPengantin);
        $data = array_merge($validated, $syncData);

        $jadwal = JadwalDekor::where('jadwal_pengantin_id', $id)->first();
        if ($request->hasFile('foto')) {
            if ($jadwal && $jadwal->foto) Storage::disk('public')->delete($jadwal->foto);
            $data['foto'] = $request->file('foto')->store('jadwal_foto', 'public');
        }

        JadwalDekor::updateOrCreate(['jadwal_pengantin_id' => $id], $data);
        return redirect()->route('admin.jadwaldekor.index', ['bulan' => $request->last_bulan, 'tahun' => $request->last_tahun])->with('swal_success', 'Jadwal dekorasi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jadwal = JadwalDekor::findOrFail($id);
        if ($jadwal->foto) Storage::disk('public')->delete($jadwal->foto);
        $jadwal->delete();
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function print(Request $request)
    {
        $query = \App\Models\JadwalPengantin::with(['paket', 'jadwalDekor']);
        if ($request->filled('bulan')) {
            $bulanInput = $request->bulan;
            $query->where(function($q) use ($bulanInput) {
                $q->where('bulan', $bulanInput)->orWhere('bulan', $this->translateBulan($bulanInput));
            });
        }
        if ($request->filled('tahun')) $query->where('tahun', $request->tahun);

        $jadwal = $query->orderBy('tanggal_awal', 'asc')->get();
        return Pdf::loadView('admin.jadwaldekor.print', [
            'jadwal' => $jadwal,
            'bulan'  => $request->bulan ?? 'Semua',
            'tahun'  => $request->tahun ?? ''
        ])->setPaper('A4', 'portrait')->stream('jadwal-dekorasi.pdf');
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'jadwal_pengantin_id' => 'required|exists:jadwal_pengantins,id',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    }

    private function syncWithJadwalPengantin($jadwalPengantin)
    {
        $tanggalAwal = Carbon::parse($jadwalPengantin->tanggal_awal);
        return [
            'bulan' => $jadwalPengantin->bulan, // Gunakan bulan dari tabel utama
            'tahun' => $jadwalPengantin->tahun, // Gunakan tahun dari tabel utama
            'nama' => $jadwalPengantin->nama,
            'alamat' => $jadwalPengantin->alamat,
            'paket_id' => $jadwalPengantin->paket_id,
        ];
    }
}