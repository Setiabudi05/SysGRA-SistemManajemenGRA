<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('baju_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('baju_id')->constrained('baju_pengantins')->onDelete('cascade'); // Menghubungkan ke tabel master baju
            $table->string('warna');
            $table->string('ukuran');
            $table->integer('stok')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baju_variants');
    }
};
