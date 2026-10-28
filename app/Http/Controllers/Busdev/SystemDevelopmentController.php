<?php

namespace App\Http\Controllers\Busdev;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SysdevCertificate;
use App\Models\SysdevAuditFinding;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SystemDevelopmentController extends Controller
{
    /**
     * Tampilkan Dasbor Monitoring Sertifikat & Spreadsheet Data
     */
    public function index(Request $request)
    {
        $certificates = SysdevCertificate::orderBy('id')->get();

        // Rekapitulasi Statistik
        $totalCertificates = $certificates->count();
        $activeCount = $certificates->where('status_validitas', 'Aktif')->count();
        $renewalCount = $certificates->where('status_validitas', 'Renewal')->count();
        $expiredCount = $certificates->where('status_validitas', 'Expired')->count();

        // Hitung Berdasarkan Jenis Sertifikat (Kategori Singkat)
        $jenisCounts = $certificates->groupBy(function ($item) {
            return strtoupper(trim($item->kategori_singkat ?: $item->jenis_sertifikat));
        })->map->count()->sortDesc();

        // Hitung Berdasarkan Lembaga Penerbit
        $lembagaCounts = $certificates->groupBy(function ($item) {
            return trim($item->lembaga_penerbit ?: 'Lainnya');
        })->map->count()->sortDesc();

        // Pengelompokan Tombol File
        $buttonGroups = [
            'FILE SERTIFIKAT ISO INTERGRASI' => [
                'color' => 'bg-blue-600 hover:bg-blue-700 text-white',
                'border' => 'border-blue-500',
                'items' => $certificates->filter(fn($c) => str_contains(strtoupper($c->kategori_grup), 'ISO'))->values()
            ],
            'FILE SERTIFIKAT SNI' => [
                'color' => 'bg-[#a3e635] hover:bg-[#84cc16] text-black font-semibold',
                'border' => 'border-lime-500',
                'items' => $certificates->filter(fn($c) => str_contains(strtoupper($c->kategori_grup), 'SNI'))->values()
            ],
            'FILE SERTIFIKAT TKDN' => [
                'color' => 'bg-red-600 hover:bg-red-700 text-white',
                'border' => 'border-red-500',
                'items' => $certificates->filter(fn($c) => str_contains(strtoupper($c->kategori_grup), 'TKDN'))->values()
            ],
            'FILE SERTIFIKAT GREEN LABEL' => [
                'color' => 'bg-emerald-600 hover:bg-emerald-700 text-white',
                'border' => 'border-emerald-500',
                'items' => $certificates->filter(fn($c) => str_contains(strtoupper($c->kategori_grup), 'GREEN'))->values()
            ],
        ];

        // Daftar unik untuk opsi filter dropdown
        $uniqueNames = $certificates->pluck('jenis_sertifikat')->unique()->filter()->values();
        $uniqueStatuses = ['Renewal', 'Expired', 'Aktif'];
        $uniqueExpiredDates = $certificates->filter(fn($c) => !empty($c->tanggal_expired))
            ->map(fn($c) => Carbon::parse($c->tanggal_expired)->format('M d, Y'))
            ->unique()
            ->values();

        // Google Sheets URL tersimpan di settings
        $sheetsSetting = Setting::where('key', 'sysdev_sheets_url')->first();
        $savedSheetsUrl = $sheetsSetting ? $sheetsSetting->value : '';

        // ==========================================
        // DATA MONITORING TINDAK LANJUT TEMUAN AUDIT
        // ==========================================
        $auditFindings = SysdevAuditFinding::orderBy('no')->get();
        $totalAudit = $auditFindings->count();
        $auditLengkap = $auditFindings->where('status_lengkap', 'Lengkap')->count();
        $auditBelumLengkap = $auditFindings->where('status_lengkap', 'Belum Lengkap')->count();
        $auditMajor = $auditFindings->where('category', 'Major')->count();
        $auditMinor = $auditFindings->where('category', 'Minor')->count();
        $auditObs = $auditFindings->where('category', 'Observation')->count();

        // Breakdown per Kategori untuk Grafik Bar Bertumpuk (Horizontal)
        $auditCategories = ['Major', 'Observation', 'Minor'];
        $auditCategoryStats = [];
        foreach ($auditCategories as $cat) {
            $items = $auditFindings->where('category', $cat);
            $auditCategoryStats[$cat] = [
                'total' => $items->count(),
                'belum_lengkap' => $items->where('status_lengkap', 'Belum Lengkap')->count(),
                'lengkap' => $items->where('status_lengkap', 'Lengkap')->count(),
            ];
        }

        $auditSheetsSetting = Setting::where('key', 'sysdev_audit_sheets_url')->first();
        $savedAuditSheetsUrl = $auditSheetsSetting ? $auditSheetsSetting->value : 'https://docs.google.com/spreadsheets/d/1HKqPpkQGYTtNKi2RLX4YWWZ7l8GEO-KR9z2qYY6boB4/edit?gid=1344160106#gid=1344160106';

        return view('busdev.system-development.index', compact(
            'certificates',
            'totalCertificates',
            'activeCount',
            'renewalCount',
            'expiredCount',
            'jenisCounts',
            'lembagaCounts',
            'buttonGroups',
            'uniqueNames',
            'uniqueStatuses',
            'uniqueExpiredDates',
            'savedSheetsUrl',
            'auditFindings',
            'totalAudit',
            'auditLengkap',
            'auditBelumLengkap',
            'auditMajor',
            'auditMinor',
            'auditObs',
            'auditCategoryStats',
            'savedAuditSheetsUrl'
        ));
    }

    /**
     * Tarik dan Sinkronkan Data dari Google Sheets (CSV Export)
     */
    public function syncFromGoogleSheets(Request $request)
    {
        $request->validate([
            'sheets_url' => ['required', 'url'],
        ]);

        $url = trim($request->sheets_url);

        // Ambil Sheet ID & GID
        preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches);
        if (!isset($matches[1])) {
            return back()->withErrors(['sheets_url' => 'Format tautan Google Sheets tidak valid. Salin tautan dari address bar browser.']);
        }
        $docId = $matches[1];

        $gid = null;
        if (preg_match('/[?&#]gid=([0-9]+)/', $url, $gidMatches)) {
            $gid = $gidMatches[1];
        }

        $csvUrl = "https://docs.google.com/spreadsheets/d/{$docId}/export?format=csv" . ($gid !== null ? "&gid={$gid}" : "");

        try {
            $response = Http::timeout(30)->withoutVerifying()->get($csvUrl);

            if (!$response->successful()) {
                throw new \Exception("Akses ke Google Sheets gagal (Kode HTTP: " . $response->status() . "). Pastikan akses spreadsheet diatur ke 'Anyone with the link' (Siapa saja yang memiliki link).");
            }

            $csvData = $response->body();
            if (str_contains($csvData, '<html') && str_contains(strtolower($csvData), 'sign in')) {
                throw new \Exception("Akses ditolak oleh Google. Harap pastikan Share Settings Google Sheets sudah disetel ke 'Anyone with the link can view'.");
            }

            // Simpan URL sebagai konfigurasi tersimpan
            Setting::updateOrCreate(
                ['key' => 'sysdev_sheets_url'],
                [
                    'value' => $url,
                    'description' => 'Tautan Google Spreadsheet untuk Sinkronisasi Sub Menu System Development'
                ]
            );

            $stream = fopen('php://memory', 'r+');
            fwrite($stream, $csvData);
            rewind($stream);

            $rows = [];
            while (($row = fgetcsv($stream)) !== false) {
                $rows[] = $row;
            }
            fclose($stream);

            $result = $this->processSpreadsheetRows($rows);

            return back()->with('success', "Sinkronisasi Google Sheets berhasil! {$result['synced']} sertifikat diperbarui/ditambahkan.")->with('active_module', 'certificates');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['sheets_url' => 'Gagal sinkronisasi spreadsheet: ' . $e->getMessage()])->with('active_module', 'certificates');
        }
    }

    /**
     * Import File Excel (.xlsx, .xls, .csv)
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt'],
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, true, false);

            $result = $this->processSpreadsheetRows($rows);

            return back()->with('success', "Import file Excel berhasil! {$result['synced']} sertifikat diperbarui/ditambahkan.");

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['excel_file' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
        }
    }

    /**
     * Download Template atau Ekspor Data Sertifikat ke Excel (.xlsx)
     */
    public function exportExcel()
    {
        $certificates = SysdevCertificate::orderBy('id')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Monitoring Sertifikat');

        // Headers
        $headers = [
            'Kode',
            'Jenis Sertifikat',
            'Nama :',
            'Nomor Sertifikat',
            'Nama Produk / Layanan',
            'Lembaga Penerbit',
            'Tanggal Terbit',
            'Tanggal Expired',
            'Link Sertifikat',
            'PIC email',
            'Hari Tersisa',
            'Status Validitas',
            'Notifikasi'
        ];

        $sheet->fromArray($headers, null, 'A1');

        $rows = [];
        foreach ($certificates as $cert) {
            $rows[] = [
                $cert->kode,
                $cert->jenis_sertifikat,
                $cert->kategori_singkat,
                $cert->nomor_sertifikat,
                $cert->nama_produk,
                $cert->lembaga_penerbit,
                $cert->tanggal_terbit ? Carbon::parse($cert->tanggal_terbit)->format('d M Y') : '',
                $cert->tanggal_expired ? Carbon::parse($cert->tanggal_expired)->format('d M Y') : '',
                $cert->link_sertifikat,
                $cert->pic_email,
                $cert->hari_tersisa,
                $cert->status_validitas,
                $cert->notifikasi ? 'TRUE' : 'FALSE',
            ];
        }

        if (!empty($rows)) {
            $sheet->fromArray($rows, null, 'A2');
        }

        // Auto-size columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Styling Header
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        $fileName = 'Monitoring_Sertifikat_' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Tambah Sertifikat Manual
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:sysdev_certificates,kode'],
            'jenis_sertifikat' => ['required', 'string', 'max:255'],
            'kategori_singkat' => ['nullable', 'string', 'max:50'],
            'kategori_grup' => ['nullable', 'string', 'max:50'],
            'nomor_sertifikat' => ['nullable', 'string', 'max:100'],
            'nama_produk' => ['nullable', 'string', 'max:255'],
            'lembaga_penerbit' => ['nullable', 'string', 'max:100'],
            'tanggal_terbit' => ['nullable', 'date'],
            'tanggal_expired' => ['nullable', 'date'],
            'link_sertifikat' => ['nullable', 'url'],
            'pic_email' => ['nullable', 'email'],
            'status_validitas' => ['required', 'in:Aktif,Renewal,Expired'],
            'notifikasi' => ['nullable', 'boolean'],
        ]);

        if (empty($data['kategori_grup'])) {
            $data['kategori_grup'] = SysdevCertificate::detectCategoryGroup(
                $data['kategori_singkat'] ?? null,
                $data['jenis_sertifikat']
            );
        }

        $data['notifikasi'] = $request->has('notifikasi');

        SysdevCertificate::create($data);

        return back()->with('success', 'Sertifikat baru berhasil ditambahkan!');
    }

    /**
     * Perbarui Sertifikat Manual
     */
    public function update(Request $request, $id)
    {
        $cert = SysdevCertificate::findOrFail($id);

        $data = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:sysdev_certificates,kode,' . $id],
            'jenis_sertifikat' => ['required', 'string', 'max:255'],
            'kategori_singkat' => ['nullable', 'string', 'max:50'],
            'kategori_grup' => ['nullable', 'string', 'max:50'],
            'nomor_sertifikat' => ['nullable', 'string', 'max:100'],
            'nama_produk' => ['nullable', 'string', 'max:255'],
            'lembaga_penerbit' => ['nullable', 'string', 'max:100'],
            'tanggal_terbit' => ['nullable', 'date'],
            'tanggal_expired' => ['nullable', 'date'],
            'link_sertifikat' => ['nullable', 'url'],
            'pic_email' => ['nullable', 'email'],
            'status_validitas' => ['required', 'in:Aktif,Renewal,Expired'],
            'notifikasi' => ['nullable', 'boolean'],
        ]);

        if (empty($data['kategori_grup'])) {
            $data['kategori_grup'] = SysdevCertificate::detectCategoryGroup(
                $data['kategori_singkat'] ?? null,
                $data['jenis_sertifikat']
            );
        }

        $data['notifikasi'] = $request->has('notifikasi');

        $cert->update($data);

        return back()->with('success', 'Data sertifikat berhasil diperbarui!');
    }

    /**
     * Hapus Sertifikat
     */
    public function destroy($id)
    {
        SysdevCertificate::findOrFail($id)->delete();
        return back()->with('success', 'Sertifikat berhasil dihapus.');
    }

    /**
     * Helper Parser Spreadsheet Baris-ke-Baris
     */
    private function processSpreadsheetRows(array $rows): array
    {
        if (empty($rows)) {
            throw new \Exception("File spreadsheet kosong atau tidak terbaca.");
        }

        // Cari Baris Header
        $headerRowIndex = -1;
        $maxScan = min(15, count($rows));

        for ($i = 0; $i < $maxScan; $i++) {
            $rowStr = strtolower(implode(' ', array_filter($rows[$i] ?? [])));
            if (str_contains($rowStr, 'kode') || str_contains($rowStr, 'jenis sertifikat') || str_contains($rowStr, 'nomor sertifikat')) {
                $headerRowIndex = $i;
                break;
            }
        }

        if ($headerRowIndex === -1) {
            throw new \Exception("Kolom header 'Kode' atau 'Jenis Sertifikat' tidak ditemukan. Harap gunakan format tabel yang sesuai.");
        }

        $headers = array_map(fn($v) => strtolower(trim((string)$v)), $rows[$headerRowIndex]);

        // Helper cari indeks kolom
        $findCol = function ($keywords) use ($headers) {
            foreach ($headers as $idx => $h) {
                foreach ((array)$keywords as $kw) {
                    if (str_contains($h, strtolower($kw))) {
                        return $idx;
                    }
                }
            }
            return false;
        };

        $idxKode = $findCol(['kode']);
        $idxJenis = $findCol(['jenis sertifikat', 'jenis']);
        $idxKat = $findCol(['nama :', 'nama', 'kategori']);
        $idxNomor = $findCol(['nomor sertifikat', 'no sertifikat', 'nomor']);
        $idxProduk = $findCol(['nama produk', 'produk', 'layanan']);
        $idxPenerbit = $findCol(['lembaga penerbit', 'lembaga', 'penerbit']);
        $idxTerbit = $findCol(['tanggal terbit', 'tgl terbit']);
        $idxExpired = $findCol(['tanggal expired', 'tgl expired', 'expired']);
        $idxLink = $findCol(['link sertifikat', 'link', 'url']);
        $idxPic = $findCol(['pic email', 'pic', 'email']);
        $idxHari = $findCol(['hari tersisa', 'sisa hari', 'hari']);
        $idxStatus = $findCol(['status validitas', 'status']);
        $idxNotif = $findCol(['notifikasi', 'notif']);

        if ($idxKode === false && $idxJenis === false) {
            throw new \Exception("Gagal mengenali struktur kolom. Pastikan ada kolom 'Kode' atau 'Jenis Sertifikat'.");
        }

        $syncedCount = 0;

        DB::beginTransaction();
        try {
            for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row)) continue;

                $kode = $idxKode !== false ? trim((string)($row[$idxKode] ?? '')) : '';
                $jenis = $idxJenis !== false ? trim((string)($row[$idxJenis] ?? '')) : '';

                if (empty($kode) && empty($jenis)) {
                    continue;
                }

                // Jika kode kosong, buat fallback kode
                if (empty($kode)) {
                    $kode = 'S' . str_pad($i, 2, '0', STR_PAD_LEFT);
                }

                $kategoriSingkat = $idxKat !== false ? trim((string)($row[$idxKat] ?? '')) : null;
                $nomor = $idxNomor !== false ? trim((string)($row[$idxNomor] ?? '')) : null;
                $produk = $idxProduk !== false ? trim((string)($row[$idxProduk] ?? '')) : null;
                $penerbit = $idxPenerbit !== false ? trim((string)($row[$idxPenerbit] ?? '')) : null;
                $rawTerbit = $idxTerbit !== false ? trim((string)($row[$idxTerbit] ?? '')) : null;
                $rawExpired = $idxExpired !== false ? trim((string)($row[$idxExpired] ?? '')) : null;
                $link = $idxLink !== false ? trim((string)($row[$idxLink] ?? '')) : null;
                $pic = $idxPic !== false ? trim((string)($row[$idxPic] ?? '')) : null;
                $rawHari = $idxHari !== false ? trim((string)($row[$idxHari] ?? '')) : null;
                $rawStatus = $idxStatus !== false ? trim((string)($row[$idxStatus] ?? '')) : null;
                $rawNotif = $idxNotif !== false ? trim((string)($row[$idxNotif] ?? '')) : null;

                // Parsing Tanggal
                $tglTerbit = $this->parseDateSafely($rawTerbit);
                $tglExpired = $this->parseDateSafely($rawExpired);

                // Parsing Sisa Hari
                $hariTersisaManual = null;
                if ($rawHari !== '' && $rawHari !== null && is_numeric($rawHari)) {
                    $hariTersisaManual = (int)$rawHari;
                }

                // Parsing Status Validitas
                $status = 'Aktif';
                $cleanStatus = strtolower(preg_replace('/[^a-z]/', '', (string)$rawStatus));
                if (str_contains($cleanStatus, 'expired') || ($hariTersisaManual !== null && $hariTersisaManual < 0)) {
                    $status = 'Expired';
                } elseif (str_contains($cleanStatus, 'renewal') || str_contains($cleanStatus, 'renew') || ($hariTersisaManual !== null && $hariTersisaManual >= 0 && $hariTersisaManual <= 60)) {
                    $status = 'Renewal';
                }

                // Parsing Notifikasi
                $notifikasi = true;
                if (!empty($rawNotif)) {
                    $notifClean = strtolower($rawNotif);
                    if ($notifClean === 'false' || $notifClean === '0' || $notifClean === 'no') {
                        $notifikasi = false;
                    }
                }

                // Deteksi Grup
                $grup = SysdevCertificate::detectCategoryGroup($kategoriSingkat, $jenis);

                SysdevCertificate::updateOrCreate(
                    ['kode' => $kode],
                    [
                        'kategori_grup' => $grup,
                        'kategori_singkat' => $kategoriSingkat,
                        'jenis_sertifikat' => $jenis ?: $kode,
                        'nomor_sertifikat' => $nomor,
                        'nama_produk' => $produk,
                        'lembaga_penerbit' => $penerbit,
                        'tanggal_terbit' => $tglTerbit,
                        'tanggal_expired' => $tglExpired,
                        'link_sertifikat' => $link,
                        'pic_email' => $pic,
                        'hari_tersisa_manual' => $hariTersisaManual,
                        'status_validitas' => $status,
                        'notifikasi' => $notifikasi,
                    ]
                );

                $syncedCount++;
            }

            if (DB::transactionLevel() > 0) {
                DB::commit();
            }
            return ['synced' => $syncedCount];

        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Parsing tanggal yang toleran terhadap format spreadsheet (e.g. 13 Sep 2024, 16 Mei 2027, 2024-09-13, 13/09/2024)
     */
    private function parseDateSafely(?string $value): ?string
    {
        if (empty($value)) return null;

        $val = trim($value);

        // Jika angka integer serial Excel (misal: 45548)
        if (is_numeric($val) && (int)$val > 30000 && (int)$val < 70000) {
            try {
                return Carbon::createFromTimestamp(((int)$val - 25569) * 86400)->format('Y-m-d');
            } catch (\Throwable) {}
        }

        // Terjemahkan nama bulan Indonesia ke Inggris untuk Carbon
        $indoMonths = [
            'januari' => 'January', 'jan' => 'Jan',
            'februari' => 'February', 'pebruari' => 'February', 'peb' => 'Feb', 'feb' => 'Feb',
            'maret' => 'March', 'mar' => 'Mar',
            'april' => 'April', 'apr' => 'Apr',
            'mei' => 'May',
            'juni' => 'June', 'jun' => 'Jun',
            'juli' => 'July', 'jul' => 'Jul',
            'agustus' => 'August', 'agu' => 'Aug', 'ags' => 'Aug', 'agt' => 'Aug',
            'september' => 'September', 'sep' => 'Sep',
            'oktober' => 'October', 'okt' => 'Oct',
            'nopember' => 'November', 'november' => 'November', 'nop' => 'Nov', 'nov' => 'Nov',
            'desember' => 'December', 'des' => 'Dec',
        ];

        $cleanVal = $val;
        foreach ($indoMonths as $indo => $eng) {
            $cleanVal = preg_replace('/\b' . $indo . '\b/i', $eng, $cleanVal);
        }

        try {
            return Carbon::parse($cleanVal)->format('Y-m-d');
        } catch (\Throwable) {
            // Coba format umum d/m/Y atau d-m-Y
            if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/', $cleanVal, $m)) {
                return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
            }
            return null;
        }
    }

    /**
     * Tarik dan Sinkronkan Data Temuan Audit dari Google Sheets
     */
    public function syncAuditSheets(Request $request)
    {
        $request->validate([
            'sheets_url' => ['required', 'url'],
        ]);

        $url = trim($request->sheets_url);

        // Ambil Sheet ID & GID
        preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches);
        if (!isset($matches[1])) {
            return back()->withErrors(['audit_sheets_url' => 'Format tautan Google Sheets tidak valid. Salin tautan dari address bar browser.']);
        }
        $docId = $matches[1];

        $gid = null;
        if (preg_match('/[?&#]gid=([0-9]+)/', $url, $gidMatches)) {
            $gid = $gidMatches[1];
        }

        // Simpan URL yang diinput user ke pengaturan
        Setting::updateOrCreate(
            ['key' => 'sysdev_audit_sheets_url'],
            [
                'value' => $url,
                'description' => 'Tautan Google Spreadsheet untuk Sinkronisasi Temuan Audit SPPT SNI'
            ]
        );

        $csvUrl = "https://docs.google.com/spreadsheets/d/{$docId}/export?format=csv" . ($gid !== null ? "&gid={$gid}" : "");

        try {
            $response = Http::timeout(30)->withoutVerifying()->get($csvUrl);

            if (!$response->successful()) {
                if ($response->status() === 401 || $response->status() === 403) {
                    throw new \Exception("Akses Google Sheets Ditolak (HTTP " . $response->status() . "). Google Spreadsheet ini masih berstatus Restricted / Privat. Silakan buka file tersebut di Google Drive, klik tombol 'Bagikan' (Share) di pojok kanan atas, lalu ubah Akses Umum menjadi 'Siapa saja yang memiliki link' (Anyone with the link can view).");
                }
                throw new \Exception("Gagal mengambil data dari Google Sheets (Kode status: " . $response->status() . "). Pastikan tautan benar dan dapat diakses publik.");
            }

            $csvData = $response->body();
            if (str_contains($csvData, '<html') && (str_contains(strtolower($csvData), 'sign in') || str_contains(strtolower($csvData), 'service login'))) {
                throw new \Exception("Akses Google Sheets memerlukan login Google. Harap ubah hak akses Google Spreadsheet menjadi 'Anyone with the link' (Siapa saja yang memiliki link) agar sistem dapat membaca data.");
            }

            $stream = fopen('php://memory', 'r+');
            fwrite($stream, $csvData);
            rewind($stream);

            $rows = [];
            while (($row = fgetcsv($stream)) !== false) {
                $rows[] = $row;
            }
            fclose($stream);

            $result = $this->processAuditFindingRows($rows);

            return back()->with('success', "Berhasil menyinkronkan {$result['synced']} baris data Temuan Audit dari Google Sheets!")->with('active_module', 'audit');

        } catch (\Throwable $e) {
            return back()->withErrors(['audit_sheets_url' => $e->getMessage()])->with('active_module', 'audit');
        }
    }

    /**
     * Memproses baris spreadsheet untuk Temuan Audit SPPT SNI
     */
    private function processAuditFindingRows(array $rows): array
    {
        if (empty($rows)) {
            throw new \Exception("Data spreadsheet kosong.");
        }

        // Cari baris header
        $headerRowIndex = null;
        $headers = [];

        foreach ($rows as $index => $row) {
            $rowStr = strtoupper(implode(' ', array_map('strval', $row)));
            if (str_contains($rowStr, 'KATEGORI') && (str_contains($rowStr, 'DESKRIPSI') || str_contains($rowStr, 'KETIDAKSESUAIAN') || str_contains($rowStr, 'JENIS'))) {
                $headerRowIndex = $index;
                $headers = array_map(function ($h) {
                    return strtoupper(trim(preg_replace('/\s+/', ' ', (string)$h)));
                }, $row);
                break;
            }
        }

        if ($headerRowIndex === null) {
            $headerRowIndex = 0;
            $headers = array_map(function ($h) {
                return strtoupper(trim(preg_replace('/\s+/', ' ', (string)$h)));
            }, $rows[0]);
        }

        // Map column indices
        $map = [
            'no' => null,
            'category' => null,
            'jenis' => null,
            'description' => null,
            'root_cause' => null,
            'correction' => null,
            'corrective_action' => null,
            'file_evidence_desc' => null,
            'file_evidence' => null,
            'audit_review' => null,
            'plan_date' => null,
            'real_date' => null,
            'explanation' => null,
            'pic' => null,
            'status_lengkap' => null,
        ];

        foreach ($headers as $idx => $rawH) {
            $h = strtoupper(trim(preg_replace('/\s+/', ' ', (string)$rawH)));
            if ($map['no'] === null && in_array($h, ['NO', 'NO.', 'NOMOR', '#'])) {
                $map['no'] = $idx;
            } elseif ($map['category'] === null && (str_contains($h, 'CATEGOR') || str_contains($h, 'KATEGORI'))) {
                $map['category'] = $idx;
            } elseif ($map['jenis'] === null && str_contains($h, 'JENIS')) {
                $map['jenis'] = $idx;
            } elseif ($map['description'] === null && (str_contains($h, 'DESCRIP') || str_contains($h, 'DESKRIPSI') || str_contains($h, 'KETIDAKSESUAIAN') || str_contains($h, 'URAIAN'))) {
                $map['description'] = $idx;
            } elseif ($map['root_cause'] === null && (str_contains($h, 'ROOT') || str_contains($h, 'AKAR') || str_contains($h, 'PENYEBAB') || str_contains($h, 'INVESTIGATION'))) {
                $map['root_cause'] = $idx;
            } elseif ($map['corrective_action'] === null && (str_contains($h, 'CORRECTIVE') || str_contains($h, 'KOREKTIF'))) {
                $map['corrective_action'] = $idx;
            } elseif ($map['correction'] === null && (str_contains($h, 'CORRECTION') || str_contains($h, 'KOREKSI'))) {
                $map['correction'] = $idx;
            } elseif ($map['file_evidence_desc'] === null && (str_contains($h, 'EVIDENCE DESC') || str_contains($h, 'DESKRIPSI BUKTI') || str_contains($h, 'URAIAN BUKTI'))) {
                $map['file_evidence_desc'] = $idx;
            } elseif ($map['file_evidence'] === null && ($h === 'FILE EVIDENCE' || str_contains($h, 'EVIDENCE') || str_contains($h, 'BUKTI DOKUMEN') || str_contains($h, 'LINK'))) {
                $map['file_evidence'] = $idx;
            } elseif ($map['audit_review'] === null && (str_contains($h, 'REVIEW') || str_contains($h, 'AUDIT TEAM'))) {
                $map['audit_review'] = $idx;
            } elseif ($map['plan_date'] === null && (str_contains($h, 'PLAN') || str_contains($h, 'RENCANA') || str_contains($h, 'TARGET'))) {
                $map['plan_date'] = $idx;
            } elseif ($map['real_date'] === null && (str_contains($h, 'REAL') || str_contains($h, 'REALISASI'))) {
                $map['real_date'] = $idx;
            } elseif ($map['explanation'] === null && (str_contains($h, 'EXPLANATION') || str_contains($h, 'KETERANGAN') || str_contains($h, 'PENJELASAN') || str_contains($h, 'CATATAN'))) {
                $map['explanation'] = $idx;
            } elseif ($map['pic'] === null && str_contains($h, 'PIC')) {
                $map['pic'] = $idx;
            } elseif ($map['status_lengkap'] === null && (str_contains($h, 'STATUS') || str_contains($h, 'LENGKAP'))) {
                $map['status_lengkap'] = $idx;
            }
        }

        // Fallbacks
        if ($map['category'] === null && count($headers) > 1) $map['category'] = 1;
        if ($map['jenis'] === null && count($headers) > 2) $map['jenis'] = 2;
        if ($map['description'] === null && count($headers) > 3) $map['description'] = 3;

        DB::beginTransaction();
        try {
            // Gunakan query delete agar transaksi ACID aman (tidak memicu implicit commit seperti truncate)
            SysdevAuditFinding::query()->delete();

            $syncedCount = 0;
            $dataRows = array_slice($rows, $headerRowIndex + 1);

            foreach ($dataRows as $r) {
                $rowCombined = trim(implode('', array_map('strval', $r)));
                if (empty($rowCombined)) continue;

                $noVal = isset($map['no']) && isset($r[$map['no']]) ? trim($r[$map['no']]) : null;
                $catVal = isset($map['category']) && isset($r[$map['category']]) ? trim($r[$map['category']]) : null;
                $descVal = isset($map['description']) && isset($r[$map['description']]) ? trim($r[$map['description']]) : null;

                if (empty($catVal) && empty($descVal)) continue;

                // Normalisasi Kategori
                $catNormalized = 'Observation';
                $catLower = strtolower((string)$catVal);
                if (str_contains($catLower, 'major')) {
                    $catNormalized = 'Major';
                } elseif (str_contains($catLower, 'minor')) {
                    $catNormalized = 'Minor';
                } elseif (str_contains($catLower, 'obs')) {
                    $catNormalized = 'Observation';
                } elseif (!empty($catVal)) {
                    $catNormalized = ucfirst($catLower);
                }

                // Normalisasi Status Lengkap
                $rawStatus = isset($map['status_lengkap']) && isset($r[$map['status_lengkap']]) ? trim($r[$map['status_lengkap']]) : '';
                $evidenceVal = isset($map['file_evidence']) && isset($r[$map['file_evidence']]) ? trim($r[$map['file_evidence']]) : '';
                
                if (!empty($rawStatus)) {
                    $statusLower = strtolower($rawStatus);
                    if (str_contains($statusLower, 'belum') || str_contains($statusLower, 'not') || str_contains($statusLower, 'open') || str_contains($statusLower, 'progress')) {
                        $statusNormalized = 'Belum Lengkap';
                    } else {
                        $statusNormalized = 'Lengkap';
                    }
                } else {
                    // Jika kolom status tidak ada di spreadsheet, otomatis ditentukan dari keterisian file bukti dokumen (Evidence)
                    $statusNormalized = !empty($evidenceVal) ? 'Lengkap' : 'Belum Lengkap';
                }

                SysdevAuditFinding::create([
                    'no' => is_numeric($noVal) ? (int)$noVal : ($syncedCount + 1),
                    'category' => $catNormalized,
                    'jenis' => isset($map['jenis']) && isset($r[$map['jenis']]) ? trim($r[$map['jenis']]) : 'SNI',
                    'description' => $descVal,
                    'root_cause' => isset($map['root_cause']) && isset($r[$map['root_cause']]) ? trim($r[$map['root_cause']]) : null,
                    'correction' => isset($map['correction']) && isset($r[$map['correction']]) ? trim($r[$map['correction']]) : null,
                    'corrective_action' => isset($map['corrective_action']) && isset($r[$map['corrective_action']]) ? trim($r[$map['corrective_action']]) : null,
                    'file_evidence_desc' => isset($map['file_evidence_desc']) && isset($r[$map['file_evidence_desc']]) ? trim($r[$map['file_evidence_desc']]) : null,
                    'file_evidence' => $evidenceVal ?: null,
                    'audit_review' => isset($map['audit_review']) && isset($r[$map['audit_review']]) ? trim($r[$map['audit_review']]) : null,
                    'plan_date' => isset($map['plan_date']) && isset($r[$map['plan_date']]) ? trim($r[$map['plan_date']]) : null,
                    'real_date' => isset($map['real_date']) && isset($r[$map['real_date']]) ? trim($r[$map['real_date']]) : null,
                    'explanation' => isset($map['explanation']) && isset($r[$map['explanation']]) ? trim($r[$map['explanation']]) : null,
                    'pic' => isset($map['pic']) && isset($r[$map['pic']]) ? trim($r[$map['pic']]) : null,
                    'status_lengkap' => $statusNormalized,
                ]);

                $syncedCount++;
            }

            if (DB::transactionLevel() > 0) {
                DB::commit();
            }
            return ['synced' => $syncedCount];

        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            throw $e;
        }
    }

    public function storeAuditFinding(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', 'string'],
            'jenis' => ['nullable', 'string'],
            'description' => ['required', 'string'],
            'root_cause' => ['nullable', 'string'],
            'correction' => ['nullable', 'string'],
            'corrective_action' => ['nullable', 'string'],
            'file_evidence_desc' => ['nullable', 'string'],
            'file_evidence' => ['nullable', 'string'],
            'audit_review' => ['nullable', 'string'],
            'plan_date' => ['nullable', 'string'],
            'real_date' => ['nullable', 'string'],
            'explanation' => ['nullable', 'string'],
            'pic' => ['nullable', 'string'],
            'status_lengkap' => ['required', 'string'],
        ]);

        $maxNo = SysdevAuditFinding::max('no') ?? 0;
        $validated['no'] = $maxNo + 1;

        SysdevAuditFinding::create($validated);

        return back()->with('success', 'Temuan audit berhasil ditambahkan.')->with('active_module', 'audit');
    }

    public function updateAuditFinding(Request $request, $id)
    {
        $finding = SysdevAuditFinding::findOrFail($id);

        $validated = $request->validate([
            'category' => ['required', 'string'],
            'jenis' => ['nullable', 'string'],
            'description' => ['required', 'string'],
            'root_cause' => ['nullable', 'string'],
            'correction' => ['nullable', 'string'],
            'corrective_action' => ['nullable', 'string'],
            'file_evidence_desc' => ['nullable', 'string'],
            'file_evidence' => ['nullable', 'string'],
            'audit_review' => ['nullable', 'string'],
            'plan_date' => ['nullable', 'string'],
            'real_date' => ['nullable', 'string'],
            'explanation' => ['nullable', 'string'],
            'pic' => ['nullable', 'string'],
            'status_lengkap' => ['required', 'string'],
        ]);

        $finding->update($validated);

        return back()->with('success', 'Temuan audit berhasil diperbarui.')->with('active_module', 'audit');
    }

    public function destroyAuditFinding($id)
    {
        $finding = SysdevAuditFinding::findOrFail($id);
        $finding->delete();

        return back()->with('success', 'Temuan audit berhasil dihapus.')->with('active_module', 'audit');
    }
}

