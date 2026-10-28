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
        Schema::create('sysdev_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // e.g. S01, S02, etc.
            $table->string('kategori_grup', 50)->default('ISO INTERGRASI'); // ISO INTERGRASI, SNI, TKDN, GREEN LABEL
            $table->string('kategori_singkat', 50)->nullable(); // ISO, SMT, SMK3, IH, SNI, TKDN, GREEN LABEL
            $table->string('jenis_sertifikat'); // ISO 9001:2015, etc.
            $table->string('nomor_sertifikat')->nullable();
            $table->string('nama_produk')->nullable(); // Manajemen Mutu, etc.
            $table->string('lembaga_penerbit')->nullable(); // PT. Sucofindo, B4T, GPCI, KAN, Kemenaker
            $table->date('tanggal_terbit')->nullable();
            $table->date('tanggal_expired')->nullable();
            $table->text('link_sertifikat')->nullable(); // https://s.id/...
            $table->string('pic_email')->nullable();
            $table->integer('hari_tersisa_manual')->nullable();
            $table->string('status_validitas', 30)->default('Aktif'); // Aktif, Renewal, Expired
            $table->boolean('notifikasi')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sysdev_certificates');
    }
};
