<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPengantin;
use App\Models\Jadwal;
use App\Models\User;
use App\Models\Paket;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class JadwalPengantinController extends Controller
{
    public function index()
    {
        return view('owner.jadwalpengantin.index');
    }

    public function data(Request $request)
    {
        $query = JadwalPengantin::with('paket')->orderBy('tanggal_awal', 'asc');
        if ($request->filled('bulan')) $query->where('bulan', $request->bulan);
        if ($request->filled('tahun')) $query->where('tahun', $request->tahun);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_full', fn($row) => Carbon::parse($row->tanggal_awal)->format('d') . ' ' . $row->bulan . ' ' . $row->tahun)
            ->addColumn('nama_paket', fn($row) => $row->paket ? $row->paket->nama_paket : '-')
            ->addColumn('keterangan_text', fn($row) => $row->keterangan ?? '-')
            ->addColumn('action', fn($row) => '<a href="' . route('owner.jadwalpengantin.edit', $row->id) . '" class="btn btn-warning btn-sm shadow-sm"><i class="bi bi-pencil-square"></i> Edit</a>')
            ->rawColumns(['action'])
            ->make(true);
    }

    // Method untuk pengecekan per-kru (saat submit form)
    public function checkKruAvailability(Request $request)
    {
        $namaKru = $request->nama_kru;
        $tanggal = $request->tanggal;
        $id = $request->jadwal_id;

        $isBusyGRA = \App\Models\JadwalPengantin::where('tanggal_awal', $tanggal)
            ->where('id', '!=', $id)
            ->where(fn($q) => $q->where('asisten', 'like', "%$namaKru%")
                ->orWhere('fg', $namaKru)
                ->orWhere('layos', $namaKru))->exists();

        $isBusyPribadi = \App\Models\Jadwal::where('event_date', $tanggal)
            ->whereHas('user', fn($q) => $q->where('name', $namaKru))->exists();

        return response()->json(['is_busy' => ($isBusyGRA || $isBusyPribadi)]);
    }

    // Method untuk status massal di dalam dropdown
    public function checkKruStatus(Request $request)
    {
        $tanggal = $request->tanggal;
        $kru = \App\Models\User::where('role', 'kru')->get();
        $statusMap = [];

        foreach ($kru as $u) {
            $busy = \App\Models\JadwalPengantin::where('tanggal_awal', $tanggal)
                ->where(fn($q) => $q->where('asisten', 'like', "%$u->name%")
                    ->orWhere('fg', $u->name)
                    ->orWhere('layos', $u->name))->exists();

            $busyPribadi = \App\Models\Jadwal::where('event_date', $tanggal)
                ->whereHas('user', fn($q) => $q->where('name', $u->name))->exists();

            $statusMap[$u->name] = ($busy || $busyPribadi) ? 'Busy' : 'Available';
        }
        return response()->json($statusMap);
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
        // Validasi Hard Constraint (Blokir Simpan jika bentrok)
        $namaKru = array_filter([$request->fg, $request->layos]);
        if ($request->has('asisten')) $namaKru = array_merge($namaKru, $request->asisten);

        foreach ($namaKru as $nama) {
            $isBusy = JadwalPengantin::where('tanggal_awal', $request->tanggal_awal)->where('id', '!=', $id)
                ->where(fn($q) => $q->where('asisten', 'like', "%$nama%")->orWhere('fg', $nama)->orWhere('layos', $nama))->exists();

            if ($isBusy) return back()->with('swal_error', "Gagal! $nama sudah memiliki jadwal.");
        }

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

        $validated['asisten'] = $request->has('asisten') ? implode(',', $request->asisten) : null;
        $jadwal->update($validated);

        return redirect()->route('owner.jadwalpengantin.index')->with('swal_success', 'Jadwal diperbarui!');
    }
}
