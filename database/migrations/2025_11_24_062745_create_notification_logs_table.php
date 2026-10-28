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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->date('scheduled_date'); // Tanggal jadwal MCU yang diingatkan
            $table->string('mode')->default('manual'); // 'manual' atau 'automatic'
            $table->integer('total_targets');      // Jumlah total karyawan yang ditargetkan
            $table->integer('email_success')->default(0);
            $table->integer('fcm_success')->default(0); // Firebase Cloud Messaging (Aplikasi Mobile)
            $table->json('failed_recipients')->nullable(); // JSON data: ['nik', 'reason']
            $table->foreignId('admin_users_id')->nullable()->constrained('admin_users'); // Jika dikirim manual oleh admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
