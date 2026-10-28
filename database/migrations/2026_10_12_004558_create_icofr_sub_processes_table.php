<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel sub proses: setiap sub proses milik satu proses bisnis
        Schema::create('icofr_sub_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('icofr_process_id')->constrained('icofr_processes')->cascadeOnDelete();
            $table->string('code', 10);
            $table->string('name');
            $table->timestamps();

            // Kode sub proses boleh sama di proses yang berbeda, tetapi unik dalam satu proses
            $table->unique(['icofr_process_id', 'code']);
        });

        // 2. Kontrol boleh punya sub proses (opsional). Bila sub proses dihapus, kolom ini menjadi NULL.
        Schema::table('icofr_controls', function (Blueprint $table) {
            $table->foreignId('icofr_sub_process_id')
                ->nullable()
                ->after('icofr_process_id')
                ->constrained('icofr_sub_processes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('icofr_controls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('icofr_sub_process_id');
        });

        Schema::dropIfExists('icofr_sub_processes');
    }
};