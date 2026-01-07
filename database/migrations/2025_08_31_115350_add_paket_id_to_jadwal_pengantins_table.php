<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_pengantins', function (Blueprint $table) {
            $table->unsignedBigInteger('paket_id')->after('alamat')->nullable();

            // buat foreign key
            $table->foreign('paket_id')->references('id')->on('pakets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_pengantins', function (Blueprint $table) {
            $table->dropForeign(['paket_id']);
            $table->dropColumn('paket_id');
        });
    }
};
