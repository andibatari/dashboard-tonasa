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
        Schema::create('kebugaran_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_poli_id')->constrained('jadwal_polis')->onDelete('cascade');
            $table->double('vo2_max'); // Kebutuhan VO2 Maksimal
            $table->integer('durasi_menit'); // Lama pemeriksaan
            $table->string('beban_latihan'); // Beban Latihan (contoh: Level 3)
            $table->integer('denyut_nadi'); // Jumlah denyut nadi
            $table->double('indeks_kebugaran')->nullable(); // Indeks Kebugaran Jasmani
            $table->string('kategori')->nullable(); // Kategori Kebugaran (contoh: Cukup)
            $table->string('file_path')->nullable(); // Untuk menyimpan path PDF yang di-generate
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kebugaran_results');
    }
};
