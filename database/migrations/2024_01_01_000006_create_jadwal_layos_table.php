<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal_layos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_pengantin_id')
                  ->constrained('jadwal_pengantins') // pastikan sesuai nama tabel relasi
                  ->onDelete('cascade');
            $table->string('bulan');   // contoh: September
            $table->string('tahun');   // contoh: 2025
            $table->string('layos');   // nama/ukuran layos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_layos');
    }
};
