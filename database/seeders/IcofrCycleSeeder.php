<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\IcofrCycle;

class IcofrCycleSeeder extends Seeder
{
    public function run(): void
    {
        $cycles = [
            ['code' => 'A', 'name' => 'Pendapatan'],
            ['code' => 'B', 'name' => 'Persediaan & Produksi'],
            ['code' => 'C', 'name' => 'Beban Gaji'],
            ['code' => 'D', 'name' => 'Aset Tetap'],
            ['code' => 'E', 'name' => 'Perbendaharaan'],
            ['code' => 'F', 'name' => 'Perpajakan'],
            ['code' => 'G', 'name' => 'Pelaporan Keuangan'],
        ];

        foreach ($cycles as $cycle) {
            IcofrCycle::firstOrCreate(['code' => $cycle['code']], $cycle);
        }
    }
}