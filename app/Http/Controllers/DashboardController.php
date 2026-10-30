<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Dokter;
use App\Models\IcofrControl;
use App\Models\IcofrCycle;
use App\Models\JadwalMcu;
use App\Models\Karyawan;
use App\Models\KmstRegistration;
use App\Models\PemantauanLingkungan;
use App\Models\PesertaMcu;
use App\Models\Setting;
use App\Models\SysdevAuditFinding;
use App\Models\SysdevCertificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan beranda cerdas berdasarkan role pengguna (Karyawan Umum vs Admin Busdev vs Admin STMC vs Super Admin).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->loadMissing(['unit.department', 'roles']);

        $isDeveloper   = $user->hasRole('Developer');
        $isSuperAdmin  = $user->hasRole('Super Admin');
        $isAdminBusdev = $user->hasRole('Admin Busdev');
        $isAdminStmc   = $user->hasRole('Admin STMC');

        $isStaff = $isDeveloper || $isSuperAdmin || $isAdminBusdev || $isAdminStmc;

        // Default viewMode: Staf melihat 'staff', Karyawan Umum melihat 'employee'
        // Staf juga dapat melihat preview tampilan karyawan dengan parameter ?view=employee
        $viewMode = $request->query('view', $isStaff ? 'staff' : 'employee');
        if (! $isStaff) {
            $viewMode = 'employee';
        }

        // Tentukan sub-departemen dashboard operasional staf:
        // - Admin STMC (murni) => 'stmc'
        // - Admin Busdev (murni) => 'busdev'
        // - Super Admin / Developer => default 'overview', dapat beralih ke 'busdev' atau 'stmc'
        $defaultStaffDept = 'overview';
        if ($isAdminStmc && ! $isAdminBusdev && ! $isSuperAdmin && ! $isDeveloper) {
            $defaultStaffDept = 'stmc';
        } elseif ($isAdminBusdev && ! $isAdminStmc && ! $isSuperAdmin && ! $isDeveloper) {
            $defaultStaffDept = 'busdev';
        }

        $staffDept = $request->query('dept', $defaultStaffDept);

        // Jika staf biasa mencoba akses departemen di luar otorisasinya lewat URL, arahkan ke hak aksesnya
        if ($isAdminStmc && ! $isAdminBusdev && ! $isSuperAdmin && ! $isDeveloper) {
            $staffDept = 'stmc';
        } elseif ($isAdminBusdev && ! $isAdminStmc && ! $isSuperAdmin && ! $isDeveloper) {
            $staffDept = 'busdev';
        }

        // 1. Status Event KMST Inovasi dari tabel settings
        $isKmstActive = Setting::where('key', 'kmst_event_active')->value('value') === '1';

        // 2. Cek apakah pengguna saat ini telah terdaftar di salah satu tim KMST
        $userKmstRegistration = null;
        if ($user) {
            $userCleanName = strtolower(trim($user->name));
            $allRegistrations = KmstRegistration::latest()->get();

            foreach ($allRegistrations as $reg) {
                $peserta = is_array($reg->data_peserta) ? $reg->data_peserta : json_decode($reg->data_peserta, true);
                if (is_array($peserta)) {
                    foreach ($peserta as $roleKey => $member) {
                        if (is_array($member) && isset($member['nama']) && strtolower(trim($member['nama'])) === $userCleanName) {
                            $userKmstRegistration = $reg;
                            break 2;
                        }
                    }
                }
            }
        }

        // 3. Status Kesehatan MCU Karyawan Terkait (untuk Banner Karyawan)
        $karyawan = Karyawan::where('email', $user->email)
            ->orWhere('nama_karyawan', $user->name)
            ->first();

        $userMcu = null;
        if ($karyawan) {
            $userMcu = JadwalMcu::where('karyawan_id', $karyawan->id)
                ->orWhere('no_sap', $karyawan->no_sap)
                ->latest('tanggal_mcu')
                ->first();
        }

        // 4. Statistik khusus untuk Unit Business & System Development (Busdev)
        $busdevStats = [];
        $recentKmstRegistrations = collect();
        if ($isAdminBusdev || $isSuperAdmin || $isDeveloper) {
            $busdevStats = [
                'total_controls' => IcofrControl::count(),
                'total_cycles'   => IcofrCycle::count(),
                'total_audits'   => SysdevAuditFinding::count(),
                'total_certs'    => SysdevCertificate::count(),
                'total_kmst'     => KmstRegistration::count(),
                'kmst_ss_count'  => KmstRegistration::where('kategori', 'like', 'SS%')->count(),
                'kmst_gkm_count' => KmstRegistration::where('kategori', 'GKM')->count(),
                'kmst_pkm_count' => KmstRegistration::where('kategori', 'PKM')->count(),
                'kmst_bi_count'  => KmstRegistration::where('kategori', 'BI')->count(),
                'total_depts'    => Department::count(),
            ];

            $recentKmstRegistrations = KmstRegistration::latest()->take(5)->get();
        }

        // 5. Statistik khusus untuk Unit Semen Tonasa Medical Centre (STMC)
        $stmcStats = [];
        $recentMcuSchedules = collect();
        if ($isAdminStmc || $isSuperAdmin || $isDeveloper) {
            $totalKaryawan = Karyawan::count();
            $totalPeserta  = PesertaMcu::count();
            $stmcStats = [
                'total_karyawan'   => $totalKaryawan,
                'total_pasien'     => $totalKaryawan + $totalPeserta,
                'total_jadwal'     => JadwalMcu::count(),
                'jadwal_aktif'     => JadwalMcu::whereIn('status', ['terjadwal', 'proses'])->count(),
                'jadwal_selesai'   => JadwalMcu::where('status', 'selesai')->count(),
                'total_dokter'     => Dokter::count(),
                'total_pemantauan' => PemantauanLingkungan::count(),
            ];

            $recentMcuSchedules = JadwalMcu::with(['karyawan', 'dokter'])
                ->latest('tanggal_mcu')
                ->take(5)
                ->get();
        }

        // 6. Ringkasan Eksekutif Terpadu (Super Admin / Developer)
        $staffStats = [
            'total_users'     => User::count(),
            'total_controls'  => $busdevStats['total_controls'] ?? IcofrControl::count(),
            'total_kmst'      => $busdevStats['total_kmst'] ?? KmstRegistration::count(),
            'total_audits'    => $busdevStats['total_audits'] ?? SysdevAuditFinding::count(),
            'total_jadwal'    => $stmcStats['total_jadwal'] ?? JadwalMcu::count(),
            'total_pasien'    => $stmcStats['total_pasien'] ?? (Karyawan::count() + PesertaMcu::count()),
            'total_dokter'    => $stmcStats['total_dokter'] ?? Dokter::count(),
        ];

        return view('dashboard', [
            'user'                     => $user,
            'karyawan'                 => $karyawan,
            'userMcu'                  => $userMcu,
            'isStaff'                  => $isStaff,
            'isDeveloper'              => $isDeveloper,
            'isSuperAdmin'             => $isSuperAdmin,
            'isAdminBusdev'            => $isAdminBusdev,
            'isAdminStmc'              => $isAdminStmc,
            'viewMode'                 => $viewMode,
            'staffDept'                => $staffDept,
            'isKmstActive'             => $isKmstActive,
            'userKmstRegistration'     => $userKmstRegistration,
            'busdevStats'              => $busdevStats,
            'stmcStats'                => $stmcStats,
            'staffStats'               => $staffStats,
            'recentMcuSchedules'       => $recentMcuSchedules,
            'recentKmstRegistrations'  => $recentKmstRegistrations,
        ]);
    }
}
