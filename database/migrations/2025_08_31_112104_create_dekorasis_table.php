<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dekorasis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paket_id'); // relasi ke paket
            $table->string('foto')->nullable();
            $table->text('deskripsi');
            $table->timestamps();

            $table->foreign('paket_id')->references('id')->on('pakets')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dekorasis');
    }
};
