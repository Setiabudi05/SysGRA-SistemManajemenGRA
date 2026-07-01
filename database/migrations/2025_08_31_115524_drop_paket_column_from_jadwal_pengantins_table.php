<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_pengantins', function (Blueprint $table) {
            $table->dropColumn('paket');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_pengantins', function (Blueprint $table) {
            $table->string('paket')->after('alamat');
        });
    }
};
