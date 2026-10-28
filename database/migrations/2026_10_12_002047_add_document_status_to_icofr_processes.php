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
        Schema::table('icofr_processes', function (Blueprint $table) {
            $table->boolean('dokumentasi_done')->default(false); // (a) Dokumentasi BPM & RCM
            $table->boolean('validasi_done')->default(false);    // (b) Validasi rancangan pengendalian
            $table->boolean('bpm_updated')->default(false);      // (d) Pemutakhiran BPM
            $table->boolean('rcm_updated')->default(false);      // (e) Pemutakhiran RCM
        });
    }

    public function down(): void
    {
        Schema::table('icofr_processes', function (Blueprint $table) {
            $table->dropColumn(['dokumentasi_done', 'validasi_done', 'bpm_updated', 'rcm_updated']);
        });
    }
};
