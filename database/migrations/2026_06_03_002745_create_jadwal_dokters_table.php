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
    Schema::create('jadwal_dokters', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('dokter_id'); // Relasi ke tabel karyawan/user
        $table->date('tanggal'); // Tanggal tugas
        $table->timestamps();

        // Relasi (asumsi dokter ada di tabel karyawans)
        $table->foreign('dokter_id')->references('id')->on('dokters')->onDelete('cascade');
        // Unik agar 1 tanggal hanya bisa 1 dokter (opsional, sesuaikan kebutuhan)
        $table->unique(['tanggal', 'dokter_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_dokters');
    }
};
