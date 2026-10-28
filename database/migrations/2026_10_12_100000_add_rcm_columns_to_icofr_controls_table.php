<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Penilaian CSA oleh user umum sudah dihapus. Status CSA kini disimpan langsung di icofr_controls.
        Schema::dropIfExists('icofr_assessments');

        Schema::table('icofr_controls', function (Blueprint $table) {
            // Kunci import: mencegah data ganda saat file RCM diunggah ulang
            $table->string('row_key', 64)->nullable()->unique();

            // Perusahaan & lokasi
            $table->string('company', 50)->nullable();
            $table->string('location', 100)->nullable();

            // Risiko (kolom Excel: Faktor Kuantitatif .. Risiko Kecurangan)
            $table->string('risk_quantitative', 100)->nullable();
            $table->string('risk_qualitative', 100)->nullable();
            $table->string('risk_value', 50)->nullable();
            $table->string('fraud_risk', 50)->nullable();

            // Kontrol (Fungsi Pelaksana .. Frekuensi)
            $table->string('executor_function')->nullable();
            $table->string('executor')->nullable();
            $table->string('control_timing', 50)->nullable();   // Preventif / Detektif
            $table->string('control_nature')->nullable();       // Sifat kontrol
            $table->string('automation_type')->nullable();      // Jenis kontrol otomatis
            $table->string('frequency', 100)->nullable();

            // Informasi: C, A, V, RA
            $table->boolean('info_c')->default(false);
            $table->boolean('info_a')->default(false);
            $table->boolean('info_v')->default(false);
            $table->boolean('info_ra')->default(false);

            // Asersi: C, A, E/O, CO, R&O, V/A, PD
            $table->boolean('asr_c')->default(false);
            $table->boolean('asr_a')->default(false);
            $table->boolean('asr_eo')->default(false);
            $table->boolean('asr_co')->default(false);
            $table->boolean('asr_ro')->default(false);
            $table->boolean('asr_va')->default(false);
            $table->boolean('asr_pd')->default(false);

            // Lain-lain
            $table->string('supporting_application')->nullable();
            $table->string('fs_item')->nullable();               // Item laporan keuangan terdampak
            $table->date('effective_date')->nullable();          // Periode efektif
            $table->text('control_attribute')->nullable();
            $table->text('supporting_documents')->nullable();
            $table->text('control_objective')->nullable();
            $table->text('control_procedure')->nullable();
            $table->text('remarks')->nullable();

            // Hasil CSA (diisi Busdev): efektif | tidak_efektif | tidak_ada | in_progress | NULL (belum)
            $table->string('csa_status', 20)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('icofr_controls', function (Blueprint $table) {
            $table->dropUnique(['row_key']);
            $table->dropIndex(['csa_status']);
            $table->dropColumn([
                'row_key', 'company', 'location', 'risk_quantitative', 'risk_qualitative', 'risk_value', 'fraud_risk',
                'executor_function', 'executor', 'control_timing', 'control_nature', 'automation_type', 'frequency',
                'info_c', 'info_a', 'info_v', 'info_ra',
                'asr_c', 'asr_a', 'asr_eo', 'asr_co', 'asr_ro', 'asr_va', 'asr_pd',
                'supporting_application', 'fs_item', 'effective_date',
                'control_attribute', 'supporting_documents', 'control_objective', 'control_procedure', 'remarks',
                'csa_status',
            ]);
        });
    }
};
