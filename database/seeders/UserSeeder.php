<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari unit Busdev untuk mengaitkan dengan user yang akan dibuat
        $unitBusdev = Unit::where('code_unit', 'BUSDEV')->first();

        $developer = User::create([
            'name' => 'Andi Batari',
            'email' => 'andibatari2311@gmail.com',
            'password' => Hash::make('tari1123'),
            'unit_id' => $unitBusdev->id ?? null,
        ]);

        $developer->assignRole('Developer');
    }
}
