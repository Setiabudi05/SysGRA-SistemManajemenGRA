<?php

namespace App\Http\Controllers\Kru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPengantin;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class JadwalPengantinController extends Controller
{
    public function index()
    {
        return view('kru.jadwal.index');
    }

    public function readNotification(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        $jadwal = JadwalPengantin::find($request->get('jadwal_id'));

        if ($jadwal) {
            return redirect()->route('kru.jadwal.index', [
                'bulan' => $jadwal->bulan,
                'tahun' => $jadwal->tahun
            ])->with('swal_success', 'Menampilkan penugasan bulan ' . $jadwal->bulan);
        }

        return redirect()->route('kru.jadwal.index');
    }

    /**
     * Helper untuk membuat kueri pencarian kru yang fleksibel
     */
    private function applyKruFilter($query, $namaKru)
    {
        $namaKru = trim($namaKru);
        return $query->where(function ($q) use ($namaKru) {
            // Menggunakan TRIM untuk memastikan spasi tidak menggagalkan pencarian
            $q->whereRaw("TRIM(fg) = ?", [$namaKru])
              ->orWhereRaw("TRIM(asisten) LIKE ?", ["%$namaKru%"])
              ->orWhereRaw("TRIM(layos) = ?", [$namaKru]);
        });
    }

    public function data(Request $request)
    {
        $query = $this->applyKruFilter(JadwalPengantin::with('paket'), Auth::user()->name);

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_awal', $request->tanggal);
        } else {
            if ($request->filled('bulan')) $query->where('bulan', $request->bulan);
            if ($request->filled('tahun')) $query->where('tahun', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_custom', function ($row) {
                $bulanIndo = [
                    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                ];
                return "{$this->formatTanggal($row)} " . ($bulanIndo[$row->bulan] ?? $row->bulan) . " {$row->tahun}";
            })
            ->addColumn('nama_paket', fn($row) => $row->paket?->nama_paket ?? '-')
            ->addColumn('aksi', function ($row) {
                $mapsUrl = "https://www.google.com/maps/search/?api=1&query=" . urlencode($row->alamat);
                return '<a href="' . $mapsUrl . '" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm fw-bold">
                            <i class="bi bi-geo-alt-fill"></i> Maps
                        </a>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    private function formatTanggal($item)
    {
        $getDay = function ($value) {
            if (!$value) return null;
            if (strpos($value, '-') !== false) {
                $parts = explode('-', $value);
                return end($parts);
            }
            return str_pad($value, 2, '0', STR_PAD_LEFT);
        };

        $tglAwal  = $getDay($item->getRawOriginal('tanggal_awal') ?? $item->tanggal_awal);
        $tglAkhir = $getDay($item->getRawOriginal('tanggal_akhir') ?? $item->tanggal_akhir);

        if ($tglAkhir && $tglAkhir != $tglAwal && $tglAkhir != "00") {
            return "{$tglAwal}-{$tglAkhir}";
        }
        return $tglAwal;
    }
    }