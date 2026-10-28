<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_poli_id')->constrained('jadwal_polis')->onDelete('cascade');
            $table->foreignId('dokter_id')->nullable()->constrained('dokters')->onDelete('set null');
            
            // Kolom JSON untuk menampung Visus, ADD, PD, dll
            $table->json('data_mata')->nullable();
            
            $table->text('kesimpulan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('file_path')->nullable(); // Jika nanti butuh cetak PDF terpisah
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_results');
    }
};