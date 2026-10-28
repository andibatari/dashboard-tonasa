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
        Schema::table('icofr_cycles', function (Blueprint $table) {
            // Tambahkan kolom string yang boleh kosong (nullable) setelah kolom 'name'
            $table->string('bpm_file_path')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('icofr_cycles', function (Blueprint $table) {
            // Untuk menghapus kolom jika melakukan rollback
            $table->dropColumn('bpm_file_path');
        });
    }
};
