<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('baju_pengantins', function (Blueprint $table) {
            // Kita gunakan logic 'after' agar urutan kolom di database rapi
            // Jika kolom sudah ada, Laravel akan mengabaikan atau Anda bisa menyesuaikan tipenya
            if (!Schema::hasColumn('baju_pengantins', 'kategori')) {
                $table->string('kategori')->after('id')->nullable();
            }
            if (!Schema::hasColumn('baju_pengantins', 'warna')) {
                $table->string('warna')->after('kategori')->nullable();
            }
            if (!Schema::hasColumn('baju_pengantins', 'ukuran')) {
                $table->string('ukuran')->after('warna')->nullable();
            }
            if (!Schema::hasColumn('baju_pengantins', 'stok')) {
                $table->integer('stok')->default(0)->after('ukuran');
            }
            if (!Schema::hasColumn('baju_pengantins', 'foto')) {
                $table->string('foto')->nullable()->after('stok');
            }
        });
    }

    public function down(): void
    {
        Schema::table('baju_pengantins', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'warna', 'ukuran', 'stok', 'foto']);
        });
    }
};