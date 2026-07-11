<?php

namespace App\Http\Controllers\Kru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalPengantin;
use App\Models\User;
use App\Notifications\SistemNotifikasi;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Helper terpusat untuk menyaring data berdasarkan nama kru
     * Menggunakan TRIM untuk menghindari masalah spasi di database
     */
    private function applyKruFilter($query, $namaKru)
    {
        $namaKru = trim($namaKru);
        return $query->where(function ($q) use ($namaKru) {
            $q->whereRaw("TRIM(fg) = ?", [$namaKru])
                ->orWhereRaw("TRIM(asisten) LIKE ?", ["%$namaKru%"])
                ->orWhereRaw("TRIM(layos) = ?", [$namaKru]);
        });
    }

    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $bulanSekarangIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][(int)date('m') - 1];
        $tahunSekarang = date('Y');

        // Menggunakan filter terpusat
        $baseQuery = $this->applyKruFilter(JadwalPengantin::query(), $user->name)
            ->where('bulan', $bulanSekarangIndo)
            ->where('tahun', $tahunSekarang);

        // Menghitung statistik menggunakan clone agar baseQuery tidak rusak
        $tugasBulanIni = (clone $baseQuery)->count();
        $tugasSelesai  = (clone $baseQuery)->whereDate('tanggal_awal', '<', $today)->count();
        $sisaTugas     = (clone $baseQuery)->whereDate('tanggal_awal', '>=', $today)->count();
        $nextJob       = (clone $baseQuery)->whereDate('tanggal_awal', '>=', $today)->orderBy('tanggal_awal', 'asc')->first();

        return view('kru.dashboard', compact('tugasBulanIni', 'tugasSelesai', 'sisaTugas', 'nextJob'));
    }

    public function data(Request $request)
    {
        $bulanSekarangIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][(int)date('m') - 1];

        $query = $this->applyKruFilter(JadwalPengantin::with('paket'), Auth::user()->name)
            ->where('bulan', $bulanSekarangIndo)
            ->where('tahun', date('Y'));

        return DataTables::of($query)
            ->addIndexColumn() // <-- Wajib ada agar error DT_RowIndex hilang
            ->addColumn('tanggal_custom', function ($row) {
                $getDay = function ($value) {
                    if (!$value) return null;
                    if (strpos($value, '-') !== false) {
                        $parts = explode('-', $value);
                        return end($parts);
                    }
                    return str_pad($value, 2, '0', STR_PAD_LEFT);
                };

                $tglAwal  = $getDay($row->getRawOriginal('tanggal_awal') ?? $row->tanggal_awal);
                $tglAkhir = $getDay($row->getRawOriginal('tanggal_akhir') ?? $row->tanggal_akhir);

                if ($tglAkhir && $tglAkhir != $tglAwal && $tglAkhir != "00") {
                    return "{$tglAwal}-{$tglAkhir} {$row->bulan} {$row->tahun}";
                }
                return "{$tglAwal} {$row->bulan} {$row->tahun}";
            })
            ->addColumn('nama_paket', function ($row) {
                return $row->paket?->nama_paket ?? '-';
            })
            ->make(true);
    }

    public function allNotifications()
    {
        $allNotif = auth()->user()->notifications()->orderBy('created_at', 'desc')->paginate(10);
        return view('kru.notification.all', compact('allNotif'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect()->route('kru.jadwal.index'); // Sesuaikan rute redirect
    }



    public function respondNotification(Request $request, $id)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();

        // 1. Update notifikasi milik Kru
        $notification = $currentUser->notifications()->findOrFail($id);
        $data = $notification->data;
        $data['status_konfirmasi'] = $request->jawaban;
        $data['alasan'] = $request->alasan ?? '';

        $notification->update(['data' => $data]);
        $notification->markAsRead();

        // 2. Persiapkan data untuk Owner & Admin
        $statusTeks = $request->jawaban == 'bisa' ? 'BISA HADIR ✅' : 'BERHALANGAN HADIR ❌';
        $alasanTeks = $request->alasan ? " (Alasan: {$request->alasan})" : "";

        $payload = [
            'judul' => '📢 Konfirmasi Tugas Kru!',
            'pesan' => "Kru {$currentUser->name} menyatakan {$statusTeks} untuk tugasnya{$alasanTeks}.",
            'icon'  => $request->jawaban == 'bisa' ? 'bi-check-circle-fill' : 'bi-x-circle-fill',
            'link'  => route('owner.jadwalpengantin.index')
        ];

        // 3. Kirim ke Owner dan Admin
        $penerima = User::whereIn('role', ['owner', 'admin'])->get();

        foreach ($penerima as $user) {
            $user->notify(new SistemNotifikasi($payload));
        }

        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi berhasil dikirim!'
        ]);
    }
}
