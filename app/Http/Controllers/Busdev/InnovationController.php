<?php

namespace App\Http\Controllers\Busdev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KmstRegistration;
use App\Models\Setting;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class InnovationController extends Controller
{
    public function portal()
    {
        // 1. Cek status portal dari database
        $kmstActive = \App\Models\Setting::where('key', 'kmst_event_active')->value('value') === '1';

        // 2. Jika portal sedang dimatikan (Off), usir pengguna!
        if (!$kmstActive) {
            // Bisa menggunakan abort(403) untuk halaman error kaku, tapi redirect lebih elegan:
            return redirect()->route('dashboard')->with('error', 'Akses Ditolak: Portal pendaftaran KMST Inovasi saat ini sedang ditutup.');
        }

        $departments = Department::with('units')->orderBy('name_dept')->get();
        $tahapanList = Setting::getKmstTahapan();
        return view('inovasi.portal', compact('departments', 'tahapanList'));
    }

    public function downloadTemplate($kategori)
    {
        $raw = strtolower(trim($kategori));
        
        // Identifikasi target key setting dan fallback
        if (str_contains($raw, 'plant')) {
            $key = 'template_ss_plant';
            $fallback = 'template_ss';
            $displayLabel = 'SS (Plant)';
        } elseif (str_contains($raw, 'office')) {
            $key = 'template_ss_office';
            $fallback = 'template_ss';
            $displayLabel = 'SS (Office)';
        } else {
            $clean = preg_replace('/[^a-z]/', '', $raw);
            $key = "template_{$clean}";
            $fallback = null;
            $displayLabel = strtoupper($clean);
        }

        // Cari file template dari database
        $templatePath = \App\Models\Setting::where('key', $key)->value('value');
        if ((!$templatePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($templatePath)) && $fallback) {
            $templatePath = \App\Models\Setting::where('key', $fallback)->value('value');
        }

        if (!$templatePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($templatePath)) {
            return back()->with('error', "Maaf, Admin belum mengunggah template resmi untuk kategori {$displayLabel} ini.");
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($templatePath);
    }

    // JANGAN LUPA tambahkan Request di parameternya
    public function adminIndex(Request $request) 
    {
        $kmstActive = Setting::where('key', 'kmst_event_active')->value('value') === '1';

        // 1. STATISTIK (Mendukung 5 Kategori Resmi KMST: SS Office, SS Plant, GKM, PKM, BI)
        $totalStats = KmstRegistration::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN kategori = "SS (Office)" OR kategori = "SS_Office" OR (kategori = "SS" AND data_peserta LIKE "%Office%") THEN 1 ELSE 0 END) as ss_office,
            SUM(CASE WHEN kategori = "SS (Plant)" OR kategori = "SS_Plant" OR (kategori = "SS" AND data_peserta NOT LIKE "%Office%") THEN 1 ELSE 0 END) as ss_plant,
            SUM(CASE WHEN kategori = "SS" OR kategori LIKE "SS%" THEN 1 ELSE 0 END) as ss_total,
            SUM(CASE WHEN kategori = "GKM" THEN 1 ELSE 0 END) as gkm,
            SUM(CASE WHEN kategori = "PKM" THEN 1 ELSE 0 END) as pkm,
            SUM(CASE WHEN kategori = "BI" OR kategori LIKE "%Breakthrough%" THEN 1 ELSE 0 END) as bi
        ')->first();

        $ssOffice = (int) ($totalStats->ss_office ?? 0);
        $ssPlant  = (int) ($totalStats->ss_plant ?? 0);
        $ssTotal  = (int) ($totalStats->ss_total ?? 0);
        if ($ssOffice === 0 && $ssPlant === 0 && $ssTotal > 0) {
            $ssOffice = $ssTotal;
        }

        $stats = [
            'total'     => (int) ($totalStats->total ?? 0),
            'ss_office' => $ssOffice,
            'ss_plant'  => $ssPlant,
            'ss_total'  => $ssTotal,
            'gkm'       => (int) ($totalStats->gkm ?? 0),
            'pkm'       => (int) ($totalStats->pkm ?? 0),
            'bi'        => (int) ($totalStats->bi ?? 0),
        ];

        // 2. DATA TREN GRAFIK MULTI-TAHUN (100% SINKRON DENGAN 5 KATEGORI RESMI)
        $trendRaw = \Illuminate\Support\Facades\DB::table('kmst_registrations')
            ->selectRaw('
                YEAR(created_at) as tahun,
                CASE 
                    WHEN kategori IN ("SS (Office)", "SS_Office") OR (kategori = "SS" AND data_peserta LIKE "%Office%") THEN "SS (Office)"
                    WHEN kategori IN ("SS (Plant)", "SS_Plant") OR (kategori = "SS" AND data_peserta NOT LIKE "%Office%") THEN "SS (Plant)"
                    WHEN kategori = "GKM" THEN "GKM"
                    WHEN kategori = "PKM" THEN "PKM"
                    WHEN kategori = "BI" OR kategori LIKE "%Breakthrough%" THEN "BI"
                    ELSE "Lainnya"
                END as kategori_normal,
                COUNT(*) as total
            ')
            ->groupBy('tahun', 'kategori_normal')
            ->orderBy('tahun', 'asc')
            ->get();

        $labelsTahun = $trendRaw->pluck('tahun')->unique()->values()->toArray();
        if (empty($labelsTahun)) $labelsTahun = [(int) date('Y')];

        $dataSSOffice = []; $dataSSPlant = []; $dataGKM = []; $dataPKM = []; $dataBI = [];
        foreach ($labelsTahun as $thn) {
            $dataSSOffice[] = (int) $trendRaw->where('tahun', $thn)->where('kategori_normal', 'SS (Office)')->sum('total');
            $dataSSPlant[]  = (int) $trendRaw->where('tahun', $thn)->where('kategori_normal', 'SS (Plant)')->sum('total');
            $dataGKM[]      = (int) $trendRaw->where('tahun', $thn)->where('kategori_normal', 'GKM')->sum('total');
            $dataPKM[]      = (int) $trendRaw->where('tahun', $thn)->where('kategori_normal', 'PKM')->sum('total');
            $dataBI[]       = (int) $trendRaw->where('tahun', $thn)->where('kategori_normal', 'BI')->sum('total');
        }

        $trendChart = [
            'labels'    => $labelsTahun,
            'ss_office' => $dataSSOffice,
            'ss_plant'  => $dataSSPlant,
            'gkm'       => $dataGKM,
            'pkm'       => $dataPKM,
            'bi'        => $dataBI,
        ];

        // --- AMBIL DAFTAR TAHUN UNTUK DROPDOWN ---
        $availableYears = KmstRegistration::selectRaw('YEAR(created_at) as tahun')
            ->distinct()->pluck('tahun')->sortDesc();

        // 3. FITUR PENCARIAN & PAGINASI (Server-Side)
        $query = KmstRegistration::with(['user.unit', 'user.roles']);

        $currentTab  = $request->query('tab', 'monitor');
        $activeTab   = $request->query('kategori', 'Semua');
        $activeTahun = $request->query('tahun', 'Semua');
        $activeSort  = $request->query('sort', 'terbaru');

        // Filter Tab Kategori (Mendukung 5 Kategori Resmi)
        if ($activeTab !== 'Semua') {
            if ($activeTab === 'SS (Office)' || $activeTab === 'SS_Office') {
                $query->where(function($q) {
                    $q->whereIn('kategori', ['SS (Office)', 'SS_Office'])
                      ->orWhere(function($sq) {
                          $sq->where('kategori', 'SS')->where('data_peserta', 'like', '%Office%');
                      });
                });
            } elseif ($activeTab === 'SS (Plant)' || $activeTab === 'SS_Plant') {
                $query->where(function($q) {
                    $q->whereIn('kategori', ['SS (Plant)', 'SS_Plant'])
                      ->orWhere(function($sq) {
                          $sq->where('kategori', 'SS')->where('data_peserta', 'not like', '%Office%');
                      });
                });
            } else {
                $query->where('kategori', $activeTab);
            }
        }

        // Filter Tahun
        if ($activeTahun !== 'Semua') {
            $query->whereYear('created_at', $activeTahun);
        }

        // Filter Pencarian Teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_tim', 'like', "%{$search}%")
                    ->orWhere('data_peserta', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        // Filter Urutan
        if ($activeSort === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $registrations = $query->paginate(10)->withQueryString();
        $recentRegistrations = KmstRegistration::with(['user.unit'])->latest()->take(5)->get();
        $tahapanList = Setting::getKmstTahapan();

        return view('busdev.innovation.index', compact(
            'registrations', 
            'recentRegistrations',
            'stats', 
            'kmstActive', 
            'trendChart', 
            'currentTab',
            'activeTab', 
            'activeTahun', 
            'activeSort', 
            'availableYears', 
            'tahapanList'
        ));
    }

    public function exportExcel(Request $request)
    {
        $query = KmstRegistration::with('user');
        if ($request->filled('kategori') && $request->kategori !== 'Semua') {
            if ($request->kategori === 'SS (Office)' || $request->kategori === 'SS_Office') {
                $query->where(function($q) {
                    $q->whereIn('kategori', ['SS (Office)', 'SS_Office'])
                      ->orWhere(function($sq) {
                          $sq->where('kategori', 'SS')->where('data_peserta', 'like', '%Office%');
                      });
                });
            } elseif ($request->kategori === 'SS (Plant)' || $request->kategori === 'SS_Plant') {
                $query->where(function($q) {
                    $q->whereIn('kategori', ['SS (Plant)', 'SS_Plant'])
                      ->orWhere(function($sq) {
                          $sq->where('kategori', 'SS')->where('data_peserta', 'not like', '%Office%');
                      });
                });
            } else {
                $query->where('kategori', $request->kategori);
            }
        }
        if ($request->filled('tahun') && $request->tahun !== 'Semua') {
            $query->whereYear('created_at', $request->tahun);
        }

        $data = $query->orderBy('created_at', 'desc')->get();
        $tahunLabel = ($request->filled('tahun') && $request->tahun !== 'Semua') ? '_Tahun_' . $request->tahun : '_Semua_Tahun';
        $kategoriLabel = ($request->filled('kategori') && $request->kategori !== 'Semua') ? '_' . preg_replace('/[^A-Za-z0-9]/', '', $request->kategori) : '';
        $filename = 'Data_Pendaftar_KMST' . $tahunLabel . $kategoriLabel . '_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $output = fopen('php://output', 'w');
            // Menulis UTF-8 BOM agar Excel langsung membaca karakter dan huruf dengan benar
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Hitung batas dinamis anggota dari Pengaturan Sistem (Aturan Form Registrasi)
            // Default 1 tim GKM/PKM/BI = 5 orang (Fasilitator [1] + Ketua [1] + Sekretaris [1] + Anggota Tambahan [2])
            $settingMaxGkm = (int) (\App\Models\Setting::where('key', 'max_anggota_gkm')->value('value') ?? 5);
            $dynamicAnggotaLimit = max(1, $settingMaxGkm - 3);

            // Periksa jika ada pendaftar yang memiliki jumlah anggota tambahan lebih banyak
            $maxAnggotaInData = 0;
            $parsedPesertaPerReg = [];

            foreach ($data as $reg) {
                // Parsing Peserta (Mengatasi single-encoded, double-encoded JSON, atau array)
                $rawPeserta = $reg->data_peserta;
                if (is_string($rawPeserta)) {
                    $decoded = json_decode($rawPeserta, true);
                    if (is_string($decoded)) {
                        $decoded = json_decode($decoded, true);
                    }
                    $peserta = is_array($decoded) ? $decoded : [];
                } elseif (is_array($rawPeserta)) {
                    $peserta = $rawPeserta;
                } else {
                    $peserta = [];
                }

                // Ekstrak anggota tambahan (di luar peran inti: ketua, fasilitator, sekretaris)
                $extraAnggota = [];
                foreach ($peserta as $roleKey => $item) {
                    if (!in_array(strtolower($roleKey), ['ketua', 'fasilitator', 'sekretaris']) && is_array($item) && !empty($item['nama'])) {
                        $extraAnggota[] = $item;
                    }
                }

                $parsedPesertaPerReg[$reg->id] = [
                    'peserta'      => $peserta,
                    'extraAnggota' => $extraAnggota,
                ];

                if (count($extraAnggota) > $maxAnggotaInData) {
                    $maxAnggotaInData = count($extraAnggota);
                }
            }

            // Jumlah kolom anggota yang ditampilkan di Excel:
            // Sesuai batas setting form (2 anggota untuk tim 5 orang) dan bertambah otomatis jika setting dinaikkan
            $totalAnggotaCols = max($dynamicAnggotaLimit, $maxAnggotaInData);

            // Header Kolom Lengkap (Hanya Ketua yang memiliki kolom No HP / WA sesuai inputan form)
            $headers = [
                'No', 'ID Registrasi', 'Tanggal & Waktu Daftar', 'Tahun', 'Kategori', 'Nama Tim Inovasi', 
                'Nama Akun Pengirim', 'Email Akun Pengirim', 'Total Personel', 'Susunan Personel Lengkap',
                'Ketua - Nama', 'Ketua - No SAP', 'Ketua - Departemen', 'Ketua - Unit Kerja', 'Ketua - No HP / WA',
                'Fasilitator - Nama', 'Fasilitator - No SAP', 'Fasilitator - Departemen', 'Fasilitator - Unit Kerja',
                'Sekretaris - Nama', 'Sekretaris - No SAP', 'Sekretaris - Departemen', 'Sekretaris - Unit Kerja',
            ];

            for ($i = 1; $i <= $totalAnggotaCols; $i++) {
                $headers[] = "Anggota {$i} - Nama";
                $headers[] = "Anggota {$i} - No SAP";
                $headers[] = "Anggota {$i} - Departemen";
                $headers[] = "Anggota {$i} - Unit Kerja";
            }

            $headers[] = 'Bidang Manfaat (Benefit)';
            $headers[] = 'Nama Berkas Makalah Resmi';
            $headers[] = 'Status Berkas Makalah';

            fputcsv($output, $headers);

            $no = 1;
            foreach ($data as $reg) {
                $parsed = $parsedPesertaPerReg[$reg->id] ?? ['peserta' => [], 'extraAnggota' => []];
                $peserta = $parsed['peserta'];
                $extraAnggota = $parsed['extraAnggota'];

                // Parsing Benefit (Mengatasi array atau JSON string)
                $rawBenefit = $reg->bidang_benefit;
                if (is_string($rawBenefit)) {
                    $decodedBenefit = json_decode($rawBenefit, true);
                    $benefits = is_array($decodedBenefit) ? implode(', ', $decodedBenefit) : $rawBenefit;
                } elseif (is_array($rawBenefit)) {
                    $benefits = implode(', ', $rawBenefit);
                } else {
                    $benefits = '-';
                }

                // Helper untuk mengekstrak data per role (dengan format text formula ="..." agar 0 di depan SAP/HP tidak hilang di Excel)
                $extractMember = function($item, $includeHp = false) {
                    if (!$item || !is_array($item) || empty($item['nama'])) {
                        return $includeHp ? ['-', '-', '-', '-', '-'] : ['-', '-', '-', '-'];
                    }
                    $res = [
                        $item['nama'] ?? '-',
                        !empty($item['sap']) ? '="' . $item['sap'] . '"' : '-',
                        $item['dept'] ?? '-',
                        $item['uk'] ?? '-',
                    ];
                    if ($includeHp) {
                        $res[] = !empty($item['hp']) ? '="' . $item['hp'] . '"' : '-';
                    }
                    return $res;
                };

                // Susunan personel lengkap dalam 1 string ringkasan
                $summaryList = [];
                foreach ($peserta as $k => $val) {
                    if (is_array($val) && !empty($val['nama'])) {
                        $roleTitle = ucfirst($k);
                        $summaryList[] = "[{$roleTitle}] {$val['nama']} (SAP: " . ($val['sap'] ?? '-') . " - " . ($val['dept'] ?? '-') . ")";
                    }
                }
                $summaryString = !empty($summaryList) ? implode('; ', $summaryList) : '-';
                $totalPersonel = count($summaryList);

                // Nama File Makalah Resmi Sesuai Format: Kategori_Nama Tim_Tahun.ext
                $extension = strtolower(pathinfo($reg->file_makalah ?? '', PATHINFO_EXTENSION) ?: 'docx');
                $kategoriClean = str_replace(['/', '\\'], '-', $reg->kategori);
                $namaTimClean  = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $reg->nama_tim);
                $tahun         = $reg->created_at ? $reg->created_at->format('Y') : date('Y');
                $formattedFilename = "{$kategoriClean}_{$namaTimClean}_{$tahun}.{$extension}";

                // Gabungkan baris data umum
                $row = [
                    $no++,
                    $reg->id,
                    $reg->created_at ? $reg->created_at->format('Y-m-d H:i') : '-',
                    $tahun,
                    $reg->kategori,
                    $reg->nama_tim,
                    $reg->user?->name ?? 'User #' . $reg->user_id,
                    $reg->user?->email ?? '-',
                    $totalPersonel,
                    $summaryString,
                ];

                // Ketua (Termasuk No HP / WA)
                $row = array_merge($row, $extractMember($peserta['ketua'] ?? null, true));

                // Fasilitator (Tanpa No HP / WA)
                $row = array_merge($row, $extractMember($peserta['fasilitator'] ?? null, false));

                // Sekretaris (Tanpa No HP / WA)
                $row = array_merge($row, $extractMember($peserta['sekretaris'] ?? null, false));

                // Anggota Tambahan (Dinamis: Anggota 1, Anggota 2, dst - Tanpa No HP / WA)
                for ($i = 0; $i < $totalAnggotaCols; $i++) {
                    $anggotaItem = $extraAnggota[$i] ?? ($peserta['anggota' . ($i + 1)] ?? null);
                    $row = array_merge($row, $extractMember($anggotaItem, false));
                }

                // Benefit dan Makalah
                $row[] = $benefits;
                $row[] = $reg->file_makalah ? $formattedFilename : '-';
                $row[] = $reg->file_makalah ? 'Tersedia' : 'Belum Diunggah';

                fputcsv($output, $row);
            }
            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Membuka pratinjau makalah di Tab Chrome Baru dengan nama resmi
     */
    public function previewMakalah($id)
    {
        $reg = KmstRegistration::with('user')->findOrFail($id);

        if (!$reg->file_makalah || !\Illuminate\Support\Facades\Storage::disk('public')->exists($reg->file_makalah)) {
            abort(404, 'Berkas makalah untuk tim ' . $reg->nama_tim . ' belum diunggah atau tidak ditemukan.');
        }

        $extension = strtolower(pathinfo($reg->file_makalah, PATHINFO_EXTENSION) ?: 'docx');
        $kategoriClean = str_replace(['/', '\\'], '-', $reg->kategori);
        $namaTimClean  = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $reg->nama_tim);
        $tahun         = $reg->created_at ? $reg->created_at->format('Y') : date('Y');
        $formattedFilename = "{$kategoriClean}_{$namaTimClean}_{$tahun}.{$extension}";

        $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($reg->file_makalah);

        // Jika file adalah PDF, buka langsung di tab browser Chrome
        if ($extension === 'pdf') {
            return response()->file($fullPath, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $formattedFilename . '"',
            ]);
        }

        // Jika file adalah Word (.docx / .doc), render halaman Pratinjau Dokumen Eksekutif di Tab Baru
        $fileSize = file_exists($fullPath) ? round(filesize($fullPath) / (1024 * 1024), 2) . ' MB' : '-';
        return view('busdev.innovation.makalah_preview', compact('reg', 'formattedFilename', 'fileSize', 'extension'));
    }

    /**
     * Mengunduh berkas makalah dengan penamaan resmi: Kategori_Nama Tim_Tahun.ext
     */
    public function downloadMakalah($id)
    {
        $reg = KmstRegistration::findOrFail($id);

        if (!$reg->file_makalah || !\Illuminate\Support\Facades\Storage::disk('public')->exists($reg->file_makalah)) {
            return back()->with('error', 'Berkas makalah tidak ditemukan.');
        }

        $extension = strtolower(pathinfo($reg->file_makalah, PATHINFO_EXTENSION) ?: 'docx');
        $kategoriClean = str_replace(['/', '\\'], '-', $reg->kategori);
        $namaTimClean  = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $reg->nama_tim);
        $tahun         = $reg->created_at ? $reg->created_at->format('Y') : date('Y');
        $formattedFilename = "{$kategoriClean}_{$namaTimClean}_{$tahun}.{$extension}";

        return \Illuminate\Support\Facades\Storage::disk('public')->download($reg->file_makalah, $formattedFilename);
    }

    // Pastikan Anda juga mengunci fungsi POST ini agar tidak ada "hacker" iseng
    // yang mengirim data menggunakan Postman saat portal sedang ditutup.
    public function store(Request $request)
    {
        // Cek status portal sebelum memproses data
        $kmstActive = \App\Models\Setting::where('key', 'kmst_event_active')->value('value') === '1';
        
        if (!$kmstActive) {
            return redirect()->route('dashboard')->with('error', 'Gagal mengirim makalah: Portal pendaftaran KMST Inovasi saat ini sedang ditutup.');
        }

        $request->validate([
            'kategori'       => ['required', 'string', 'in:SS,SS (Office),SS (Plant),SS_Office,SS_Plant,GKM,PKM,BI'],
            'nama_tim'       => 'required|string|max:255',
            'peserta'        => 'required|array', 
            'bidang_benefit' => 'required|array', // Diubah menjadi wajib
            'file_makalah'   => 'required|mimes:pdf,doc,docx|max:30720', // Maks 30MB
        ]);

        // Menyaring dan merapikan data peserta (Mengganti pilihan 'Lainnya' dengan teks asli)
        $pesertaFinal = [];
        foreach ($request->peserta as $role => $p) {
            // Abaikan jika data anggota kosong (karena tombol tambah anggota tidak diklik)
            if (empty($p['nama'])) continue;

            $dept = ($p['dept'] === 'Lainnya') ? ($p['dept_lainnya'] ?? 'Lainnya') : $p['dept'];
            $unit = ($p['unit'] === 'Lainnya') ? ($p['unit_lainnya'] ?? 'Lainnya') : $p['unit'];

            $pesertaFinal[$role] = [
                'nama' => $p['nama'],
                'sap'  => $p['sap'],
                'dept' => $dept,
                'uk'   => $unit,
                'hp'   => $p['hp'] ?? null,
            ];
        }

        $folder = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->kategori);
        $filePath = $request->file('file_makalah')->store('inovasi_makalah/' . $folder, 'public');

        KmstRegistration::create([
            'user_id'        => auth()->id(),
            'kategori'       => $request->kategori,
            'nama_tim'       => $request->nama_tim,
            'data_peserta'   => $pesertaFinal,
            'bidang_benefit' => $request->bidang_benefit,
            'file_makalah'   => $filePath,
        ]);

        return back()->with('success', 'Luar biasa! Makalah inovasi tim Anda berhasil dikirim.');
    }
}