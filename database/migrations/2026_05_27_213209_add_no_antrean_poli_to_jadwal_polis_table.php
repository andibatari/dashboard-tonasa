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
        Schema::table('jadwal_polis', function (Blueprint $table) {
            $table->integer('no_antrean_poli')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_polis', function (Blueprint $table) {
            // ✅ Tambahkan kode ini untuk menghapus kolom jika rollback
            $table->dropColumn('no_antrean_poli');
        });
    }
};
