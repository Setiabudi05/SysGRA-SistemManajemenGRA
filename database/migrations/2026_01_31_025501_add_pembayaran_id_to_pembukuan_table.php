<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembukuan', function (Blueprint $table) {
            // Menambahkan kolom foreign key untuk relasi ke tabel pembayarans
            $table->unsignedBigInteger('pembayaran_id')->nullable()->after('nominal');
            
            // Opsional: Tambahkan foreign key constraint agar data konsisten
            $table->foreign('pembayaran_id')->references('id')->on('pembayarans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('pembukuan', function (Blueprint $table) {
            $table->dropForeign(['pembayaran_id']);
            $table->dropColumn('pembayaran_id');
        });
    }
};