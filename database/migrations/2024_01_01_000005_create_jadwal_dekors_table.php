<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_dekors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_pengantin_id');
            $table->string('bulan', 20);
            $table->string('tahun', 4);
            $table->string('nama');
            $table->string('alamat');
            $table->unsignedBigInteger('paket_id');
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            // Relasi ke tabel jadwal_pengantins
            $table->foreign('jadwal_pengantin_id')->references('id')->on('jadwal_pengantins')->onDelete('cascade');

            // Relasi ke tabel pakets
            $table->foreign('paket_id')->references('id')->on('pakets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_dekors');
    }
};
