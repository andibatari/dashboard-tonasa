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
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('no_sap', 50)->unique();
            $table->string('nik_karyawan', 16)->nullable()->unique();
            $table->string('nama_karyawan')->nullable();
            
            $table->string('pekerjaan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('kebangsaan')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('umur')->nullable();
            
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->decimal('berat_badan', 5, 2)->nullable();
            
            $table->string('jenis_kelamin')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('agama')->nullable();
            $table->string('status_pernikahan')->nullable();
            $table->string('hubungan')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('eselon')->nullable();
            $table->string('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->string('foto_profil')->nullable();
            
            $table->unsignedBigInteger('departemens_id')->nullable();
            $table->unsignedBigInteger('unit_kerjas_id')->nullable();

            $table->foreign('departemens_id')->references('id')->on('departemens')->onDelete('set null');
            $table->foreign('unit_kerjas_id')->references('id')->on('unit_kerjas')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};