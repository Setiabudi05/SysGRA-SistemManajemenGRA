<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paket;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class PaketController extends Controller
{
    public function index()
    {
        return view('admin.paket.index');
    }

    public function data(Request $request)
    {
        // Ambil kolom tahun juga
        $pakets = Paket::select(['id', 'nama_paket', 'tahun', 'makeup', 'dekorasi', 'dokumentasi', 'harga']);

        // Filter berdasarkan kolom 'tahun', bukan 'created_at'
        if ($request->has('tahun') && !empty($request->tahun)) {
            $pakets->where('tahun', $request->tahun);
        }

        return DataTables::of($pakets)
            ->addIndexColumn()
            ->addColumn('action', function ($paket) {
                return '
                <div class="d-flex justify-content-center gap-2">
                    <a href="' . route('admin.paket.edit', $paket->id) . '" class="btn btn-warning btn-sm px-2 py-1">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <button onclick="hapus(' . $paket->id . ')" class="btn btn-danger btn-sm px-2 py-1">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.paket.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'tahun'      => 'required|numeric', // Validasi tahun wajib diisi
            'makeup'     => 'nullable|string',
            'dekorasi'   => 'nullable|string',
            'dokumentasi'=> 'nullable|string',
            'harga'      => 'required|numeric|min:0',
        ]);

        Paket::create($validated);

        return redirect()->route('admin.paket.index')->with('swal_success', 'Paket berhasil ditambah!');
    }

    public function edit($id)
    {
        $paket = Paket::findOrFail($id);
        return view('admin.paket.edit', compact('paket'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'tahun'      => 'required|numeric',
            'makeup'     => 'nullable|string',
            'dekorasi'   => 'nullable|string',
            'dokumentasi'=> 'nullable|string',
            'harga'      => 'required|numeric|min:0',
        ]);

        $paket = Paket::findOrFail($id);
        $paket->update($validated);

        return redirect()->route('admin.paket.index')->with('swal_success', 'Paket berhasil diubah!');
    }

    public function destroy($id)
    {
        try {
            $paket = Paket::findOrFail($id);
            $paket->delete();

            // Mengembalikan response sukses dalam format JSON
            return response()->json([
                'success' => true,
                'message' => 'Data paket pernikahan berhasil dihapus dari sistem.'
            ]);
        } catch (Exception $e) {
            // Mengembalikan response gagal dalam format JSON
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
