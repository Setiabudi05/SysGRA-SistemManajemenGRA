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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            // Relasi
            $table->bigInteger('user_id')->nullable(); 
            $table->bigInteger('paket_id')->nullable(); 
            $table->string('customer_name');
            $table->string('whatsapp_number');
            $table->string('bride_groom_name')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('facebook_name')->nullable();
            $table->string('instagram_name')->nullable();
            $table->text('event_address');
            $table->string('latitude')->nullable();    
            $table->string('longitude')->nullable();   
            $table->date('event_date');
            $table->integer('event_duration')->default(1);
            $table->string('package_name');
            $table->string('package_price');
            $table->text('notes')->nullable();         
            $table->string('status')->default('pending'); 
            $table->string('another_column_name')->nullable(); 

            $table->timestamps();
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
