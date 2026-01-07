<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('jadwal_gowns', function (Blueprint $table) {
            $table->integer('tahun')->after('bulan')->nullable();
        });
    }

    public function down()
    {
        Schema::table('jadwal_gowns', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });
    }

};
