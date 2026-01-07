<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembukuan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf as PDF;



class PembukuanController extends Controller
{
    // Halaman utama Pembukuan
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        $totalPemasukan = Pembukuan::whereDate('tanggal', $tanggal)
            ->where('tipe', 'pemasukan')
            ->sum('nominal');

        $totalPengeluaran = Pembukuan::whereDate('tanggal', $tanggal)
            ->where('tipe', 'pengeluaran')
            ->sum('nominal');

        $saldo = $totalPemasukan - $totalPengeluaran;

        return view('admin.pembukuan.index', compact(
            'tanggal',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo'
        ));
    }

    // DataTables Pemasukan
    public function pemasukanData(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        $query = Pembukuan::whereDate('tanggal', $tanggal)
            ->where('tipe', 'pemasukan')
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('customer', fn($row) => $row->customer ?? '-')
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->addColumn('action', function ($row) use ($tanggal) {
                // Menambahkan parameter tanggal aktif ke URL Edit
                $editUrl = route('admin.pembukuan.edit', ['id' => $row->id, 'f_tanggal' => $tanggal]);

                return '
                <div class="d-flex justify-content-center gap-2">
                    <a href="' . $editUrl . '" class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                       <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <button onclick="hapusPembukuan(' . $row->id . ')" class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // DataTables Pengeluaran
    public function pengeluaranData(Request $request)
    {
        // Mengambil tanggal dari filter, jika tidak ada gunakan tanggal hari ini
        $tanggal = $request->input('tanggal', now()->toDateString());

        $query = Pembukuan::whereDate('tanggal', $tanggal)
            ->where('tipe', 'pengeluaran')
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->addColumn('action', function ($row) use ($tanggal) {
                // Menambahkan parameter 'f_tanggal' agar filter tetap terjaga setelah edit
                $editUrl = route('admin.pembukuan.edit', [
                    'id' => $row->id,
                    'f_tanggal' => $tanggal
                ]);

                return '
            <div class="d-flex justify-content-center gap-2">
                <a href="' . $editUrl . '" 
                   class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                   <i class="bi bi-pencil-square"></i> Edit
                </a>
                <button onclick="hapusPembukuan(' . $row->id . ')" 
                        class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Ambil saldo (Ajax)
    public function getSaldo(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        $totalPemasukan = Pembukuan::whereDate('tanggal', $tanggal)
            ->where('tipe', 'pemasukan')
            ->sum('nominal');

        $totalPengeluaran = Pembukuan::whereDate('tanggal', $tanggal)
            ->where('tipe', 'pengeluaran')
            ->sum('nominal');

        $saldo = $totalPemasukan - $totalPengeluaran;

        return response()->json([
            'pemasukan' => $totalPemasukan,
            'pengeluaran' => $totalPengeluaran,
            'saldo' => $saldo
        ]);
    }

    // Form Tambah
    public function createPemasukan()
    {
        $tipe = 'pemasukan';
        return view('admin.pembukuan.create', compact('tipe'));
    }

    public function createPengeluaran()
    {
        $tipe = 'pengeluaran';
        return view('admin.pembukuan.create', compact('tipe'));
    }

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

        // Redirect kembali ke tanggal yang baru diinput
        return redirect()->route('admin.pembukuan.index', ['tanggal' => $request->tanggal])
            ->with('success', ucfirst($request->tipe) . ' berhasil ditambahkan!');
    }

    // Form edit
    public function edit($id)
    {
        $pembukuan = Pembukuan::findOrFail($id);
        return view('admin.pembukuan.edit', compact('pembukuan'));
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

        // Ambil tanggal asal dari hidden input 'last_tanggal'
        $targetTanggal = $request->input('last_tanggal', $request->tanggal);

        return redirect()->route('admin.pembukuan.index', ['tanggal' => $targetTanggal])
            ->with('success', 'Data berhasil diperbarui');
    }

    // Hapus
    public function destroy($id)
    {
        try {
            $data = Pembukuan::findOrFail($id);
            $data->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data transaksi keuangan berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
    // ✅ Cetak Laporan PDF
    public function print(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        $pemasukan = Pembukuan::whereDate('tanggal', $tanggal)
            ->where('tipe', 'pemasukan')
            ->get();

        $pengeluaran = Pembukuan::whereDate('tanggal', $tanggal)
            ->where('tipe', 'pengeluaran')
            ->get();

        $totalPemasukan = $pemasukan->sum('nominal');
        $totalPengeluaran = $pengeluaran->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $pdf = PDF::loadView('admin.pembukuan.print', compact(

            'tanggal',
            'pemasukan',
            'pengeluaran',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo'
        ))->setPaper('A4', 'portrait');

        return $pdf->download("Pembukuan-{$tanggal}.pdf");
    }
}
