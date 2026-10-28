<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('paket_poli', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paket_mcu_id');
            $table->unsignedBigInteger('poli_id');

            $table->foreign('paket_mcu_id')->references('id')->on('paket_mcus')->onDelete('cascade');
            $table->foreign('poli_id')->references('id')->on('polis')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_poli');
    }
};
