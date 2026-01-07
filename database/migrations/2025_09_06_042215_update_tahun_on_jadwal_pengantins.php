<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        $jadwals = DB::table('jadwal_pengantins')->get();

        foreach ($jadwals as $jadwal) {
            if ($jadwal->tanggal_awal) {
                $tanggalAwal = Carbon::parse($jadwal->tanggal_awal);

                DB::table('jadwal_pengantins')
                    ->where('id', $jadwal->id)
                    ->update([
                        'bulan' => $tanggalAwal->translatedFormat('F'),
                        'tahun' => $tanggalAwal->format('Y'),
                    ]);
            }
        }
    }

    public function down(): void
    {
        // optional: reset tahun & bulan jadi NULL kalau rollback
        DB::table('jadwal_pengantins')->update([
            'bulan' => null,
            'tahun' => null,
        ]);
    }
};

