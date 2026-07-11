<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel pembayarans.
     */
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel bookings
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->string('bukti_transfer')->nullable(); // Foto bukti transfer
            $table->string('jumlah_bayar'); 
            $table->enum('status_pembayaran', ['pending', 'valid', 'invalid'])->default('pending');
            $table->text('catatan_admin')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi (Hapus tabel).
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};