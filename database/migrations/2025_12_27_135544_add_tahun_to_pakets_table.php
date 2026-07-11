<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            // Method integer() harus dipanggil dari variabel $table
            $table->integer('tahun')->after('nama_paket')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn('tahun');
        });
    }
};
