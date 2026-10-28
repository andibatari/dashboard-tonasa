<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_mcus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('no_sap', 50)->nullable();
            $table->enum('tipe_anggota', ['Istri', 'Suami', 'Non-Karyawan'])->nullable();
            $table->string('nik_pasien')->nullable();
            $table->string('nama_lengkap');
            $table->string('jenis_kelamin')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('umur')->nullable();
            
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->decimal('berat_badan', 5, 2)->nullable();
            
            $table->string('golongan_darah')->nullable();
            $table->string('agama')->nullable();
            $table->string('status_pernikahan')->nullable();
            $table->string('hubungan')->nullable();
            $table->string('kebangsaan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('perusahaan_asal')->nullable();
            $table->string('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->string('foto_profil')->nullable();

            $table->foreignId('provinsi_id')->nullable()->references('id')->on('provinsis')->onDelete('set null'); 
            $table->string('nama_kabupaten', 255)->nullable(); 
            $table->string('nama_kecamatan', 255)->nullable(); 

            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_mcus');
    }
};