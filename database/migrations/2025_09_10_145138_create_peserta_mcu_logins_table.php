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
        Schema::create('peserta_mcu_logins', function (Blueprint $table) {
            $table->id();
            $table->string('nik_pasien')->unique(); 
            $table->foreignId('peserta_mcu_id')->constrained('peserta_mcus')->onDelete('cascade');
            $table->string('password');
            $table->string('fcm_token', 255)->nullable(); // 🌟 Kolom token pindah ke sini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_mcu_logins');
    }
};