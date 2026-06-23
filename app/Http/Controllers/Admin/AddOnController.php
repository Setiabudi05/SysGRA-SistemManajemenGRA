<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AddOnController extends Controller
{
    public function index()
    {
        return view('admin.addons.index');
    }

    public function data()
    {
        $data = AddOn::query();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
            <div class="d-flex justify-content-center gap-2">
                <a href="' . route('admin.addons.edit', $row->id) . '" 
                   class="btn btn-warning btn-sm px-2 py-1 fw-bold shadow-sm">
                   <i class="bi bi-pencil-square"></i> Edit
                </a>
                <button onclick="hapusAddon(' . $row->id . ')" 
                        class="btn btn-danger btn-sm px-2 py-1 fw-bold shadow-sm">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.addons.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nama_item' => 'required', 'harga' => 'required|numeric']);
        AddOn::create($request->all());
        return redirect()->route('admin.addons.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit($id)
    {
        $addOn = AddOn::findOrFail($id);
        return view('admin.addons.edit', compact('addOn'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_item' => 'required', 'harga' => 'required|numeric']);
        AddOn::findOrFail($id)->update($request->all());
        return redirect()->route('admin.addons.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        AddOn::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }
}
