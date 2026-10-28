<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Departemen;
use App\Models\UnitKerja;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan pengecekan foreign key untuk sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Hapus semua data dari tabel unit_kerjas
        UnitKerja::truncate();

        // Ambil ID dari departemen yang sudah ada atau buat jika belum ada
        $departemensId1 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Project Mngmnt & Maint. Support'])->id;
        $departemensId2 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Finance & Accounting'])->id;
        $departemensId3 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Human Capital & GRC'])->id;
        $departemensId4 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Internal Audit'])->id;
        $departemensId5 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Clinker & Cement Production'])->id;
        $departemensId6 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Communication, Legal & GA'])->id;
        $departemensId7 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Infrastructure'])->id;
        $departemensId8 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Maintenance'])->id;
        $departemensId9 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Market Planning & Development'])->id;
        $departemensId10 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Mining & Power Plant'])->id;
        $departemensId11 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Production Planning & Control'])->id;
        $departemensId12 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Sales'])->id;
        $departemensId13 = Departemen::firstOrCreate(['nama_departemen' => 'Staff of Pres Dir - BU Non Cement'])->id;
        $departemensId14 = Departemen::firstOrCreate(['nama_departemen' => 'Staff of Pres Dir (Pelsindo)'])->id;
        $departemensId15 = Departemen::firstOrCreate(['nama_departemen' => 'Staff of Pres Dir (BKS Raya)'])->id;
        $departemensId16 = Departemen::firstOrCreate(['nama_departemen' => 'Staff of Pres Dir (Dana Pensiun)'])->id;
        $departemensId17 = Departemen::firstOrCreate(['nama_departemen' => 'Staff of Pres Dir (Kopkar)'])->id;
        $departemensId18 = Departemen::firstOrCreate(['nama_departemen' => 'Staff of Pres Dir (PKM)'])->id;
        $departemensId19 = Departemen::firstOrCreate(['nama_departemen' => 'Staff of Pres Dir (Tonasa Lines)'])->id;
        $departemensId20 = Departemen::firstOrCreate(['nama_departemen' => 'Staff of Pres Dir (Topabiring)'])->id;
        $departemensId21 = Departemen::firstOrCreate(['nama_departemen' => 'Staff of Pres Dir (Sedaya Multi Matra)'])->id;
        $departemensId22 = Departemen::firstOrCreate(['nama_departemen' => 'Unit of Quality Assurance'])->id;
        $departemensId23 = Departemen::firstOrCreate(['nama_departemen' => 'Unit of Warehouse'])->id;

        // Masukkan data unit kerja
        DB::table('unit_kerjas')->insert([
            ['nama_unit_kerja' => 'Staff of TPM Officer', 'departemens_id' => $departemensId1],
            ['nama_unit_kerja' => 'Unit of CAPEX Management', 'departemens_id' =>$departemensId1],
            ['nama_unit_kerja' => 'Unit of Engineering', 'departemens_id' =>$departemensId1],
            ['nama_unit_kerja' => 'Unit of Maintenance Planning & Eval.', 'departemens_id' =>$departemensId1],
            ['nama_unit_kerja' => 'Unit of Project Management', 'departemens_id' =>$departemensId1],
            ['nama_unit_kerja' => 'Unit of Workshop & Design', 'departemens_id' =>$departemensId1],
            ['nama_unit_kerja' => 'Unit of Accounting', 'departemens_id' =>$departemensId2],
            ['nama_unit_kerja' => 'Unit of Budgeting & Performance', 'departemens_id' =>$departemensId2],
            ['nama_unit_kerja' => 'Unit of Finance', 'departemens_id' =>$departemensId2],
            ['nama_unit_kerja' => 'Unit of GRC & IC', 'departemens_id' =>$departemensId3],
            ['nama_unit_kerja' => 'Unit of CLD & Innovation', 'departemens_id' =>$departemensId3],
            ['nama_unit_kerja' => 'Unit of HC Operational', 'departemens_id' =>$departemensId3],
            ['nama_unit_kerja' => 'Unit of Health Serv & Industrial Hygiene', 'departemens_id' =>$departemensId3],
            ['nama_unit_kerja' => 'Staff of Internal Audit', 'departemens_id' =>$departemensId4],
            ['nama_unit_kerja' => 'Staff of Quality Assurance Int. Audit', 'departemens_id' =>$departemensId4],
            ['nama_unit_kerja' => 'Unit of Cement Production', 'departemens_id' =>$departemensId5],
            ['nama_unit_kerja' => 'Unit of Clinker Production', 'departemens_id' =>$departemensId5],
            ['nama_unit_kerja' => 'Unit of Derivative Product  & Supporting', 'departemens_id' =>$departemensId5],
            ['nama_unit_kerja' => 'Unit of Communication & Secretariate', 'departemens_id' =>$departemensId6],
            ['nama_unit_kerja' => 'Unit of General Facility & Asset', 'departemens_id' =>$departemensId6],
            ['nama_unit_kerja' => 'Unit of Legal', 'departemens_id' =>$departemensId6],
            ['nama_unit_kerja' => 'Unit of Security', 'departemens_id' =>$departemensId6],
            ['nama_unit_kerja' => 'Unit of TJSL', 'departemens_id' =>$departemensId6],
            ['nama_unit_kerja' => 'Unit of Packing Plant 1', 'departemens_id' =>$departemensId7],
            ['nama_unit_kerja' => 'Unit of Packing Plant 2', 'departemens_id' =>$departemensId7],
            ['nama_unit_kerja' => 'Unit of SCM Infra Port Management', 'departemens_id' =>$departemensId7],
            ['nama_unit_kerja' => 'Unit of Plant & Port Prod Discharge Opr', 'departemens_id' =>$departemensId7],
            ['nama_unit_kerja' => 'Unit of Interplant Logistic', 'departemens_id' =>$departemensId7],
            ['nama_unit_kerja' => 'Unit of Elins Maintenance 1', 'departemens_id' =>$departemensId8],
            ['nama_unit_kerja' => 'Unit of Elins Maintenance 2', 'departemens_id' =>$departemensId8],
            ['nama_unit_kerja' => 'Unit of Machine Maintenance 1', 'departemens_id' =>$departemensId8],
            ['nama_unit_kerja' => 'Unit of Machine Maintenance 2', 'departemens_id' =>$departemensId8],
            ['nama_unit_kerja' => 'Unit of Port Product Discharge Maint.', 'departemens_id' =>$departemensId8],
            ['nama_unit_kerja' => 'Unit of Reliability Maintenance', 'departemens_id' =>$departemensId8],
            ['nama_unit_kerja' => 'Unit of Market Intel. Channel & SF Mgmt', 'departemens_id' =>$departemensId9],
            ['nama_unit_kerja' => 'Staff of Sales Process Management', 'departemens_id' =>$departemensId9],
            ['nama_unit_kerja' => 'Unit of Product Pricing & Promotion Mgmt', 'departemens_id' =>$departemensId9],
            ['nama_unit_kerja' => 'Unit of Mine Plan', 'departemens_id' =>$departemensId10],
            ['nama_unit_kerja' => 'Unit of Mining Operation', 'departemens_id' =>$departemensId10],
            ['nama_unit_kerja' => 'Unit of Power Distribution', 'departemens_id' =>$departemensId10],
            ['nama_unit_kerja' => 'Unit of Power Plant Elins Maintenance', 'departemens_id' =>$departemensId10],
            ['nama_unit_kerja' => 'Unit of Power Plant Machine Maint', 'departemens_id' =>$departemensId10],
            ['nama_unit_kerja' => 'Unit of Power Plant Operation', 'departemens_id' =>$departemensId10],
            ['nama_unit_kerja' => 'Unit of Raw Material Management', 'departemens_id' =>$departemensId10],
            ['nama_unit_kerja' => 'Unit of AFR & Energy', 'departemens_id' =>$departemensId11],
            ['nama_unit_kerja' => 'Unit of OHS', 'departemens_id' =>$departemensId11],
            ['nama_unit_kerja' => 'Unit of Prod. Plan Eval. & Environmental', 'departemens_id' =>$departemensId11],
            ['nama_unit_kerja' => 'Unit of Production Support', 'departemens_id' =>$departemensId11],
            ['nama_unit_kerja' => 'Unit of Quality Control', 'departemens_id' =>$departemensId11],
            ['nama_unit_kerja' => 'Staff of Sales', 'departemens_id' =>$departemensId12],
            ['nama_unit_kerja' => 'Unit of Corporate Sales', 'departemens_id' =>$departemensId12],
            ['nama_unit_kerja' => 'Unit of Outbound Logistic', 'departemens_id' =>$departemensId12],
            ['nama_unit_kerja' => 'Unit of Sales Senior Office', 'departemens_id' =>$departemensId12],
            ['nama_unit_kerja' => 'Unit of Sales 1', 'departemens_id' =>$departemensId12],
            ['nama_unit_kerja' => 'Unit of Sales 2', 'departemens_id' =>$departemensId12],
            ['nama_unit_kerja' => 'Staff of Pres Dir - BU Non Cement', 'departemens_id' =>$departemensId13],
            ['nama_unit_kerja' => 'Staff of Pres Dir (Pelsindo)', 'departemens_id' =>$departemensId14],
            ['nama_unit_kerja' => 'Staff of Pres Dir (BKS Raya)', 'departemens_id' =>$departemensId15],
            ['nama_unit_kerja' => 'Staff of Pres Dir (Dana Pensiun)', 'departemens_id' =>$departemensId16],
            ['nama_unit_kerja' => 'Staff of Pres Dir (Kopkar)', 'departemens_id' =>$departemensId17],
            ['nama_unit_kerja' => 'Staff of Pres Dir (PKM)', 'departemens_id' =>$departemensId18],
            ['nama_unit_kerja' => 'Staff of Pres Dir (Tonasa Lines)', 'departemens_id' =>$departemensId19],
            ['nama_unit_kerja' => 'Staff of Pres Dir (Topabiring)', 'departemens_id' =>$departemensId20],
            ['nama_unit_kerja' => 'Staff of Pres Dir (Sedaya Multi Matra)', 'departemens_id' =>$departemensId21],
            ['nama_unit_kerja' => 'Unit of Quality Assurance', 'departemens_id' =>$departemensId22],
            ['nama_unit_kerja' => 'Unit of Warehouse', 'departemens_id' =>$departemensId23],
        ]);

        // Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
