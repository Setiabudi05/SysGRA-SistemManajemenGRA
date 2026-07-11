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
        Schema::create('jadwal_gowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_pengantin_id')->constrained('jadwal_pengantins')->onDelete('cascade');
            $table->string('bulan')->nullable();
            $table->integer('tahun')->nullable();
            $table->string('nama')->nullable();
            $table->text('alamat')->nullable();
            $table->foreignId('paket_id')->nullable()->constrained('pakets');

            $table->string('gown');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_gowns');
    }
};
