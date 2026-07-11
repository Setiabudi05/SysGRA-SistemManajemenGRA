<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pesanan_id')->unsigned();
            $table->string('bukti_transfer')->nullable();
            $table->string('jumlah_bayar');
            $table->string('keterangan')->nullable(); 
            $table->string('status_pembayaran', 50)->nullable(); 
            $table->timestamps();
            // Menambahkan foreign key manual jika ingin menjaga integritas relasi
            $table->foreign('pesanan_id')->references('id')->on('bookings')->onDelete('cascade');
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
