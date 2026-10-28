<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('icofr_csa_reports', function (Blueprint $table) {
        $table->id();
        // Relasi ke Master Kontrol RCM
        $table->foreignId('icofr_control_id')->constrained('icofr_controls')->cascadeOnDelete();
        
        // Penanda Waktu (Periode)
        $table->integer('tahun');
        $table->enum('triwulan', ['TW1', 'TW2', 'TW3', 'TW4']);
        
        // Hasil Pelaporan dari Excel / Manual
        $table->enum('hasil_csa', ['efektif', 'tidak_efektif', 'tidak_ada_transaksi','sedang_berjalan', 'belum_dinilai'])->default('belum_dinilai');
        $table->text('keterangan_tidak_efektif')->nullable();
        $table->string('root_cause')->nullable();
        $table->string('referensi_kertas_kerja')->nullable();
        $table->text('rencana_remediasi')->nullable();
        $table->string('status_remediasi')->default('open'); // open/closed

        $table->timestamps();

        // Mencegah 1 kontrol dinilai 2 kali di triwulan & tahun yang sama
        $table->unique(['icofr_control_id', 'tahun', 'triwulan'], 'unique_control_period');
    });
}
};
