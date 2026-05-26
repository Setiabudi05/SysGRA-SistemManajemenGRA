<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToCartsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Tambahkan kolom identitas baru
            $table->string('customer_name')->after('paket_id');
            $table->string('bride_groom_name')->after('customer_name');
            $table->string('whatsapp_number')->after('bride_groom_name');
            $table->string('parent_name')->nullable()->after('whatsapp_number');
            $table->string('facebook_name')->nullable()->after('parent_name');
            $table->string('instagram_name')->nullable()->after('facebook_name');

            // Tambahkan kolom rentang tanggal untuk kebutuhan Algoritma CBS
            $table->date('start_date')->after('instagram_name');
            $table->date('end_date')->after('start_date');
            $table->string('event_duration')->after('end_date');

            // Rename kolom lama agar sesuai dengan form baru
            if (Schema::hasColumn('carts', 'lokasi')) {
                $table->renameColumn('lokasi', 'event_address');
            }

            if (Schema::hasColumn('carts', 'catatan')) {
                $table->renameColumn('catatan', 'notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn([
                'bride_groom_name',
                'whatsapp_number',
                'parent_name',
                'facebook_name',
                'instagram_name',
                'start_date',
                'end_date',
                'event_duration'
            ]);
        });
    }
}
