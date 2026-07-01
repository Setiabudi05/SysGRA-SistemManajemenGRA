<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pakets', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket', 100); // nama paket (misal: All In Diamond)
            $table->text('makeup')->nullable(); // detail makeup
            $table->text('dekorasi')->nullable(); // detail dekorasi
            $table->text('dokumentasi')->nullable(); // detail dokumentasi
            $table->bigInteger('harga'); // harga paket
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pakets');
    }
};
