<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::disableQueryLog();

        // Mengosongkan tabel sebelum seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('provinsis')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // --- Logika untuk Provinsi ---
        Log::info('Seeding provinsi...');
        try {
            $provinsiJson = File::get(database_path('data/provinsis.json'));
            $provinsis = json_decode($provinsiJson, true);
            
            if (is_array($provinsis) && !empty($provinsis)) {
                // Perbaikan: Memecah data menjadi potongan 100 baris
                foreach (array_chunk($provinsis, 100) as $chunk) {
                    DB::table('provinsis')->insert($chunk);
                }
                Log::info('Provinsi berhasil di-seed.');
            } else {
                Log::error('Gagal memproses data provinsi dari file JSON atau file kosong.');
            }
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat seeding provinsi: ' . $e->getMessage());
        }

    }
}
