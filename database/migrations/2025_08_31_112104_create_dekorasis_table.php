<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dekorasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_id')->constrained('pakets')->onDelete('cascade');
            $table->string('foto')->nullable(); 
            $table->text('deskripsi');         
        });
    }

    public function down()
    {
        Schema::dropIfExists('dekorasis');
    }
};
