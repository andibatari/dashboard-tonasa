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
        Schema::create('notif', function (Blueprint $table) {
            $table->id();
            // Kolom ini akan menyimpan ID karyawan yang menerima notifikasi.
            $table->unsignedBigInteger('karyawan_id');

            // Kolom string untuk menyimpan jenis notifikasi (contoh: 'mcu_approved').
            $table->string('type');

            // Kolom JSON untuk menyimpan data spesifik notifikasi (contoh: nomor antrean, nama dokter, dll.).
            $table->json('data')->nullable();

            // Kolom timestamp untuk menandai kapan notifikasi dibaca. Nullable karena awalnya belum dibaca.
            $table->timestamp('read_at')->nullable();

            $table->timestamps(); // Kolom created_at dan updated_at otomatis.

            // Mendefinisikan kunci asing (foreign key) ke tabel 'karyawans'.
            // Jika data karyawan dihapus, semua notifikasinya juga akan ikut terhapus.
            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notif');
    }
};
