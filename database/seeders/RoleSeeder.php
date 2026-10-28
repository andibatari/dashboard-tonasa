<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan cache Spatie Permission agar tidak error
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Gunakan firstOrCreate agar aman dijalankan berkali-kali tanpa error
        Role::firstOrCreate(['name' => 'Developer']);
        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Admin STMC']);
        Role::firstOrCreate(['name' => 'Dokter STMC']);
        Role::firstOrCreate(['name' => 'User']);
    }
}