<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use App\Models\PaketMcu;
use App\Models\Poli;

class PaketPoliSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('paket_poli')->truncate();
        PaketMcu::truncate();
        Poli::truncate();

        // MASTER POLI TERBARU (Sudah dipisah)
        Poli::create(['nama_poli' => 'LABORATORIUM']);
        Poli::create(['nama_poli' => 'GIGI']);
        Poli::create(['nama_poli' => 'FISIK']);
        Poli::create(['nama_poli' => 'MATA']); 
        Poli::create(['nama_poli' => 'EKG']);
        Poli::create(['nama_poli' => 'AUDIOMETRI']);
        Poli::create(['nama_poli' => 'SPIROMETRI']);
        Poli::create(['nama_poli' => 'KEBUGARAN']); 
        Poli::create(['nama_poli' => 'TREADMILL']); 
        Poli::create(['nama_poli' => 'THORAX PHOTO']);
        Poli::create(['nama_poli' => 'USG']);

        Schema::enableForeignKeyConstraints();
    }
}