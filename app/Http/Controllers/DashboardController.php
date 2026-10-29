<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\IcofrControl;
use App\Models\IcofrCycle;
use App\Models\KmstRegistration;
use App\Models\Setting;
use App\Models\SysdevAuditFinding;
use App\Models\SysdevCertificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan beranda cerdas berdasarkan role pengguna (Karyawan Umum vs Staf HC & GRC).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->loadMissing(['unit.department', 'roles']);

        $isStaff = $user->hasAnyRole(['Developer', 'Super Admin', 'Admin Busdev', 'Admin STMC']);

        // Default view: Staf HC melihat 'staff', Karyawan Umum selalu melihat 'employee'
        // Staf juga dapat melihat preview tampilan karyawan dengan parameter ?view=employee
        $viewMode = $request->query('view', $isStaff ? 'staff' : 'employee');
        if (! $isStaff) {
            $viewMode = 'employee';
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

        // 3. Statistik khusus untuk Staf Internal Dept. HC & GRC
        $staffStats = [];
        if ($isStaff) {
            $staffStats = [
                'total_users'     => User::count(),
                'total_controls'  => IcofrControl::count(),
                'total_cycles'    => IcofrCycle::count(),
                'total_audits'    => SysdevAuditFinding::count(),
                'total_certs'     => SysdevCertificate::count(),
                'total_kmst'      => KmstRegistration::count(),
                'kmst_ss_count'   => KmstRegistration::where('kategori', 'like', 'SS%')->count(),
                'kmst_gkm_count'  => KmstRegistration::where('kategori', 'GKM')->count(),
                'kmst_pkm_count'  => KmstRegistration::where('kategori', 'PKM')->count(),
                'kmst_bi_count'   => KmstRegistration::where('kategori', 'BI')->count(),
                'total_depts'     => Department::count(),
            ];
        }

        return view('dashboard', [
            'user'                 => $user,
            'isStaff'              => $isStaff,
            'viewMode'             => $viewMode,
            'isKmstActive'         => $isKmstActive,
            'userKmstRegistration' => $userKmstRegistration,
            'staffStats'           => $staffStats,
        ]);
    }
}

