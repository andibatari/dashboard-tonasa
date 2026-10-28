<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Departemen;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menonaktifkan foreign key checks untuk menghindari masalah dengan truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Mengosongkan tabel departemens
        Departemen::truncate();

        // Mengaktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Masukkan data awal untuk Departemen
        DB::table('departemens') -> insert([
            ['nama_departemen' => 'Dept. of Project Mngmnt & Maint. Support'],
            ['nama_departemen' => 'Dept. of Finance & Accounting'],
            ['nama_departemen' => 'Dept. of Human Capital & GRC'],
            ['nama_departemen' => 'Dept. of Internal Audit'],
            ['nama_departemen' => 'Dept. of Clinker & Cement Production'],
            ['nama_departemen' => 'Dept. of Communication, Legal & GA'],
            ['nama_departemen' => 'Dept. of Infrastructure'],
            ['nama_departemen' => 'Dept. of Maintenance'],
            ['nama_departemen' => 'Dept. of Market Planning & Development'],
            ['nama_departemen' => 'Dept. of Mining & Power Plant'],
            ['nama_departemen' => 'Dept. of Production Planning & Control'],
            ['nama_departemen' => 'Dept. of Sales'],
            ['nama_departemen' => 'Staff of Pres Dir - BU Non Cement'],
            ['nama_departemen' => 'Staff of Pres Dir (Pelsindo)'],
            ['nama_departemen' => 'Staff of Pres Dir (BKS Raya)'],
            ['nama_departemen' => 'Staff of Pres Dir (Dana Pensiun)'],
            ['nama_departemen' => 'Staff of Pres Dir (Kopkar)'],
            ['nama_departemen' => 'Staff of Pres Dir (PKM)'],
            ['nama_departemen' => 'Staff of Pres Dir (Tonasa Lines)'],
            ['nama_departemen' => 'Staff of Pres Dir (Topabiring)'],
            ['nama_departemen' => 'Staff of Pres Dir (Sedaya Multi Matra)'],
            ['nama_departemen' => 'Unit of Quality Assurance'],
            ['nama_departemen' => 'Unit of Warehouse'],
        ]);
    }
}
