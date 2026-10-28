<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kmst_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // SS, GKM, PKM, BI
            $table->string('nama_tim');
            $table->json('data_peserta'); // Akan menampung SEMUA nama, SAP, UK, dan HP dari seluruh peserta
            $table->json('bidang_benefit')->nullable();
            $table->string('file_makalah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kmst_registrations');
    }
};