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
        Schema::create('jadwal_polis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_mcus_id');
            $table->unsignedBigInteger('poli_id');
            $table->string('status', 50)->default('Pending');
            $table->timestamps();
            $table->unique(['jadwal_mcus_id', 'poli_id']); // Mencegah duplikat
            $table->foreign('jadwal_mcus_id')->references('id')->on('jadwal_mcus')->onDelete('cascade');
            $table->foreign('poli_id')->references('id')->on('polis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_poli');
    }
};
