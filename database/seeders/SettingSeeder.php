<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate(
            ['key' => 'kmst_event_active'],
            ['value' => '1', 'description' => 'Tampilkan menu KMST Inovasi di Sidebar Karyawan']
        );

        Setting::firstOrCreate(
            ['key' => 'maintenance_mode'],
            ['value' => '0', 'description' => 'Tampilkan banner Sedang Pemeliharaan Sistem']
        );
    }
}