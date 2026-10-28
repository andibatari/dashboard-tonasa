<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('poli_gigi_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_poli_id')->constrained('jadwal_polis')->onDelete('cascade');
            $table->json('data_pemeriksaan')->nullable(); // Simpan semua data form dalam format JSON
            $table->string('kesimpulan')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('file_path')->nullable(); // Untuk menyimpan path PDF yang di-generate
            $table->unsignedBigInteger('dokter_id')->nullable(); // Kolom untuk menyimpan ID dokter
            $table->foreign('dokter_id')->references('id')->on('dokters')->onDelete('set null'); // Asumsikan tabel dokter adalah 'users'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('poli_gigi_results');
    }
};