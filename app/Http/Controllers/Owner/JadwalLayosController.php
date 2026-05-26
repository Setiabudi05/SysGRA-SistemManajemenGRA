<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\JadwalLayos;
use App\Models\JadwalPengantin;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalLayosController extends Controller
{
    public function index()
    {
        return view('owner.jadwallayos.index');
    }

    public function data(Request $request)
    {
        $query = JadwalLayos::with('pengantin.paket');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($row) {
                return $row->pengantin
                    ? Carbon::parse($row->pengantin->tanggal_awal)->translatedFormat('d')
                    : '-';
            })
            ->addColumn('nama', fn($row) => $row->pengantin->nama ?? '-')
            ->addColumn('paket', fn($row) => $row->pengantin->paket->nama_paket ?? '-')
            ->addColumn('action', function ($row) use ($request) {
                $params = [
                    'id' => $row->id,
                    'f_bulan' => $request->bulan,
                    'f_tahun' => $request->tahun
                ];

                return '
                <div class="d-flex justify-content-center gap-2">
                    <a href="' . route('owner.jadwallayos.edit', $params) . '" 
                       class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                       <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <button onclick="hapusJadwal(' . $row->id . ')" 
                            class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $jadwals = JadwalPengantin::with('paket')->orderBy('tanggal_awal')->get();
        return view('owner.jadwallayos.create', compact('jadwals'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $pengantin = JadwalPengantin::findOrFail($request->jadwal_pengantin_id);

        $sync = $this->syncWithPengantin($pengantin);
        $data = array_merge($validated, $sync);
        JadwalLayos::create($data);

        return redirect()->route('owner.jadwallayos.index', [
            'bulan' => $sync['bulan'],
            'tahun' => $sync['tahun']
        ])->with('swal_success', 'Jadwal Layos berhasil ditambahkan!');
    }

    // PERBAIKAN: Menambahkan parameter $id
    public function edit($id)
    {
        $jadwal = JadwalLayos::findOrFail($id);
        $jadwals = JadwalPengantin::with('paket')->orderBy('tanggal_awal')->get();
        return view('owner.jadwallayos.edit', compact('jadwal', 'jadwals'));
    }

    // PERBAIKAN: Menambahkan parameter $request dan $id
    public function update(Request $request, $id)
    {
        $validated = $this->validateRequest($request);
        $jadwal = JadwalLayos::findOrFail($id);
        $pengantin = JadwalPengantin::findOrFail($request->jadwal_pengantin_id);

        $sync = $this->syncWithPengantin($pengantin);
        $data = array_merge($validated, $sync);
        $jadwal->update($data);

        return redirect()->route('owner.jadwallayos.index', [
            'bulan' => $request->last_bulan,
            'tahun' => $request->last_tahun
        ])->with('swal_success', 'Jadwal Layos berhasil diperbarui!');
    }

    public function destroy($id)
    {
        try {
            $jadwal = JadwalLayos::findOrFail($id);
            $jadwal->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function print(Request $request)
    {
        $query = JadwalLayos::with(['pengantin.paket']);
        if ($request->filled('bulan')) $query->where('bulan', $request->bulan);
        if ($request->filled('tahun')) $query->where('tahun', $request->tahun);

        $jadwal = $query->get();
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        return Pdf::loadView('owner.jadwallayos.print', compact('jadwal', 'bulan', 'tahun'))
            ->setPaper('A4', 'portrait')
            ->stream('jadwal-layos.pdf');
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'jadwal_pengantin_id' => 'required|exists:jadwal_pengantins,id',
            'layos' => 'required|string',
        ]);
    }

    private function syncWithPengantin($pengantin)
    {
        $date = Carbon::parse($pengantin->tanggal_awal)->locale('id');
        return [
            'jadwal_pengantin_id' => $pengantin->id,
            'bulan' => $date->translatedFormat('F'),
            'tahun' => $date->format('Y'),
        ];
    }
}