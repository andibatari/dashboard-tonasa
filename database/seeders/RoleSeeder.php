<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * 5 Role Resmi Sistem Dashboard Tonasa & STMC:
     * 1. Developer     : Khusus pengembang sistem (Andi Batari) - Akses penuh seluruh sistem & teknis.
     * 2. Super Admin   : Administrator umum - Manajemen akun, log aktivitas, pengaturan sistem.
     * 3. Admin Busdev  : Divisi Business & System Development - BPM ICoFR, Sertifikasi, Inovasi KMST.
     * 4. Admin STMC    : Divisi Semen Tonasa Medical Centre - Jadwal MCU, Pasien, Dokter, Rekam Medis.
     * 5. User          : Karyawan Umum PT Semen Tonasa - Akses portal Beranda, usulan KMST & MCU pribadi.
     */
    public function run(): void
    {
        // Bersihkan cache Spatie Permission agar perubahan langsung terbaca
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat / Pastikan 5 Role Utama Ada
        $roleDeveloper  = Role::firstOrCreate(['name' => 'Developer']);
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleAdminBusdev= Role::firstOrCreate(['name' => 'Admin Busdev']);
        $roleAdminStmc  = Role::firstOrCreate(['name' => 'Admin STMC']);
        $roleUser       = Role::firstOrCreate(['name' => 'User']);

        // 2. Migrasi Pengguna dari Role Lama ke Role Baru jika ada
        $oldAdminRole = Role::where('name', 'Admin')->first();
        if ($oldAdminRole) {
            $usersWithOldAdmin = User::role('Admin')->get();
            foreach ($usersWithOldAdmin as $user) {
                $user->assignRole('Admin Busdev');
                $user->removeRole('Admin');
            }
            $oldAdminRole->delete();
        }

        $oldDokterRole = Role::where('name', 'Dokter STMC')->first();
        if ($oldDokterRole) {
            $usersWithOldDokter = User::role('Dokter STMC')->get();
            foreach ($usersWithOldDokter as $user) {
                $user->assignRole('Admin STMC');
                $user->removeRole('Dokter STMC');
            }
            $oldDokterRole->delete();
        }

        // 3. Pastikan Akun Developer (Andi Batari) Terpasang Role Developer
        $developerUser = User::where('email', 'andibatari2311@gmail.com')->first();
        if ($developerUser && !$developerUser->hasRole('Developer')) {
            $developerUser->assignRole('Developer');
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
