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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('parent_name')->nullable()->after('bride_groom_name');
            $table->string('facebook_name')->nullable()->after('parent_name');
            $table->string('instagram_name')->nullable()->after('facebook_name');
            $table->integer('event_duration')->default(1)->after('event_date');
            $table->text('add_ons')->nullable()->after('package_price');
            $table->text('notes')->nullable()->after('add_ons');
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
