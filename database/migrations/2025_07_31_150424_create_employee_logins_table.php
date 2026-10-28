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
        Schema::create('employee_logins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id')->unique();
            $table->string('no_sap', 50)->unique();
            $table->string('password');
            $table->string('fcm_token', 255)->nullable(); // 🌟 Kolom token pindah ke sini
            $table->timestamps();

            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('cascade');
        });
    }  

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_logins');
    }
};