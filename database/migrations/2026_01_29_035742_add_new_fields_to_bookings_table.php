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
        Schema::table('bookings', function (Blueprint $table) {
            // Hanya tambah jika belum ada
            if (!Schema::hasColumn('bookings', 'parent_name')) {
                $table->string('parent_name')->nullable()->after('bride_groom_name');
            }

            // Lakukan hal yang sama untuk kolom lain yang menyebabkan error
            if (!Schema::hasColumn('bookings', 'another_column_name')) {
                $table->string('another_column_name')->nullable();
            }
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
