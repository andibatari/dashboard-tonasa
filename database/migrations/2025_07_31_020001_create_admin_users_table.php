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
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();

            // Kolom untuk Login & Informasi Admin
            $table->string('no_sap', 50)->unique()->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('nik',20)->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->string('role')->default('admin'); // Bisa diisi 'admin', 'superadmin', dll.
            $table->string('foto_profil')->nullable();
            
            // Kolom ID Sumber Admin
            $table->unsignedBigInteger('dokter_id')->nullable();
            $table->unsignedBigInteger('karyawan_id')->nullable(); // <-- TAMBAH KOLOM INI

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};