<?php

namespace App\Http\Controllers\Kru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class JadwalPribadiController extends Controller
{
    public function index()
    {
        return view('kru.jadwal.pribadi.index');
    }

    public function getData(Request $request)
    {
        $query = Jadwal::where('user_id', Auth::id());

        // Filter
        if ($request->filled('tanggal')) {
            $query->whereDate('event_date', $request->tanggal);
        } else {
            if ($request->filled('bulan')) {
                $bulanMap = ['Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4, 'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8, 'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12];
                $query->whereMonth('event_date', $bulanMap[$request->bulan]);
            }
            if ($request->filled('tahun')) {
                $query->whereYear('event_date', $request->tahun);
            }
        }

        return DataTables::of($query->orderBy('event_date', 'asc'))
            ->addIndexColumn() // Wajib untuk 'DT_RowIndex'
            ->editColumn('event_date', fn($row) => Carbon::parse($row->event_date)->translatedFormat('d F Y'))
            ->addColumn('aksi', function ($row) {
                return '<a href="' . route('kru.jadwal.pribadi.edit', $row->id) . '" class="btn btn-sm btn-warning">Edit</a>
                    <form action="' . route('kru.jadwal.pribadi.destroy', $row->id) . '" method="POST" class="d-inline">
                        ' . csrf_field() . method_field("DELETE") . '
                        <button class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin hapus?\')">Hapus</button>
                    </form>';
            })
            ->rawColumns(['aksi', 'tipe']) // Pastikan 'tipe' ada di sini jika pakai badge
            ->make(true);
    }


    public function create()
    {
        return view('kru.jadwal.pribadi.create');
    }

    public function store(Request $request)
    {
        $request->validate(['event_date' => 'required', 'nama_event' => 'required', 'tipe_tugas' => 'required']);
        Jadwal::create([
            'user_id' => Auth::id(),
            'event_date' => $request->event_date,
            'nama_event' => $request->nama_event,
            'tipe' => $request->tipe_tugas,
            'nama_vendor' => $request->nama_vendor,
            'keterangan' => $request->keterangan
        ]);
        return redirect()->route('kru.jadwal.pribadi.index')->with('success', 'Agenda berhasil ditambah!');
    }

    public function edit($id)
    {
        $jadwal = Jadwal::where('user_id', Auth::id())->findOrFail($id);
        return view('kru.jadwal.pribadi.edit', compact('jadwal'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::where('user_id', Auth::id())->findOrFail($id);
        $jadwal->update([
            'event_date' => $request->event_date,
            'nama_event' => $request->nama_event,
            'tipe' => $request->tipe_tugas,
            'nama_vendor' => $request->tipe_tugas == 'EKSTERNAL' ? $request->nama_vendor : null,
            'keterangan' => $request->keterangan
        ]);
        return redirect()->route('kru.jadwal.pribadi.index')->with('success', 'Agenda diperbarui!');
    }

    public function destroy($id)
    {
        Jadwal::where('user_id', Auth::id())->findOrFail($id)->delete();
        return redirect()->route('kru.jadwal.pribadi.index')->with('success', 'Agenda dihapus!');
    }
}
