<?php

namespace App\Http\Controllers\Kru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalPengantin;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan Halaman Dashboard Utama Kru
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $namaKru = $user->name;
        // Ambil nama depan (misal: "Norma Ynk" jadi "Norma")
        $namaDepan = explode(' ', $namaKru)[0];

        // 1. Info waktu sekarang
        $today = Carbon::today()->toDateString();
        $tahunSekarang = date('Y');
        $listBulanIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $bulanSekarangIndo = $listBulanIndo[(int)date('m')];

        // 2. Query Dasar yang Fleksibel (Berlaku untuk 3 CARD & WIDGET)
        $baseQueryBulanIni = JadwalPengantin::where('bulan', $bulanSekarangIndo)
            ->where('tahun', $tahunSekarang)
            ->where(function ($q) use ($namaKru, $namaDepan) {
                $q->where('fg', 'LIKE', '%' . $namaDepan . '%')
                    ->orWhere('asisten', 'LIKE', '%' . $namaDepan . '%')
                    ->orWhere('layos', 'LIKE', '%' . $namaDepan . '%')
                    ->orWhere('fg', 'LIKE', '%' . $namaKru . '%')
                    ->orWhere('asisten', 'LIKE', '%' . $namaKru . '%')
                    ->orWhere('layos', 'LIKE', '%' . $namaKru . '%');
            });

        // 3. HITUNG STATISTIK (CARD)
        $tugasBulanIni = (clone $baseQueryBulanIni)->count();

        // Tugas Selesai (Tanggal Awal < Hari Ini)
        $tugasSelesai = (clone $baseQueryBulanIni)
            ->whereDate('tanggal_awal', '<', $today)
            ->count();

        // Sisa Tugas (Tanggal Awal >= Hari Ini)
        $sisaTugas = (clone $baseQueryBulanIni)
            ->whereDate('tanggal_awal', '>=', $today)
            ->count();

        // Widget Tugas Terdekat (Widget Kanan)
        $nextJob = (clone $baseQueryBulanIni)
            ->whereDate('tanggal_awal', '>=', $today)
            ->orderBy('tanggal_awal', 'asc')
            ->first();

        return view('kru.dashboard', compact('tugasBulanIni', 'tugasSelesai', 'sisaTugas', 'nextJob'));
    }

    /**
     * Mengambil Data untuk DataTables (AJAX)
     */
    public function data(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $namaKru = $user->name;
        $namaDepan = explode(' ', $namaKru)[0];

        $bulanSekarang = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ][(int)date('m')];

        $tahunSekarang = date('Y');

        // Query Tabel harus sama persis logikanya dengan Card
        $query = JadwalPengantin::with('paket')
            ->where('bulan', $bulanSekarang)
            ->where('tahun', $tahunSekarang)
            ->where(function ($q) use ($namaKru, $namaDepan) {
                $q->where('fg', 'LIKE', '%' . $namaDepan . '%')
                    ->orWhere('asisten', 'LIKE', '%' . $namaDepan . '%')
                    ->orWhere('layos', 'LIKE', '%' . $namaDepan . '%')
                    ->orWhere('fg', 'LIKE', '%' . $namaKru . '%')
                    ->orWhere('asisten', 'LIKE', '%' . $namaKru . '%')
                    ->orWhere('layos', 'LIKE', '%' . $namaKru . '%');
            });

        return DataTables::of($query)
            ->addIndexColumn()
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
    // KUNCI: Ambil user lalu beri tahu VS Code kalau ini adalah Model User resmi
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Sekarang panggil dari variabel $user, garis merah dijamin hilang!
        $allNotif = $user->notifications()->orderBy('created_at', 'desc')->paginate(10);

        return view('kru.notification.all', compact('allNotif'));
    }

    public function respondNotification(Request $request, $id)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();

        // 1. Cari notifikasi milik kru
        $notification = $currentUser->notifications()->findOrFail($id);
        $data = $notification->data;

        // 2. Ambil keputusan dari tombol (bisa / tidak_bisa)
        $jawaban = $request->input('jawaban');
        $alasan = $request->input('alasan', '');

        $data['status_konfirmasi'] = $jawaban;
        $data['alasan'] = $alasan;

        // Update data JSON notifikasi si kru di database
        $notification->data = $data;
        $notification->markAsRead(); // Otomatis tandai sudah dibaca
        $notification->save();

        // 3. KIRIM NOTIFIKASI BALIK KE OWNER & ADMIN
        $statusTeks = $jawaban == 'bisa' ? 'BISA HADIR ✅' : 'BERHALANGAN HADIR ❌';
        $alasanTeks = $alasan ? " (Alasan: $alasan)" : "";

        $payloadOwner = [
            'judul' => '📢 Konfirmasi Tugas Kru!',
            'pesan' => "Kru {$currentUser->name} menyatakan {$statusTeks} untuk tugasnya{$alasanTeks}.",
            'icon'  => $jawaban == 'bisa' ? 'bi-check-circle-fill' : 'bi-x-circle-fill',
            'link'  => route('owner.jadwalpengantin.index') // Jika diklik, owner masuk ke jadwal
        ];

        // Cari user yang rolenya owner dan admin
        $penerimaNotif = User::whereIn('role', ['owner', 'admin'])->get();
        foreach ($penerimaNotif as $penerima) {
            $penerima->notify(new \App\Notifications\SistemNotifikasi($payloadOwner));
        }

        return response()->json(['success' => true, 'message' => 'Konfirmasi berhasil dikirim ke Owner!']);
    }
}
