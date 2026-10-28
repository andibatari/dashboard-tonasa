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
        Schema::table('jadwal_mcus', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_mcus', 'is_read_admin')) {
                $table->boolean('is_read_admin')->default(false)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_mcus', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_mcus', 'is_read_admin')) {
                $table->dropColumn('is_read_admin');
            }
        });
    }
};
