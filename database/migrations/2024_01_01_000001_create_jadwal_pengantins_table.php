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
            $table->date('tanggal_awal');    // tanggal mulai
            $table->date('tanggal_akhir');   // tanggal selesai
            $table->string('bulan');         // bulan acara (ambil dari tanggal_awal)
            $table->string('nama');
            $table->string('alamat');
            $table->string('paket');
            $table->string('asisten')->nullable();
            $table->string('fg')->nullable();
            $table->string('layos')->nullable();
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