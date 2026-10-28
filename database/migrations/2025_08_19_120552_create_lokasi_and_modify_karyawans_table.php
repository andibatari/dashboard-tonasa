<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinsis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_provinsi');
            $table->timestamps();
        });

        Schema::table('karyawans', function (Blueprint $table) {
            // Safety Drop untuk kolom lama/percobaan sebelumnya
            if (Schema::hasColumn('karyawans', 'provinsi_id')) {
                try { $table->dropForeign(['provinsi_id']); } catch (\Exception $e) {}
                $table->dropColumn('provinsi_id');
            }
            if (Schema::hasColumn('karyawans', 'kabupaten_id')) { // Cek nama kolom lama
                try { $table->dropForeign(['kabupaten_id']); } catch (\Exception $e) {}
                $table->dropColumn('kabupaten_id');
            }
            if (Schema::hasColumn('karyawans', 'kecamatan_id')) { // Cek nama kolom lama
                try { $table->dropForeign(['kecamatan_id']); } catch (\Exception $e) {}
                $table->dropColumn('kecamatan_id');
            }
            
            // Tambahkan kembali kolom lokasi
            
            // Provinsi: Tetap Foreign ID ke tabel provinsis
            $table->foreignId('provinsi_id')->nullable()->after('unit_kerjas_id')->constrained()->onDelete('set null');
            
            // PERUBAHAN NAMA KOLOM UTAMA: Kabupaten dan Kecamatan menjadi String Nama
            $table->string('nama_kabupaten', 255)->nullable()->after('provinsi_id'); 
            $table->string('nama_kecamatan', 255)->nullable()->after('nama_kabupaten');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Drop Foreign Key Provinsi
            $table->dropForeign(['provinsi_id']);
            $table->dropColumn('provinsi_id');
            
            // Drop kolom string yang baru
            $table->dropColumn('nama_kabupaten');
            $table->dropColumn('nama_kecamatan');
        });

        Schema::dropIfExists('provinsis');
    }
};
