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
        Schema::create('jadwal_mcus', function (Blueprint $table) {
            $table->id();
            $table->uuid('qr_code_id')->nullable();
            
            // Kolom untuk ID pasien dari salah satu tabel
            $table->unsignedBigInteger('karyawan_id')->nullable(); 
            $table->unsignedBigInteger('peserta_mcus_id')->nullable(); // Perbaikan nama kolom: peserta_mcus_id
            $table->unsignedBigInteger('paket_mcus_id')->nullable();
            
            // Kolom yang menyimpan data umum untuk kedua jenis pasien
            $table->string('no_sap')->nullable();
            $table->string('nik_pasien')->nullable(); // Digunakan untuk NIK dari kedua tabel
            $table->string('nama_pasien')->nullable(); // Digunakan untuk nama dari kedua tabel
            $table->string('perusahaan_asal')->nullable(); // Digunakan untuk non-karyawan

            // Kolom wajib untuk semua jadwal
            $table->date('tanggal_mcu');
            $table->date('tanggal_pendaftaran');
            $table->string('no_antrean', 20)->nullable();
            $table->unsignedBigInteger('dokter_id')->nullable();
            $table->text('resume_body')->nullable();
            $table->text('resume_saran')->nullable();
            $table->string('resume_kategori')->nullable();
            $table->string('status', 50)->default('Scheduled'); // Mengubah default menjadi Scheduled


            // Definisi Foreign Keys
            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('set null');
            $table->foreign('peserta_mcus_id')->references('id')->on('peserta_mcus')->onDelete('set null');
            $table->foreign('dokter_id')->references('id')->on('dokters')->onDelete('set null');
            $table->foreign('paket_mcus_id')->references('id')->on('paket_mcus')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_mcus');
    }
};