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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Data Pemesan
            $table->string('customer_name');
            $table->string('whatsapp_number');
            $table->string('bride_groom_name')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('facebook_name')->nullable();
            $table->string('instagram_name')->nullable();

            // Data Acara
            $table->text('event_address');
            $table->date('event_date');
            $table->string('event_duration')->nullable();

            // Data Paket
            $table->string('package_name');
            $table->string('package_price');

            // Catatan
            $table->text('add_ons')->nullable();
            $table->text('gown_notes')->nullable();
            $table->text('other_notes')->nullable();

            // (Opsional) Tambahkan ini untuk melacak status
            // $table->string('status')->default('pending'); 

            $table->timestamps(); // Ini akan membuat created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
