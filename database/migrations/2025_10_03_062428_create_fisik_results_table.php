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
        Schema::create('fisik_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_poli_id')->constrained('jadwal_polis')->onDelete('cascade');
            $table->foreignId('dokter_id')->nullable()->constrained('dokters')->onDelete('set null');
            
            $table->json('data_fisik')->nullable()->comment('Menyimpan semua data pemeriksaan fisik dalam format JSON');
            $table->text('kesimpulan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('file_path')->nullable()->comment('Path file PDF hasil pemeriksaan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fisik_results');
    }
};
