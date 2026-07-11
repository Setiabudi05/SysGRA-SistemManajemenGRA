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
        Schema::create('jadwal_pengantins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('bookings')->onDelete('cascade');
            $table->date('tanggal_awal');
            $table->date('tanggal_akhir')->nullable();
            $table->string('bulan');
            $table->year('tahun');
            $table->string('nama');
            $table->string('alamat');
            $table->foreignId('paket_id')->constrained('pakets');
            $table->string('asisten')->nullable();
            $table->string('fg')->nullable();
            $table->string('layos')->nullable();
            $table->tinyInteger('is_manual')->default(0); // Penanda entri manual atau otomatis
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_dekors');
    }
};
