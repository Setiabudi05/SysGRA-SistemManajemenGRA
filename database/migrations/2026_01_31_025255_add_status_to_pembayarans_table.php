<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambah kolom status.
     */
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Menambahkan kolom status setelah kolom keterangan
            $table->string('status')->default('valid')->after('keterangan');
        });
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Menghapus kolom status jika migrasi di-rollback
            $table->dropColumn('status');
        });
    }
};