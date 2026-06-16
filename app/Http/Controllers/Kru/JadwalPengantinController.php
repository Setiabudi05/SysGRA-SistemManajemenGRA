<?php

namespace App\Http\Controllers\Kru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPengantin;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class JadwalPengantinController extends Controller
{
    /**
     * Menampilkan Halaman Jadwal Utama (Jadwal Aktif)
     */
    public function index()
    {
        return view('kru.jadwal.index');
    }

    /**
     * Menandai notifikasi sebagai sudah dibaca
     */

    public function readNotification(Request $request, $id)
    {
    // KUNCI: Beri tahu VS Code kalau ini adalah Model User resmi
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Jalankan pencarian notifikasi dari variabel $user
        $notification = $user->notifications()->findOrFail($id);

        // Tandai sudah dibaca
        $notification->markAsRead();

        // 3. Cari tahu acara ini diplot untuk bulan dan tahun apa
        // Kita ambil jadwal_id yang dikirim dari request URL atau properti database
        $jadwalId = $request->get('jadwal_id');
        $jadwal = JadwalPengantin::find($jadwalId);

        if ($jadwal) {
            // Alihkan ke halaman JADWAL KRU dengan membawa filter bulan & tahun penugasan tersebut
            return redirect()->route('kru.jadwal.index', [
                'bulan' => $jadwal->bulan,
                'tahun' => $jadwal->tahun
            ])->with('swal_success', 'Menampilkan penugasan baru untuk bulan ' . $jadwal->bulan);
        }

        // Jika data jadwal tidak ditemukan, default balik ke dashboard saja
        return redirect()->route('kru.jadwal.index');
    }

    /**
     * Helper untuk memformat tanggal agar konsisten
     */
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

    /**
     * Mengambil Data via AJAX untuk DataTables (Logika Fleksibel Nama Depan)
     */
    public function data(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $namaKru = $user->name;
        $namaDepan = explode(' ', $namaKru)[0]; // Ambil nama depan (ex: Norma)

        $query = JadwalPengantin::with('paket')
            ->where(function ($q) use ($namaKru, $namaDepan) {
                $q->where('fg', 'LIKE', '%' . $namaDepan . '%')
                    ->orWhere('asisten', 'LIKE', '%' . $namaDepan . '%')
                    ->orWhere('layos', 'LIKE', '%' . $namaDepan . '%')
                    ->orWhere('fg', 'LIKE', '%' . $namaKru . '%')
                    ->orWhere('asisten', 'LIKE', '%' . $namaKru . '%')
                    ->orWhere('layos', 'LIKE', '%' . $namaKru . '%');
            });

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_custom', function ($row) {
                $bulanIndo = [
                    'January' => 'Januari',
                    'February' => 'Februari',
                    'March' => 'Maret',
                    'April' => 'April',
                    'May' => 'Mei',
                    'June' => 'Juni',
                    'July' => 'Juli',
                    'August' => 'Agustus',
                    'September' => 'September',
                    'October' => 'Oktober',
                    'November' => 'November',
                    'December' => 'Desember'
                ];
                $bulan = $bulanIndo[$row->bulan] ?? $row->bulan;
                $tglText = $this->formatTanggal($row);
                return "{$tglText} {$bulan} {$row->tahun}";
            })
            ->addColumn('nama_paket', function ($row) {
                return $row->paket?->nama_paket ?? '-';
            })
            ->addColumn('aksi', function ($row) {
                $encodedAlamat = urlencode($row->alamat);
                $mapsUrl = "https://www.google.com/maps/search/?api=1&query=" . $encodedAlamat;

                return '<a href="' . $mapsUrl . '" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm fw-bold">
                    <i class="bi bi-geo-alt-fill"></i> Maps
                </a>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Cetak PDF Jadwal Aktif (Logika Fleksibel Nama Depan)
     */
    public function print(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $namaKru = $user->name;
        $namaDepan = explode(' ', $namaKru)[0];

        $query = JadwalPengantin::with('paket')
            ->where(function ($q) use ($namaKru, $namaDepan) {
                $q->where('fg', 'LIKE', '%' . $namaDepan . '%')
                    ->orWhere('asisten', 'LIKE', '%' . $namaDepan . '%')
                    ->orWhere('layos', 'LIKE', '%' . $namaDepan . '%')
                    ->orWhere('fg', 'LIKE', '%' . $namaKru . '%')
                    ->orWhere('asisten', 'LIKE', '%' . $namaKru . '%')
                    ->orWhere('layos', 'LIKE', '%' . $namaKru . '%');
            });

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $jadwal = $query->orderBy('tanggal_awal', 'asc')->get()->map(function ($item) {
            $item->tanggal_display = $this->formatTanggal($item);
            return $item;
        });

        $pdf = Pdf::loadView('kru.jadwal.print', [
            'jadwal' => $jadwal,
            'bulan'  => $request->bulan ?? 'Semua',
            'tahun'  => $request->tahun ?? date('Y'),
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('jadwal_kru_' . strtolower(str_replace(' ', '_', $namaKru)) . '.pdf');
    }
}
