<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Siklus Utama (Contoh: F. Perpajakan)
        Schema::create('icofr_cycles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5); // Kode huruf (A, B, C...)
            $table->string('name'); // Nama Siklus
            $table->timestamps();
        });

        // 2. Tabel Proses Bisnis (Contoh: 01. PPh 21)
        Schema::create('icofr_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('icofr_cycle_id')->constrained()->cascadeOnDelete();
            $table->string('code', 10); // Kode angka (01, 02...)
            $table->string('name'); // Nama Proses
            $table->timestamps();
        });

        // 3. Tabel Matriks Kontrol / RCM (Risk Control Matrix)
        Schema::create('icofr_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('icofr_process_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete(); // Unit yang mengerjakan
            
            // Data Risiko (Risk)
            $table->string('risk_code', 10)->nullable(); // Contoh: R1
            $table->text('risk_description')->nullable(); 
            
            // Data Kontrol (Control)
            $table->string('control_code', 10)->nullable(); // Contoh: C1
            $table->text('control_description')->nullable();
            
            // Tipe Kontrol (True = Key Control, False = Non-Key Control)
            $table->boolean('is_key_control')->default(false); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('icofr_controls');
        Schema::dropIfExists('icofr_processes');
        Schema::dropIfExists('icofr_cycles');
    }
};