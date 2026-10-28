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
        Schema::create('pemantauan_lingkungans', function (Blueprint $table) {
            $table->id();
            $table->string('area'); // Misal: Crusher 4, Clay 5
            $table->string('lokasi'); // Misal: Lantai 1, Ruang Kontrol
            $table->date('tanggal_pemantauan')->nullable();
            $table->decimal('nab_cahaya', 8, 2)->nullable();
            $table->integer('nab_bising')->nullable();
            $table->string('nab_debu')->nullable();
            $table->decimal('nab_suhu', 8, 2)->nullable(); // NEW FIELD for NAB Suhu
            $table->json('data_pemantauan'); // Menyimpan semua data pengukuran dalam format JSON
            // --- KOLOM KESIMPULAN BARU ---
            $table->text('kesimpulan')->nullable(); // TIDAK WAJIB

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
        Schema::dropIfExists('pemantauan_lingkungans');
    }
};
