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
        Schema::create('sysdev_audit_findings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('no')->nullable();
            $table->string('category', 50)->nullable(); // Major, Minor, Observation
            $table->string('jenis', 100)->nullable(); // IH, SNI, etc.
            $table->text('description')->nullable(); // Deskripsi Ketidaksesuaian
            $table->text('root_cause')->nullable(); // Analisa Akar Penyebab
            $table->text('correction')->nullable(); // Tindakan Koreksi
            $table->text('corrective_action')->nullable(); // Tindakan Korektif
            $table->text('file_evidence_desc')->nullable(); // Deskripsi Bukti Dokumen
            $table->text('file_evidence')->nullable(); // Link Dokumen / Evidence
            $table->text('audit_review')->nullable(); // Hasil Review Auditor
            $table->string('plan_date')->nullable(); // Rencana Realisasi
            $table->string('real_date')->nullable(); // Realisasi Tindak Lanjut
            $table->text('explanation')->nullable(); // Keterangan
            $table->string('pic')->nullable(); // PIC
            $table->string('status_lengkap', 50)->default('Belum Lengkap'); // Belum Lengkap, Lengkap
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sysdev_audit_findings');
    }
};
