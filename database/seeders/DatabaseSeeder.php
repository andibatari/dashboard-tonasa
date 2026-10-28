<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentUnitSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SysdevCertificateSeeder::class,
        ]);
    }
}