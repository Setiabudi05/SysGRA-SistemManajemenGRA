<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembukuan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;

class PembukuanController extends Controller
{
    // Halaman utama Pembukuan
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        $totalPemasukan = Pembukuan::whereDate('tanggal', $tanggal)->where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = Pembukuan::whereDate('tanggal', $tanggal)->where('tipe', 'pengeluaran')->sum('nominal');

        $saldo = $totalPemasukan - $totalPengeluaran;

        return view('admin.pembukuan.index', compact('tanggal', 'totalPemasukan', 'totalPengeluaran', 'saldo'));
    }

    // Logic umum untuk DataTables
    private function getDataTable($tipe, $tanggal)
    {
        $query = Pembukuan::whereDate('tanggal', $tanggal)
            ->where('tipe', $tipe)
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->addColumn('action', function ($row) use ($tanggal) {
                $editUrl = route('admin.pembukuan.edit', ['id' => $row->id, 'f_tanggal' => $tanggal]);
                return '
                <div class="d-flex justify-content-center gap-2">
                    <a href="'.$editUrl.'" class="btn btn-warning btn-sm fw-bold"><i class="bi bi-pencil-square"></i></a>
                    <button onclick="hapusPembukuan('.$row->id.')" class="btn btn-danger btn-sm fw-bold"><i class="bi bi-trash"></i></button>
                </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function pemasukanData(Request $request) { return $this->getDataTable('pemasukan', $request->input('tanggal', now()->toDateString())); }
    public function pengeluaranData(Request $request) { return $this->getDataTable('pengeluaran', $request->input('tanggal', now()->toDateString())); }

    // Form Tambah
    public function createPemasukan() { return view('admin.pembukuan.create', ['tipe' => 'pemasukan']); }
    public function createPengeluaran() { return view('admin.pembukuan.create', ['tipe' => 'pengeluaran']); }

    // Simpan data
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'customer' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'nominal' => 'required|numeric|min:0',
        ]);

        Pembukuan::create($validated);

        return redirect()->route('admin.pembukuan.index', ['tanggal' => $request->tanggal])
            ->with('success', ucfirst($request->tipe) . ' berhasil ditambahkan!');
    }

    // Edit
    public function edit($id, Request $request)
    {
        $pembukuan = Pembukuan::findOrFail($id);
        $tanggalAsal = $request->input('f_tanggal', $pembukuan->tanggal);
        return view('admin.pembukuan.edit', compact('pembukuan', 'tanggalAsal'));
    }

    // Update
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'customer' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'nominal' => 'required|numeric|min:0',
        ]);

        $data = Pembukuan::findOrFail($id);
        $data->update($validated);

        return redirect()->route('admin.pembukuan.index', ['tanggal' => $request->input('last_tanggal', $request->tanggal)])
            ->with('success', 'Data berhasil diperbarui');
    }

    // Hapus
    public function destroy($id)
    {
        try {
            Pembukuan::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Print PDF
    public function print(Request $request)
    {
        $tanggal = Carbon::parse($request->input('tanggal', now()->toDateString()))->toDateString();
        $pemasukan = Pembukuan::whereDate('tanggal', $tanggal)->where('tipe', 'pemasukan')->get();
        $pengeluaran = Pembukuan::whereDate('tanggal', $tanggal)->where('tipe', 'pengeluaran')->get();

        $data = [
            'tanggal' => $tanggal,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'totalPemasukan' => $pemasukan->sum('nominal'),
            'totalPengeluaran' => $pengeluaran->sum('nominal'),
            'saldo' => $pemasukan->sum('nominal') - $pengeluaran->sum('nominal')
        ];

        return PDF::loadView('admin.pembukuan.print', $data)->setPaper('A4', 'portrait')->stream();
    }
}