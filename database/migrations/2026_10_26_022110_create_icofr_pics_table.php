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
        Schema::create('icofr_pics', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();              // Nama Pelaksana Kontrol (e.g. Ricky Novianto, S.Ak.)
            $table->string('position');                      // Jabatan Pelaksana Kontrol (e.g. SM of General Facility & Aset)
            $table->string('unit');                          // Unit Pelaksana Kontrol (e.g. Unit of General Facility & Aset)
            $table->string('department')->nullable();        // Departemen (e.g. Department of Communication, Legal & GA)
            $table->string('phone')->nullable();             // No WhatsApp/Telp (opsional)
            $table->text('notes')->nullable();               // Catatan tambahan (opsional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icofr_pics');
    }
};
