<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembukuan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('tipe');
            $table->string('customer')->nullable();
            $table->string('keterangan')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->bigInteger('pembayaran_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('pembayaran_id')->references('id')->on('pembayarans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembukuan');
    }
};
