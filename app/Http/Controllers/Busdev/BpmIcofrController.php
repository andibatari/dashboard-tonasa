<?php

namespace App\Http\Controllers\Busdev;

use App\Exports\IcofrSummaryExport;
use App\Http\Controllers\Controller;
use App\Models\IcofrControl;
use App\Models\IcofrCsaReport;
use App\Models\IcofrCycle;
use App\Models\IcofrProcess;
use App\Models\IcofrSubProcess;
use App\Models\Unit;
use App\Services\RcmImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Carbon;

class BpmIcofrController extends Controller
{
    /** Role yang boleh mengelola master data & melihat dasbor (khusus unit Busdev). */
    private const ADMIN_ROLES = ['Developer', 'Super Admin', 'Admin'];

    private const QUARTERS = ['TW1', 'TW2', 'TW3', 'TW4'];

    /** Field form yang disimpan ke icofr_csa_reports (bukan ke icofr_controls). */
    private const REPORT_FIELDS = ['tahun', 'triwulan', 'hasil_csa', 'keterangan_tidak_efektif'];

    // =====================================================================
    //  HALAMAN UTAMA
    // =====================================================================
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        [$selectedYear, $selectedTW] = $this->resolvePeriod($request);

        $cycles      = IcofrCycle::with([
            'processes' => fn($q) => $q->orderBy('code'),
            'processes.controls' => fn($q) => $q->orderBy('control_code'),
        ])->orderBy('code')->get();
        $summaryData = $this->buildSummary($cycles, $selectedYear, $selectedTW);
        $departments    = \App\Models\Department::orderBy('name_dept')->get();
        $picDepartments = $departments->where('is_icofr_pic', true)->values();
        $deptGmMap      = $picDepartments->map(fn($d) => [
            'id'         => $d->id,
            'dept'       => $d->name_dept,
            'jabatan'    => $d->leader_title ?: ('GM of ' . preg_replace('/^(Department|Dept\.)\s+of\s+/i', '', $d->name_dept)),
            'nama'       => $d->leader_name ?: '',
            'is_pic'     => (bool) $d->is_icofr_pic,
        ])->values();
        $directors      = \App\Models\Setting::getDirectors();
        $icofrSheetsUrl = \App\Models\Setting::where('key', 'icofr_sheets_url')->value('value') ?? config('app.icofr_sheets_url', '');
        $icofrWebhookUrl = \App\Models\Setting::where('key', 'icofr_sheets_webhook_url')->value('value') ?? '';
        $icofrPics      = \App\Models\IcofrPic::orderBy('unit')->orderBy('position')->get();
        $allUnits       = \App\Models\Unit::with('department')->orderBy('name_unit')->get();

        return view('busdev.bpm-icofr.index', compact('cycles', 'summaryData', 'selectedTW', 'selectedYear', 'departments', 'picDepartments', 'deptGmMap', 'directors', 'icofrSheetsUrl', 'icofrWebhookUrl', 'icofrPics', 'allUnits'));
    }

    public function show($id)
    {
        $this->authorizeAdmin();

        $cycle = IcofrCycle::with([
            'processes' => fn($q) => $q->orderBy('code'),
            'processes.subProcesses' => fn($q) => $q->orderBy('code'),
            'processes.controls' => fn($q) => $q->orderBy('control_code'),
            'processes.controls.unit'
        ])->findOrFail($id);
        $units = Unit::all();

        // Satu query untuk seluruh laporan CSA terakhir (menggantikan query per baris di view)
        $controlIds    = $cycle->processes->flatMap(fn($p) => $p->controls)->pluck('id');
        $reportsByControl = IcofrCsaReport::whereIn('icofr_control_id', $controlIds)
            ->orderByDesc('tahun')->orderByDesc('triwulan')
            ->get()->groupBy('icofr_control_id');

        $icofrSheetsUrl = \App\Models\Setting::where('key', 'icofr_sheets_url')->value('value') ?? config('app.icofr_sheets_url', '');
        $icofrWebhookUrl = \App\Models\Setting::where('key', 'icofr_sheets_webhook_url')->value('value') ?? '';
        $icofrPics      = \App\Models\IcofrPic::orderBy('unit')->orderBy('position')->get();
        $departments    = \App\Models\Department::orderBy('name_dept')->get();
        $allUnits       = \App\Models\Unit::with('department')->orderBy('name_unit')->get();

        return view('busdev.bpm-icofr.show', compact('cycle', 'units', 'reportsByControl', 'icofrSheetsUrl', 'icofrWebhookUrl', 'icofrPics', 'departments', 'allUnits'));
    }

    // =====================================================================
    //  MASTER DATA (khusus admin)
    // =====================================================================
    public function storeCycle(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'code' => ['required', 'string', 'max:5', Rule::unique('icofr_cycles', 'code')],
            'name' => ['required', 'string', 'max:255'],
        ]);

        IcofrCycle::create($data);

        return back()->with('success', 'Siklus bisnis baru berhasil ditambahkan!');
    }

    public function updateCycle(Request $request, $id)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'code' => ['required', 'string', 'max:5'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        \App\Models\IcofrCycle::findOrFail($id)->update($data);

        return back()->with('success', 'Data siklus berhasil diperbarui.');
    }

    public function destroyCycle($id)
    {
        $this->authorizeAdmin();

        // Hapus siklus (pastikan relasi di model sudah diset cascade, atau hapus manual di sini jika perlu)
        \App\Models\IcofrCycle::findOrFail($id)->delete();

        return back()->with('success', 'Siklus beserta seluruh proses dan kontrol di dalamnya berhasil dihapus secara permanen.');
    }

    public function storeProcess(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'icofr_cycle_id' => ['required', 'exists:icofr_cycles,id'],
            'code'           => ['required', 'string', 'max:10'],
            'name'           => ['required', 'string', 'max:255'],
        ]);

        IcofrProcess::create($data);

        return back()->with('success', 'Proses bisnis baru berhasil ditambahkan!');
    }

    public function updateProcess(Request $request, $id)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'code' => ['required', 'string', 'max:10'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        IcofrProcess::findOrFail($id)->update($data);

        return back()->with('success', 'Proses bisnis berhasil diperbarui.');
    }

    public function destroyProcess($id)
    {
        $this->authorizeAdmin();

        IcofrProcess::findOrFail($id)->delete();

        return back()->with('success', 'Proses bisnis beserta isinya berhasil dihapus.');
    }

    /** Status dokumen per proses. Dipakai untuk kolom (a) (b) (d) (e) pada dasbor. */
    public function updateProcessStatus(Request $request, $id)
    {
        $this->authorizeAdmin();

        IcofrProcess::findOrFail($id)->update([
            'dokumentasi_done' => $request->boolean('dokumentasi_done'),
            'validasi_done'    => $request->boolean('validasi_done'),
            'bpm_updated'      => $request->boolean('bpm_updated'),
            'rcm_updated'      => $request->boolean('rcm_updated'),
        ]);

        return back()->with('success', 'Status dokumen proses berhasil diperbarui.');
    }

    // ----- Sub proses -----
    public function storeSubProcess(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'icofr_process_id' => ['required', 'exists:icofr_processes,id'],
            'code'             => [
                'required', 'string', 'max:10',
                Rule::unique('icofr_sub_processes', 'code')->where('icofr_process_id', $request->input('icofr_process_id')),
            ],
            'name'             => ['required', 'string', 'max:255'],
        ], ['code.unique' => 'Kode sub proses sudah dipakai pada proses ini.']);

        IcofrSubProcess::create($data);

        return back()->with('success', 'Sub proses baru berhasil ditambahkan!');
    }

    public function updateSubProcess(Request $request, $id)
    {
        $this->authorizeAdmin();

        $sub = IcofrSubProcess::findOrFail($id);

        $data = $request->validate([
            'code' => [
                'required', 'string', 'max:10',
                Rule::unique('icofr_sub_processes', 'code')
                    ->where('icofr_process_id', $sub->icofr_process_id)
                    ->ignore($sub->id),
            ],
            'name' => ['required', 'string', 'max:255'],
        ], ['code.unique' => 'Kode sub proses sudah dipakai pada proses ini.']);

        $sub->update($data);

        return back()->with('success', 'Sub proses berhasil diperbarui.');
    }

    public function destroySubProcess($id)
    {
        $this->authorizeAdmin();

        $sub = IcofrSubProcess::findOrFail($id);

        IcofrControl::where('icofr_sub_process_id', $sub->id)->update(['icofr_sub_process_id' => null]);
        $sub->delete();

        return back()->with('success', 'Sub proses dihapus. Kontrol di dalamnya dipindahkan ke "Tanpa sub proses".');
    }

    // ----- Kontrol RCM -----
    public function storeControl(Request $request)
    {
        $this->authorizeAdmin();

        $data = $this->validateControl($request);

        DB::transaction(function () use ($data) {
            $control = IcofrControl::create(Arr::except($data, self::REPORT_FIELDS));
            $this->saveCsaReport($control, $data);
        });

        return back()->with('success', 'Matriks Risiko dan Kontrol (RCM) berhasil ditambahkan!');
    }

    public function updateControl(Request $request, $id)
    {
        $this->authorizeAdmin();

        $control = IcofrControl::findOrFail($id);
        $data    = $this->validateControl($request); // validasi sekali di awal: tidak ada simpan setengah jalan

        DB::transaction(function () use ($control, $data) {
            $control->update(Arr::except($data, self::REPORT_FIELDS));
            $this->saveCsaReport($control, $data);
        });

        return back()->with('success', 'Matriks RCM berhasil diperbarui!');
    }

    public function destroyControl($id)
    {
        $this->authorizeAdmin();
        IcofrControl::findOrFail($id)->delete();

        return back()->with('success', 'Data kontrol berhasil dihapus secara permanen.');
    }

    public function uploadBpm(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'cycle_id' => ['required', 'exists:icofr_cycles,id'],
            'bpm_file' => ['required', 'mimes:pdf', 'max:25000'],
        ]);

        $cycle = IcofrCycle::findOrFail($request->cycle_id);

        if ($cycle->bpm_file_path) {
            Storage::disk('public')->delete($cycle->bpm_file_path);
        }

        $cycle->bpm_file_path = $request->file('bpm_file')->store('bpm_documents', 'public');
        $cycle->save();

        return back()->with('success', 'Dokumen BPM berhasil diunggah.');
    }

    // =====================================================================
    //  IMPORT FILE RCM (Excel)
    // =====================================================================
    public function importRcm(Request $request, RcmImportService $importer)
    {
        $this->authorizeAdmin();

        $request->validate([
            'rcm_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:20480'],
        ], [
            'rcm_file.required' => 'Pilih file Excel RCM terlebih dahulu.',
            'rcm_file.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'rcm_file.max'      => 'Ukuran file maksimal 20 MB.',
        ]);

        try {
            $result = $importer->import($request->file('rcm_file')->getRealPath());
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['rcm_file' => 'File RCM gagal dibaca: ' . $e->getMessage()]);
        }

        return back()
            ->with('success', "Import RCM selesai: {$result['controls']} kontrol diproses ({$result['created']} baru, {$result['updated']} diperbarui), {$result['processes']} proses baru.")
            ->with('import_warnings', array_slice($result['skipped'], 0, 15));
    }

    // =====================================================================
    //  SINKRONISASI GOOGLE SHEETS (MASTER DATA + HASIL CSA)
    // =====================================================================
    public function syncFromGoogleSheets(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'sheets_url' => ['required', 'url'],
            'tahun'      => ['required', 'integer'],
            'triwulan'   => ['required', 'string'],
        ]);

        $url = $request->sheets_url;
        preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches);
        if (!isset($matches[1])) {
            return back()->withErrors(['sheets_url' => 'Tautan Google Sheets tidak valid.']);
        }
        $documentId = $matches[1];

        // Simpan URL sheets agar otomatis tersimpan dan terisi kembali saat user membuka modal
        \App\Models\Setting::updateOrCreate(
            ['key' => 'icofr_sheets_url'],
            ['value' => $url, 'description' => 'URL Google Sheets aktif ICoFR RCM']
        );

        $gid = null;
        if (preg_match('/[?&#]gid=([0-9]+)/', $url, $gidMatches)) {
            $gid = $gidMatches[1];
        }
        
        if ($gid !== null) {
            $csvUrl = "https://docs.google.com/spreadsheets/d/{$documentId}/export?format=csv&gid={$gid}";
        } else {
            $csvUrl = "https://docs.google.com/spreadsheets/d/{$documentId}/export?format=csv";
        }

        try {
            $response = \Illuminate\Support\Facades\Http::get($csvUrl);
            
            if (!$response->successful()) {
                throw new \Exception("Koneksi ditolak oleh Google (Error HTTP: " . $response->status() . "). Coba tunggu 1 menit lalu ulangi.");
            }
            
            $csvData = $response->body();
            
            if (str_contains($csvData, '<html') && str_contains(strtolower($csvData), 'sign in')) {
                throw new \Exception("Akses Ditolak! File Google Sheets Anda masih terkunci (Private/Restricted).");
            }

            $lines = [];
            $stream = fopen('php://memory', 'r+');
            fwrite($stream, $csvData);
            rewind($stream);
            while (($row = fgetcsv($stream)) !== false) {
                $lines[] = $row;
            }
            fclose($stream);

            $header = [];
            $headerRowIndex = -1;

            for ($i = 0; $i < min(10, count($lines)); $i++) {
                $rowString = strtolower(implode(" ", $lines[$i]));
                if (str_contains($rowString, '(4)') || str_contains($rowString, 'ref. control') || str_contains($rowString, 'ref control')) {
                    $headerRowIndex = $i;
                    break;
                }
            }

            if ($headerRowIndex === -1) {
                throw new \Exception("Format kolom tidak dikenali. Pastikan Anda menyalin link dari sheet yang tepat.");
            }

            $maxCols = count($lines[$headerRowIndex]);
            for ($c = 0; $c < $maxCols; $c++) {
                $colText = '';
                for ($r = 0; $r <= $headerRowIndex; $r++) {
                    if (isset($lines[$r][$c])) {
                        $colText .= ' ' . $lines[$r][$c];
                    }
                }
                $header[$c] = trim($colText);
            }

            $findCol = function($keywords) use ($header) {
                foreach ($header as $index => $colName) {
                    $cleanName = preg_replace('/\s+/', ' ', strtolower(trim($colName)));
                    foreach ((array)$keywords as $kw) {
                        if (str_contains($cleanName, strtolower($kw))) return $index;
                    }
                }
                return false;
            };

            $idxSiklus    = $findCol(['Siklus', '(1)']);
            $idxProses    = $findCol(['Proses', '(2)']);
            $idxSub       = $findCol(['Sub Proses', '(3)']);
            
            // --- DETEKSI TEPAT: 3 KOLOM PELAKSANA KONTROL (NAMA, JABATAN, UNIT) ---
            $idxNamaPic       = false;
            $idxJabatanPic    = false;
            $idxPelaksanaUnit = false;
            foreach ($header as $index => $colName) {
                $cleanName = preg_replace('/\s+/', ' ', strtolower(trim($colName)));
                if (str_contains($cleanName, 'nama pelaksana') || str_contains($cleanName, 'nama pic')) {
                    $idxNamaPic = $index;
                } elseif (str_contains($cleanName, 'jabatan pelaksana') || str_contains($cleanName, 'jabatan pic') || str_contains($cleanName, 'jabatan')) {
                    $idxJabatanPic = $index;
                } elseif (str_contains($cleanName, 'unit pelaksana') || str_contains($cleanName, 'unit pic') || str_contains($cleanName, 'unit kerja')) {
                    $idxPelaksanaUnit = $index;
                }
            }
            if ($idxPelaksanaUnit === false) {
                foreach ($header as $index => $colName) {
                    $cleanName = preg_replace('/\s+/', ' ', strtolower(trim($colName)));
                    if ($index !== $idxNamaPic && $index !== $idxJabatanPic) {
                        if (str_contains($cleanName, 'pelaksana kontrol') || str_contains($cleanName, 'pelaksana')) {
                            $idxPelaksanaUnit = $index;
                            break;
                        }
                    }
                }
            }
            
            // --- DETEKSI TEPAT: KOLOM RISIKO (BARU DARI SPREADSHEET) ---
            $idxRefRisk   = $findCol(['ref. risiko', 'ref risiko', 'ref. risk', 'ref risk', 'kode risiko', 'ref.risiko']);
            $idxDescRisk  = $findCol(['deskripsi risiko', 'uraian risiko', 'deskripsi risk', 'risk description', 'keterangan risiko']);
            $idxNilaiRisk = $findCol(['nilai risiko', 'nilai risk', 'risk value', 'tingkat risiko', 'level risiko']);
            $idxFraudRisk = $findCol(['risiko kecurangan', 'kecurangan', 'fraud risk', 'fraud']);
            $idxKeyCtrl   = $findCol(['key/non key', 'key / non key', 'key/non', 'key non key', 'tipe kontrol']);

            $idxCtrlCode  = $findCol(['Ref. Control', 'Ref Control', '(4)', 'no. control', 'no control', 'kode kontrol', 'kode control']);
            
            // Deskripsi Kontrol: cari yang spesifik kontrol / (5), dan BUKAN deskripsi risiko
            $idxDesc = false;
            foreach ($header as $index => $colName) {
                $cleanName = preg_replace('/\s+/', ' ', strtolower(trim($colName)));
                if (str_contains($cleanName, '(5)') || str_contains($cleanName, 'deskripsi kontrol') || str_contains($cleanName, 'uraian kontrol')) {
                    $idxDesc = $index;
                    break;
                }
            }
            if ($idxDesc === false) {
                foreach ($header as $index => $colName) {
                    $cleanName = preg_replace('/\s+/', ' ', strtolower(trim($colName)));
                    if (str_contains($cleanName, 'deskripsi') && !str_contains($cleanName, 'risiko') && !str_contains($cleanName, 'risk')) {
                        $idxDesc = $index;
                        break;
                    }
                }
            }
            if ($idxDesc === false) {
                $idxDesc = $findCol(['Deskripsi', '(5)']);
            }

            $idxFreq      = $findCol(['Frekuensi', '(7)']);
            $idxDoc       = $findCol(['Dokumen', '(10)']);
            $idxObj       = $findCol(['Obyektif', '(11)']);
            $idxProc      = $findCol(['Prosedur', '(12)']);
            $idxHasilCsa  = $findCol(['Hasil CSA', '(13)']);
            $idxKet       = $findCol(['Keterangan Tidak', '(14)']);
            $idxRoot      = $findCol(['Root Cause', '(15)']);
            $idxKertas    = $findCol(['Kertas Kerja', '(16)']);
            $idxRemediasi = $findCol(['Remediasi', '(17)']);

            // Kolom Master Kepala Departemen (jika ada pada sheet CRM/RCM)
            $idxKepalaDept     = $findCol(['Kepala Departemen', 'Jabatan GM']);
            $idxNamaKepalaDept = $findCol(['Nama Kepala Departemen', 'Nama GM']);
            $allDepartments    = \App\Models\Department::all();

            if ($idxCtrlCode === false || $idxHasilCsa === false) {
                throw new \Exception("Gagal menemukan kolom inti. Pastikan nomor (4) untuk Ref Control dan (13) untuk Hasil CSA tidak dihapus.");
            }

            $countNewControls = 0;
            $countUpdatedControls = 0;
            $countCsaUpdated = 0;

            $ingatSiklus = '';
            $ingatProses = '';
            $ingatSubProses = '';
            
            // Ambil semua data Unit dari database sekali saja untuk pencocokan cepat
            $allUnits = \App\Models\Unit::all();

            \Illuminate\Support\Facades\DB::beginTransaction();

            for ($i = $headerRowIndex + 1; $i < count($lines); $i++) {
                $row = $lines[$i];
                if (empty($row) || !isset($row[$idxCtrlCode]) || empty(trim($row[$idxCtrlCode]))) continue;
                if (stripos($row[$idxCtrlCode], 'kolom ini diisi') !== false) continue;
                
                $controlCode = trim($row[$idxCtrlCode]);

                if ($idxSiklus !== false && !empty(trim($row[$idxSiklus]))) {
                    $ingatSiklus = trim($row[$idxSiklus]);
                }
                if ($idxProses !== false && !empty(trim($row[$idxProses]))) {
                    $ingatProses = trim($row[$idxProses]);
                    $ingatSubProses = ''; 
                }
                if ($idxSub !== false && !empty(trim($row[$idxSub]))) {
                    $ingatSubProses = trim($row[$idxSub]);
                }

                $processId = null;
                $subProcessId = null;

                if (!empty($ingatSiklus) && !empty($ingatProses)) {
                    $siklusRaw = trim($ingatSiklus);
                    if (preg_match('/^([A-Za-z0-9]+)[\.\-\s]\s*(.*)/', $siklusRaw, $cMatch)) {
                        $cCode = strtoupper(trim($cMatch[1])); 
                        $cName = trim($cMatch[2]);             
                    } else {
                        $cCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $siklusRaw), 0, 3));
                        $cName = $siklusRaw;
                    }

                    $cycle = \App\Models\IcofrCycle::where('name', $cName)
                        ->orWhere('name', $siklusRaw)
                        ->orWhere('code', $cCode)
                        ->first();
                    if (!$cycle) {
                        $cycle = \App\Models\IcofrCycle::create(['name' => $cName, 'code' => $cCode]);
                    } else {
                        $cycle->update(['code' => $cCode]); 
                    }

                    preg_match('/^(\d+[\.\-]?\w*)\s*(.*)/', $ingatProses, $pMatch);
                    $pCode = !empty($pMatch[1]) ? trim($pMatch[1], '.') : 'P';
                    $pName = !empty($pMatch[2]) ? trim($pMatch[2]) : $ingatProses;

                    $process = \App\Models\IcofrProcess::firstOrCreate(
                        ['icofr_cycle_id' => $cycle->id, 'name' => $pName],
                        ['code' => $pCode]
                    );
                    $processId = $process->id;

                    if (!empty($ingatSubProses)) {
                        preg_match('/^(\d+[\.\-]?\w*)\s*(.*)/', $ingatSubProses, $sMatch);
                        $sCode = !empty($sMatch[1]) ? trim($sMatch[1], '.') : 'S';
                        $sName = !empty($sMatch[2]) ? trim($sMatch[2]) : $ingatSubProses;

                        $subProcess = \App\Models\IcofrSubProcess::firstOrCreate(
                            ['icofr_process_id' => $process->id, 'name' => $sName],
                            ['code' => $sCode]
                        );
                        $subProcessId = $subProcess->id;
                    }
                }

                // --- BACA NAMA PELAKSANA KONTROL, JABATAN & UNIT/FUNGSI PELAKSANA ---
                $namaPic = $idxNamaPic !== false ? trim($row[$idxNamaPic] ?? '') : '';
                $jabatanPic = $idxJabatanPic !== false ? trim($row[$idxJabatanPic] ?? '') : '';
                $unitText = $idxPelaksanaUnit !== false ? trim($row[$idxPelaksanaUnit] ?? '') : '';
                $unitId = null;
                $executorFunction = null;

                if (!empty($unitText)) {
                    // Cari unit yang namanya cocok dengan Master Unit
                    $matchedUnit = $allUnits->first(function ($unit) use ($unitText) {
                        return strtolower(trim($unit->name_unit)) === strtolower(trim($unitText));
                    });

                    if ($matchedUnit) {
                        $unitId = $matchedUnit->id;
                        $executorFunction = $matchedUnit->name_unit;
                    } else {
                        // Jika tidak ada di Master Unit, simpan teks aslinya
                        $executorFunction = $unitText; 
                    }
                }

                // Otomatis sinkronisasi ke Master PIC jika ada jabatan & unit
                if (!empty($jabatanPic) && !empty($unitText)) {
                    \App\Models\IcofrPic::updateOrCreate(
                        ['position' => $jabatanPic, 'unit' => $unitText],
                        ['name' => !empty($namaPic) ? $namaPic : null]
                    );
                }

                // --- TAHAP B: SINKRONISASI MASTER KONTROL ---
                $control = \App\Models\IcofrControl::where('control_code', $controlCode)->first();
                
                $refRiskRaw   = $idxRefRisk !== false ? trim($row[$idxRefRisk] ?? '') : '';
                $descRiskRaw  = $idxDescRisk !== false ? trim($row[$idxDescRisk] ?? '') : '';
                $nilaiRiskRaw = $idxNilaiRisk !== false ? trim($row[$idxNilaiRisk] ?? '') : '';
                $fraudRiskRaw = $idxFraudRisk !== false ? trim($row[$idxFraudRisk] ?? '') : '';
                $keyCtrlRaw   = $idxKeyCtrl !== false ? trim($row[$idxKeyCtrl] ?? '') : '';

                $controlData = [
                    'control_description'  => $idxDesc !== false ? trim($row[$idxDesc] ?? '') : ($control->control_description ?? '-'),
                    'frequency'            => $idxFreq !== false ? trim($row[$idxFreq] ?? '') : ($control->frequency ?? null),
                    'supporting_documents' => $idxDoc !== false ? trim($row[$idxDoc] ?? '') : ($control->supporting_documents ?? null),
                    'control_objective'    => $idxObj !== false ? trim($row[$idxObj] ?? '') : ($control->control_objective ?? null),
                    'control_procedure'    => $idxProc !== false ? trim($row[$idxProc] ?? '') : ($control->control_procedure ?? null),
                ];

                if (!empty($refRiskRaw)) {
                    $controlData['risk_code'] = $refRiskRaw;
                }
                if (!empty($descRiskRaw)) {
                    $controlData['risk_description'] = $descRiskRaw;
                }
                if (!empty($nilaiRiskRaw)) {
                    $controlData['risk_value'] = $nilaiRiskRaw;
                }
                if (!empty($fraudRiskRaw)) {
                    $controlData['fraud_risk'] = $fraudRiskRaw;
                }
                if (!empty($keyCtrlRaw)) {
                    $controlData['is_key_control'] = (!str_contains(strtolower($keyCtrlRaw), 'non') && str_contains(strtolower($keyCtrlRaw), 'key'));
                }

                if (!empty($namaPic)) {
                    $controlData['executor'] = $namaPic;
                }
                if (!empty($jabatanPic)) {
                    $controlData['executor_position'] = $jabatanPic;
                }
                if (!empty($unitText)) {
                    $controlData['executor_unit'] = $unitText;
                }
                if ($unitId) {
                    $controlData['unit_id'] = $unitId;
                }
                if (!empty($executorFunction)) {
                    $controlData['executor_function'] = $executorFunction;
                }

                if ($processId) $controlData['icofr_process_id'] = $processId;
                if ($subProcessId) $controlData['icofr_sub_process_id'] = $subProcessId;

                if (!$control) {
                    $controlData['control_code'] = $controlCode;
                    if (empty($controlData['risk_code'])) {
                        $controlData['risk_code'] = 'R-' . $controlCode;
                    }
                    if (empty($controlData['risk_description'])) {
                        $controlData['risk_description'] = 'Risiko terkait ' . $controlCode;
                    }
                    if (!isset($controlData['is_key_control'])) {
                        $controlData['is_key_control'] = stripos($controlCode, 'C') === 0;
                    }
                    $controlData['executor'] = !empty($namaPic) ? $namaPic : null;
                    $controlData['executor_position'] = !empty($jabatanPic) ? $jabatanPic : null;
                    $controlData['executor_unit'] = !empty($unitText) ? $unitText : null;
                    $controlData['unit_id'] = $unitId;
                    $controlData['executor_function'] = $executorFunction;
                    
                    $control = \App\Models\IcofrControl::create($controlData);
                    $countNewControls++;
                } else {
                    $control->update($controlData);
                    $countUpdatedControls++;
                }

                // --- SINKRONISASI KEPALA DEPARTEMEN (JIKA ADA DI SHEET) ---
                if ($idxKepalaDept !== false && $idxNamaKepalaDept !== false) {
                    $rawTitle = trim($row[$idxKepalaDept] ?? '');
                    $rawName  = trim($row[$idxNamaKepalaDept] ?? '');
                    if (!empty($rawTitle) && !empty($rawName)) {
                        $matchedDept = $allDepartments->first(function($d) use ($rawTitle) {
                            $cleanSearch = strtolower(trim(str_ireplace(['GM of ', 'Dept. of ', 'Department of '], '', $rawTitle)));
                            return strtolower(trim((string)$d->leader_title)) === strtolower(trim($rawTitle))
                                || (!empty($cleanSearch) && str_contains(strtolower($d->name_dept), $cleanSearch));
                        });
                        if ($matchedDept && ($matchedDept->leader_name !== $rawName || $matchedDept->leader_title !== $rawTitle)) {
                            $matchedDept->update([
                                'leader_title' => $rawTitle,
                                'leader_name'  => $rawName,
                            ]);
                        }
                    }
                }

                // --- TAHAP C: SINKRONISASI HASIL PELAPORAN CSA ---
                $hasilCsaRaw = strtolower(trim($row[$idxHasilCsa] ?? ''));
                if (!empty($hasilCsaRaw)) {
                    $hasilCsa = 'belum_dinilai';
                    $teksBersih = preg_replace('/[^a-z]/', '', $hasilCsaRaw);

                    if (str_contains($teksBersih, 'tidakada')) {
                        $hasilCsa = 'tidak_ada';
                    } elseif (str_contains($teksBersih, 'sedangberjalan')) {
                        $hasilCsa = 'sedang_berjalan';
                    } elseif (str_contains($teksBersih, 'tidakefektif') || str_contains($teksBersih, 'defisiensi')) {
                        $hasilCsa = 'tidak_efektif';
                    } elseif (str_contains($teksBersih, 'efektif')) {
                        $hasilCsa = 'efektif';
                    }

                    \App\Models\IcofrCsaReport::updateOrCreate(
                        [
                            'icofr_control_id' => $control->id,
                            'tahun'            => $request->tahun,
                            'triwulan'         => $request->triwulan,
                        ],
                        [
                            'hasil_csa'                => $hasilCsa,
                            'keterangan_tidak_efektif' => $idxKet !== false ? trim($row[$idxKet] ?? '') : null,
                            'root_cause'               => $idxRoot !== false ? trim($row[$idxRoot] ?? '') : null,
                            'referensi_kertas_kerja'   => $idxKertas !== false ? trim($row[$idxKertas] ?? '') : null,
                            'rencana_remediasi'        => $idxRemediasi !== false ? trim($row[$idxRemediasi] ?? '') : null,
                        ]
                    );
                    $countCsaUpdated++;
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', "Sinkronisasi Hebat berhasil! $countNewControls kontrol baru ditambahkan, $countUpdatedControls deskripsi kontrol diperbarui, dan $countCsaUpdated hasil CSA ditarik ke Dasbor.");

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            report($e);
            return back()->withErrors(['sheets_url' => 'Terjadi kesalahan sinkronisasi: ' . $e->getMessage()]);
        }
    }

    /**
     * Kirim data kontrol & hasil CSA langsung dari Web ke Google Sheets via Apps Script Webhook.
     */
    public function pushToGoogleSheets(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'webhook_url' => ['required', 'url'],
            'tahun'       => ['required', 'integer'],
            'triwulan'    => ['required', 'string'],
            'cycle_id'    => ['nullable', 'exists:icofr_cycles,id'],
        ]);

        $webhookUrl = $request->webhook_url;

        // Simpan webhook url ke database agar tersimpan permanen
        \App\Models\Setting::updateOrCreate(
            ['key' => 'icofr_sheets_webhook_url'],
            ['value' => $webhookUrl, 'description' => 'URL Webhook Google Apps Script aktif ICoFR RCM']
        );

        $controlsQuery = IcofrControl::with([
            'process.cycle',
            'subProcess',
            'unit',
            'csaReports' => function ($q) use ($request) {
                $q->where('tahun', $request->tahun)->where('triwulan', $request->triwulan);
            }
        ]);

        if ($request->filled('cycle_id')) {
            $controlsQuery->whereHas('process', function ($q) use ($request) {
                $q->where('icofr_cycle_id', $request->cycle_id);
            });
        }

        $controls = $controlsQuery->get();

        if ($controls->isEmpty()) {
            return back()->withErrors(['webhook_url' => 'Tidak ada data kontrol yang ditemukan untuk dikirim ke Spreadsheet.']);
        }

        // Urutkan data secara terstruktur dan alfabetis (A-Z):
        // 1. Kode Siklus (A, B, C, D, ...)
        // 2. Kode Proses (01, 02, ...)
        // 3. Kode Sub-Proses
        // 4. Kode Kontrol (CTR-01, CTR-02, ...)
        $controls = $controls->sort(function ($a, $b) {
            $cycleA = (string) ($a->process?->cycle?->code ?? '');
            $cycleB = (string) ($b->process?->cycle?->code ?? '');
            $cmpCycle = strnatcasecmp($cycleA, $cycleB);
            if ($cmpCycle !== 0) return $cmpCycle;

            $procA = (string) ($a->process?->code ?? '');
            $procB = (string) ($b->process?->code ?? '');
            $cmpProc = strnatcasecmp($procA, $procB);
            if ($cmpProc !== 0) return $cmpProc;

            $subProcA = (string) ($a->subProcess?->code ?? '');
            $subProcB = (string) ($b->subProcess?->code ?? '');
            $cmpSub = strnatcasecmp($subProcA, $subProcB);
            if ($cmpSub !== 0) return $cmpSub;

            $ctrlA = (string) ($a->control_code ?? '');
            $ctrlB = (string) ($b->control_code ?? '');
            $cmpCtrl = strnatcasecmp($ctrlA, $ctrlB);
            if ($cmpCtrl !== 0) return $cmpCtrl;

            return $a->id <=> $b->id;
        })->values();

        $formatCodeAndName = function (?string $code, ?string $name): string {
            $code = trim((string) $code);
            $name = trim((string) $name);

            if ($code === '' && $name === '') return '';
            if ($code === '') return $name;
            if ($name === '') return $code;

            // Jika nama sudah diawali kode diikuti tanda pemisah (titik/spasi/strip)
            if (preg_match('/^' . preg_quote($code, '/') . '[\.\s\-]/i', $name)) {
                return $name;
            }

            // Jika nama persis sama dengan kode
            if (strcasecmp($code, $name) === 0) {
                return $code;
            }

            return "{$code}. {$name}";
        };

        $csaLabels = [
            'efektif'         => 'Efektif',
            'tidak_efektif'   => 'Tidak Efektif',
            'tidak_ada'       => 'Tidak Ada Transaksi',
            'sedang_berjalan' => 'Sedang Berjalan',
            'belum_dinilai'   => 'Belum DInilai',
        ];

        $payloadRows = [];
        foreach ($controls as $ctrl) {
            $csa = $ctrl->csaReports->first();
            $cycle = $ctrl->process?->cycle;
            $proc = $ctrl->process;
            $subProc = $ctrl->subProcess;

            $siklusText    = $cycle ? $formatCodeAndName($cycle->code, $cycle->name) : '';
            $prosesText    = $proc ? $formatCodeAndName($proc->code, $proc->name) : '';
            $subProsesText = $subProc ? $formatCodeAndName($subProc->code, $subProc->name) : '';
            $unitName      = $ctrl->executor_unit ?: ($ctrl->unit?->name_unit ?: ($ctrl->executor_function ?: ''));
            $jabatanName   = $ctrl->executor_position ?: '';
            $picName       = $ctrl->executor ?: '';

            $payloadRows[] = [
                'siklus'                   => $siklusText,
                'proses'                   => $prosesText,
                'sub_proses'               => $subProsesText,
                'nama_pelaksana'           => $picName,
                'jabatan_pelaksana'        => $jabatanName,
                'unit_pelaksana'           => $unitName,
                'ref_risk'                 => $ctrl->risk_code ?? '',
                'deskripsi_risiko'         => $ctrl->risk_description ?? '',
                'nilai_risiko'             => $ctrl->risk_value ?? '',
                'risiko_kecurangan'        => $ctrl->fraud_risk ?? '',
                'ref_control'              => $ctrl->control_code ?? '',
                'deskripsi_kontrol'        => $ctrl->control_description ?? '',
                'key_non_key'              => $ctrl->is_key_control ? 'Key' : 'Non Key',
                'pelaksana_kontrol'        => $unitName,
                'nama_pic'                 => $picName,
                'frekuensi'                => $ctrl->frequency ?? '',
                'obyektif'                 => $ctrl->control_objective ?? '',
                'prosedur'                 => $ctrl->control_procedure ?? '',
                'dokumen'                  => $ctrl->supporting_documents ?? '',
                'hasil_csa'                => $csa ? ($csaLabels[$csa->hasil_csa] ?? $csa->hasil_csa) : 'Belum DInilai',
                'keterangan_tidak_efektif' => $csa?->keterangan_tidak_efektif ?? '',
                'root_cause'               => $csa?->root_cause ?? '',
                'kertas_kerja'             => $csa?->referensi_kertas_kerja ?? '',
                'remediasi'                => $csa?->rencana_remediasi ?? '',
            ];
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(60)
                ->post($webhookUrl, [
                    'action'   => 'sync_controls',
                    'tahun'    => (int) $request->tahun,
                    'triwulan' => $request->triwulan,
                    'data'     => $payloadRows,
                ]);

            if (!$response->successful()) {
                throw new \Exception("Server Google merespons error HTTP {$response->status()}. Pastikan Web App Anda di-deploy dengan akses 'Anyone'.");
            }

            $result = $response->json();
            if (isset($result['status']) && $result['status'] === 'error') {
                throw new \Exception($result['message'] ?? 'Terjadi kesalahan pada skrip Google Spreadsheet.');
            }

            $msg = $result['message'] ?? ('Berhasil mengirim ' . count($payloadRows) . ' kontrol langsung ke Google Spreadsheet!');
            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['webhook_url' => 'Gagal mengirim data ke Spreadsheet: ' . $e->getMessage()]);
        }
    }

    // =====================================================================
    //  EXPORT
    // =====================================================================
    public function exportExcel(Request $request)
    {
        $this->authorizeAdmin();

        [$year, $tw] = $this->resolvePeriod($request);

        $cycles      = IcofrCycle::with(['processes.controls'])->orderBy('code')->get();
        $summaryData = $this->buildSummary($cycles, $year, $tw); // kalkulasi sama persis dengan dasbor

        return Excel::download(new IcofrSummaryExport($summaryData, $year, $tw), "Laporan_ICoFR_{$tw}_{$year}.xlsx");
    }

    // =====================================================================
    //  HELPER PRIVATE
    // =====================================================================

    /**
     * Periode dasbor: pakai ?tahun & ?triwulan bila ada. Bila tidak ada, pakai periode
     * TERBARU yang sudah punya hasil CSA (bukan triwulan berjalan), supaya data yang
     * sudah diinput langsung tampil. Jika belum ada data sama sekali, pakai triwulan berjalan.
     */
    private function resolvePeriod(Request $request): array
    {
        $latest = IcofrCsaReport::where('hasil_csa', '!=', 'belum_dinilai')
            ->orderByDesc('tahun')->orderByDesc('triwulan')->first();

        $year = (int) $request->query('tahun', $latest->tahun ?? date('Y'));
        $tw   = strtoupper((string) $request->query('triwulan', ''));

        if (! in_array($tw, self::QUARTERS, true)) {
            $tw = $latest->triwulan ?? 'TW' . (int) ceil(date('n') / 3);
        }

        return [$year, $tw];
    }

    /**
     * Seluruh angka dasbor dan export dihitung di sini (satu sumber kebenaran).
     *
     * - Jumlah kontrol (Key / Non Key) : icofr_controls (hasil import RCM).
     * - Hasil CSA                       : icofr_csa_reports untuk tahun + triwulan terpilih.
     * - % CSA                           : key control yang sudah punya hasil / jumlah key control.
     * - Kolom (a)(b)(d)(e)              : proses bertanda selesai / jumlah proses dalam siklus.
     */
    private function buildSummary($cycles, int $tahun, string $triwulan): array
    {
        // SATU query untuk seluruh laporan periode ini: [control_id => hasil_csa]
        $reports = IcofrCsaReport::where('tahun', $tahun)
            ->where('triwulan', $triwulan)
            ->pluck('hasil_csa', 'icofr_control_id');

        $statusOf = fn($control) => $reports[$control->id] ?? 'belum_dinilai';
        $count    = fn($list, string $status) => $list->filter(fn($c) => $statusOf($c) === $status)->count();

        $summary = [];

        foreach ($cycles as $cycle) {
            $controls = $cycle->processes->flatMap(fn($p) => $p->controls);
            $keys     = $controls->where('is_key_control', true);

            $total      = $controls->count();
            $keyTotal   = $keys->count();
            $keyDinilai = $keyTotal - $count($keys, 'belum_dinilai');

            $processes = $cycle->processes;
            $pCount    = $processes->count();
            $pct = fn(string $field) => $pCount > 0
                ? (int) round($processes->filter(fn($p) => (bool) $p->{$field})->count() / $pCount * 100)
                : 0;

            $summary[] = (object) [
                'id'               => $cycle->id,
                'code'             => $cycle->code,
                'name'             => $cycle->name,

                'total_controls'   => $total,
                'key_controls'     => $keyTotal,
                'non_key_controls' => $total - $keyTotal,

                'efektif'          => $count($controls, 'efektif'),
                'tidak_efektif'    => $count($controls, 'tidak_efektif'),
                'tidak_ada'        => $count($controls, 'tidak_ada'),
                'in_progress'      => $count($controls, 'sedang_berjalan'),
                'belum_dimulai'    => $count($controls, 'belum_dinilai'),

                'key_efektif'       => $count($keys, 'efektif'),
                'key_tidak_efektif' => $count($keys, 'tidak_efektif'),
                'key_tidak_ada'     => $count($keys, 'tidak_ada'),
                'key_in_progress'   => $count($keys, 'sedang_berjalan'),
                'key_dinilai'       => $keyDinilai,

                'csa_progress'     => $keyTotal > 0 ? (int) round($keyDinilai / $keyTotal * 100) : 0,

                'dokumentasi_pct'  => $pct('dokumentasi_done'),
                'validasi_pct'     => $pct('validasi_done'),
                'bpm_pct'          => $pct('bpm_updated'),
                'rcm_pct'          => $pct('rcm_updated'),

                'tod_pct'          => null,
                'toe_pct'          => null,
                'rem_tod_pct'      => null,
                'rem_toe_pct'      => null,
            ];
        }

        return $summary;
    }

    /** Validasi kontrol + data CSA sekaligus. */
    private function validateControl(Request $request): array
    {
        return $request->validate([
            'icofr_process_id'     => ['required', 'exists:icofr_processes,id'],
            'icofr_sub_process_id' => [
                'nullable',
                Rule::exists('icofr_sub_processes', 'id')->where('icofr_process_id', $request->input('icofr_process_id')),
            ],
            'unit_id'              => ['nullable', 'exists:units,id'],
            'executor'             => ['nullable', 'string', 'max:255'],
            'executor_position'    => ['nullable', 'string', 'max:255'],
            'executor_unit'        => ['nullable', 'string', 'max:255'],
            'risk_code'            => ['required', 'string', 'max:30'],
            'risk_description'     => ['required', 'string'],
            'risk_value'           => ['nullable', 'string', 'max:50'],
            'fraud_risk'           => ['nullable', 'string', 'max:50'],
            'control_code'         => ['required', 'string', 'max:30'],
            'control_description'  => ['required', 'string'],
            'is_key_control'       => ['required', 'boolean'],

            'frequency'            => ['nullable', 'string', 'max:100'],
            'control_objective'    => ['nullable', 'string'],
            'control_procedure'    => ['nullable', 'string'],
            'supporting_documents' => ['nullable', 'string'],                // kolom text: jangan dibatasi 255

            // Hasil CSA per periode
            'tahun'                    => ['nullable', 'integer', 'between:2020,2100'],
            'triwulan'                 => ['nullable', Rule::in(self::QUARTERS)],
            'hasil_csa'                => ['nullable', Rule::in(array_keys(IcofrControl::HASIL_CSA))],
            'keterangan_tidak_efektif' => ['nullable', 'required_if:hasil_csa,tidak_efektif', 'string', 'max:2000'],
        ], [
            'keterangan_tidak_efektif.required_if' => 'Keterangan wajib diisi bila hasil CSA "Tidak efektif".',
        ]);
    }

    /** Simpan / perbarui hasil CSA satu kontrol untuk satu periode. */
    private function saveCsaReport(IcofrControl $control, array $data): void
    {
        if (empty($data['tahun']) || empty($data['triwulan'])) {
            return;
        }

        $hasil    = $data['hasil_csa'] ?? 'belum_dinilai';
        $keys     = ['icofr_control_id' => $control->id, 'tahun' => $data['tahun'], 'triwulan' => $data['triwulan']];
        $existing = IcofrCsaReport::where($keys)->first();

        // Jangan membuat baris "belum dinilai" kosong hanya karena kontrol diedit
        if ($hasil === 'belum_dinilai' && ! $existing) {
            return;
        }

        IcofrCsaReport::updateOrCreate($keys, [
            'hasil_csa'                => $hasil,
            'keterangan_tidak_efektif' => $hasil === 'tidak_efektif' ? ($data['keterangan_tidak_efektif'] ?? null) : null,
        ]);
    }

    private function isAdmin(): bool
    {
        return auth()->user()->hasAnyRole(self::ADMIN_ROLES);
    }

    private function authorizeAdmin(): void
    {
        abort_unless($this->isAdmin(), 403);
    }

    public function downloadCertificate(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'tingkat'         => 'required|in:GM,DIRBID',
            'triwulan'        => 'required|integer',
            'tahun'           => 'required|integer',
            'nama_pejabat'    => 'required|string',
            'jabatan_pejabat' => 'required|string',
            'unit_pejabat'    => 'required|string',
        ]);

        $tingkat  = $request->tingkat;
        $triwulan = 'TW' . $request->triwulan;
        $tahun    = (string) $request->tahun; 

        $templateName = $tingkat === 'GM' ? 'template_gm.docx' : 'template_dirbid.docx';
        $templatePath = storage_path("app/templates/{$templateName}");

        if (!file_exists($templatePath)) {
            return back()->withErrors(['Template Word tidak ditemukan di: ' . $templatePath]);
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        // --- 1. IDENTITAS UMUM (Berlaku untuk GM & DIRBID) ---
        $templateProcessor->setValue('nama', htmlspecialchars((string) $request->nama_pejabat, ENT_XML1, 'UTF-8'));
        $templateProcessor->setValue('jabatan', htmlspecialchars((string) $request->jabatan_pejabat, ENT_XML1, 'UTF-8'));
        $templateProcessor->setValue('unit', htmlspecialchars((string) $request->unit_pejabat, ENT_XML1, 'UTF-8'));
        $templateProcessor->setValue('tw', (string) $request->triwulan);
        $templateProcessor->setValue('tahun', $tahun);
        
        \Illuminate\Support\Carbon::setLocale('id');
        $templateProcessor->setValue('tanggal', \Illuminate\Support\Carbon::now()->translatedFormat('d F Y'));

        $cycles = \App\Models\IcofrCycle::with(['processes.controls.unit'])->get();
        $reports = \App\Models\IcofrCsaReport::where('tahun', $request->tahun)
            ->where('triwulan', $triwulan)
            ->get()
            ->keyBy('icofr_control_id');

        // =====================================================================
        // JIKA YANG DICETAK ADALAH GENERAL MANAGER (GM)
        // =====================================================================
        if ($tingkat === 'GM') {
            $uniquePics = []; 
            $table1Data = []; 
            $table2Data = []; 

            foreach ($cycles as $cycle) {
                $controls = $cycle->processes->flatMap(fn($p) => $p->controls);
                foreach ($controls as $ctrl) {
                    $namaPIC = !empty(trim((string)$ctrl->executor)) ? trim((string)$ctrl->executor) : '-';
                    $jabatanPIC = $ctrl->unit ? $ctrl->unit->name_unit : (!empty(trim((string)$ctrl->executor_function)) ? trim((string)$ctrl->executor_function) : '-');
                    $siklusName = $cycle->name;
                    $status = isset($reports[$ctrl->id]) ? $reports[$ctrl->id]->hasil_csa : 'belum_dinilai';
                    
                    $picKey = $namaPIC . '|' . $jabatanPIC;
                    if (!isset($uniquePics[$picKey])) {
                        $uniquePics[$picKey] = ['pic' => $namaPIC, 'jabatan' => $jabatanPIC];
                    }

                    $key = $picKey . '|' . $siklusName;
                    if (in_array($status, ['efektif', 'tidak_efektif', 'tidak_ada', 'sedang_berjalan'])) {
                        if (!isset($table1Data[$key])) {
                            $table1Data[$key] = ['pic' => $namaPIC, 'jabatan' => $jabatanPIC, 'siklus' => $siklusName, 'efektif' => 0, 'tidak_efektif' => 0, 'tidak_ada' => 0, 'total' => 0];
                        }
                        if ($status === 'efektif') $table1Data[$key]['efektif']++;
                        if ($status === 'tidak_efektif') $table1Data[$key]['tidak_efektif']++;
                        if ($status === 'tidak_ada') $table1Data[$key]['tidak_ada']++;
                        $table1Data[$key]['total']++;
                    } else {
                        if (!isset($table2Data[$key])) {
                            $table2Data[$key] = ['pic' => $namaPIC, 'jabatan' => $jabatanPIC, 'siklus' => $siklusName, 'total' => 0];
                        }
                        $table2Data[$key]['total']++;
                    }
                }
            }

            // Clone Tabel 0 (Halaman Depan)
            $values0 = array_values($uniquePics);
            if (count($values0) > 0) {
                $templateProcessor->cloneRow('no0', count($values0));
                foreach ($values0 as $index => $row) {
                    $i = $index + 1;
                    $templateProcessor->setValue("no0#{$i}", (string)$i);
                    $templateProcessor->setValue("pic0#{$i}", htmlspecialchars($row['pic'], ENT_XML1, 'UTF-8'));
                    $templateProcessor->setValue("jab0#{$i}", htmlspecialchars($row['jabatan'], ENT_XML1, 'UTF-8'));
                }
            } else {
                $templateProcessor->cloneRow('no0', 1);
                $templateProcessor->setValue("no0#1", "-");
                $templateProcessor->setValue("pic0#1", "-");
                $templateProcessor->setValue("jab0#1", "-");
            }

            // Clone Tabel 1 (Dikerjakan)
            $values1 = array_values($table1Data);
            if (count($values1) > 0) {
                $templateProcessor->cloneRow('no1', count($values1));
                foreach ($values1 as $index => $row) {
                    $i = $index + 1;
                    $templateProcessor->setValue("no1#{$i}", (string)$i);
                    $templateProcessor->setValue("pic1#{$i}", htmlspecialchars($row['pic'], ENT_XML1, 'UTF-8'));
                    $templateProcessor->setValue("jab1#{$i}", htmlspecialchars($row['jabatan'], ENT_XML1, 'UTF-8'));
                    $templateProcessor->setValue("sik1#{$i}", htmlspecialchars($row['siklus'], ENT_XML1, 'UTF-8'));
                    $templateProcessor->setValue("efek1#{$i}", (string)$row['efektif']);
                    $templateProcessor->setValue("tefek1#{$i}", (string)$row['tidak_efektif']);
                    $templateProcessor->setValue("tada1#{$i}", (string)$row['tidak_ada']);
                    $templateProcessor->setValue("tot1#{$i}", (string)$row['total']);
                }
            } else {
                // Tambahan agar aman jika Tabel 1 kosong
                $templateProcessor->cloneRow('no1', 1);
                $templateProcessor->setValue("no1#1", "-");
                $templateProcessor->setValue("pic1#1", "-");
                $templateProcessor->setValue("jab1#1", "-");
                $templateProcessor->setValue("sik1#1", "-");
                $templateProcessor->setValue("efek1#1", "0");
                $templateProcessor->setValue("tefek1#1", "0");
                $templateProcessor->setValue("tada1#1", "0");
                $templateProcessor->setValue("tot1#1", "0");
            }

            // Clone Tabel 2 (Belum Dikerjakan)
            $values2 = array_values($table2Data);
            if (count($values2) > 0) {
                $templateProcessor->cloneRow('no2', count($values2));
                foreach ($values2 as $index => $row) {
                    $i = $index + 1;
                    $templateProcessor->setValue("no2#{$i}", (string)$i);
                    $templateProcessor->setValue("pic2#{$i}", htmlspecialchars($row['pic'], ENT_XML1, 'UTF-8'));
                    $templateProcessor->setValue("jab2#{$i}", htmlspecialchars($row['jabatan'], ENT_XML1, 'UTF-8'));
                    $templateProcessor->setValue("sik2#{$i}", htmlspecialchars($row['siklus'], ENT_XML1, 'UTF-8'));
                    $templateProcessor->setValue("tot2#{$i}", (string)$row['total']);
                }
            } else {
                // PERBAIKAN: Buat 1 baris dummy jika tidak ada hasil CSA yang belum dikerjakan
                $templateProcessor->cloneRow('no2', 1);
                $templateProcessor->setValue("no2#1", "-");
                $templateProcessor->setValue("pic2#1", "-");
                $templateProcessor->setValue("jab2#1", "-");
                $templateProcessor->setValue("sik2#1", "-");
                $templateProcessor->setValue("tot2#1", "0");
            }

            // Total Baris Bawah GM
            $templateProcessor->setValue('tot_efek1', (string)array_sum(array_column($table1Data, 'efektif')));
            $templateProcessor->setValue('tot_tefek1', (string)array_sum(array_column($table1Data, 'tidak_efektif')));
            $templateProcessor->setValue('tot_tada1', (string)array_sum(array_column($table1Data, 'tidak_ada')));
            $templateProcessor->setValue('tot_all1', (string)array_sum(array_column($table1Data, 'total')));
            $templateProcessor->setValue('tot_all2', (string)array_sum(array_column($table2Data, 'total')));
        } 
        
        // =====================================================================
        // JIKA YANG DICETAK ADALAH DIREKTUR BIDANG (DIRBID)
        // =====================================================================
        elseif ($tingkat === 'DIRBID') {
            $table1Dir = []; 
            $table2Dir = []; 

            foreach ($cycles as $cycle) {
                $siklusName = $cycle->name;
                $table1Dir[$siklusName] = ['siklus' => $siklusName, 'efektif' => 0, 'tidak_efektif' => 0, 'tidak_ada' => 0, 'total' => 0];
                $table2Dir[$siklusName] = ['siklus' => $siklusName, 'total' => 0];

                $controls = $cycle->processes->flatMap(fn($p) => $p->controls);
                foreach ($controls as $ctrl) {
                    $status = isset($reports[$ctrl->id]) ? $reports[$ctrl->id]->hasil_csa : 'belum_dinilai';
                    
                    if (in_array($status, ['efektif', 'tidak_efektif', 'tidak_ada', 'sedang_berjalan'])) {
                        if ($status === 'efektif') $table1Dir[$siklusName]['efektif']++;
                        if ($status === 'tidak_efektif') $table1Dir[$siklusName]['tidak_efektif']++;
                        if ($status === 'tidak_ada') $table1Dir[$siklusName]['tidak_ada']++;
                        $table1Dir[$siklusName]['total']++;
                    } else {
                        $table2Dir[$siklusName]['total']++;
                    }
                }
            }

            // Clone Tabel 1 (Dirbid)
            $values1 = array_values($table1Dir);
            $templateProcessor->cloneRow('sik_d', count($values1));
            foreach ($values1 as $index => $row) {
                $i = $index + 1;
                $templateProcessor->setValue("sik_d#{$i}", htmlspecialchars($row['siklus'], ENT_XML1, 'UTF-8'));
                $templateProcessor->setValue("efek_d#{$i}", (string)$row['efektif']);
                $templateProcessor->setValue("tefek_d#{$i}", (string)$row['tidak_efektif']);
                $templateProcessor->setValue("tada_d#{$i}", (string)$row['tidak_ada']);
                $templateProcessor->setValue("tot_d#{$i}", (string)$row['total']);
            }

            // Clone Tabel 2 (Dirbid)
            $values2 = array_values($table2Dir);
            $templateProcessor->cloneRow('sik_t', count($values2));
            foreach ($values2 as $index => $row) {
                $i = $index + 1;
                $templateProcessor->setValue("sik_t#{$i}", htmlspecialchars($row['siklus'], ENT_XML1, 'UTF-8'));
                $templateProcessor->setValue("tot_t#{$i}", (string)$row['total']);
            }
            
            // Total Baris Bawah Dirbid
            $templateProcessor->setValue('tot_all_efek', (string)array_sum(array_column($table1Dir, 'efektif')));
            $templateProcessor->setValue('tot_all_tefek', (string)array_sum(array_column($table1Dir, 'tidak_efektif')));
            $templateProcessor->setValue('tot_all_tada', (string)array_sum(array_column($table1Dir, 'tidak_ada')));
            $templateProcessor->setValue('tot_all_d', (string)array_sum(array_column($table1Dir, 'total')));
            $templateProcessor->setValue('tot_all_t', (string)array_sum(array_column($table2Dir, 'total')));
        }

        // --- PENGIRIMAN FILE BINER ANTI-CORRUPT ---
        $safeFileName = preg_replace('/[^A-Za-z0-9\-]/', '_', $request->nama_pejabat);
        $fileName = "Sertifikasi_{$tingkat}_{$triwulan}_{$tahun}_{$safeFileName}.docx";
        
        $tempFile = storage_path('app/temp_' . uniqid() . '.docx');
        $templateProcessor->saveAs($tempFile);

        while (ob_get_level() > 0) { ob_end_clean(); }

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($tempFile));
        
        readfile($tempFile);
        @unlink($tempFile);
        exit;
    }

    // =====================================================================
    //  MASTER PEJABAT (GM & DIREKSI)
    // =====================================================================
    public function updateOfficials(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'departments' => 'nullable|array',
            'directors'   => 'nullable|array',
        ]);

        if ($request->has('departments') && is_array($request->departments)) {
            foreach ($request->departments as $deptId => $data) {
                $dept = \App\Models\Department::find($deptId);
                if ($dept) {
                    $dept->update([
                        'leader_title' => !empty(trim($data['leader_title'] ?? '')) ? trim($data['leader_title']) : $dept->leader_title,
                        'leader_name'  => isset($data['leader_name']) ? trim($data['leader_name']) : null,
                        'is_icofr_pic' => !empty($data['is_icofr_pic']),
                    ]);
                }
            }
        }

        if ($request->has('directors') && is_array($request->directors)) {
            $directors = [];
            foreach ($request->directors as $item) {
                if (!empty($item['jabatan'])) {
                    $directors[] = [
                        'jabatan'    => trim($item['jabatan']),
                        'direktorat' => trim($item['direktorat'] ?? $item['jabatan']),
                        'nama'       => trim($item['nama'] ?? ''),
                    ];
                }
            }
            if (count($directors) > 0) {
                \App\Models\Setting::setDirectors($directors);
            }
        }

        return back()->with('success', 'Data pejabat (GM & Direksi) berhasil diperbarui!');
    }

    // =====================================================================
    //  MANAJEMEN MASTER PIC PELAKSANA KONTROL
    // =====================================================================

    /**
     * Mengambil daftar master PIC (bisa via AJAX / JSON).
     */
    public function getPics(Request $request)
    {
        $this->authorizeAdmin();

        $query = \App\Models\IcofrPic::query();

        if ($request->filled('q')) {
            $query->search($request->q);
        }
        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        $pics = $query->orderBy('unit')->orderBy('position')->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data'   => $pics,
            ]);
        }

        return response()->json($pics);
    }

    /**
     * Menambahkan data PIC Pelaksana Kontrol baru.
     */
    public function storePic(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name'       => ['nullable', 'string', 'max:255'],
            'position'   => ['required', 'string', 'max:255'],
            'unit'       => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:50'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ]);

        $pic = \App\Models\IcofrPic::create($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'PIC Pelaksana Kontrol berhasil ditambahkan.',
                'data'    => $pic,
            ]);
        }

        return back()->with('success', 'PIC Pelaksana Kontrol "' . $pic->position . '" berhasil ditambahkan.');
    }

    /**
     * Memperbarui data PIC Pelaksana Kontrol.
     */
    public function updatePic(Request $request, $id)
    {
        $this->authorizeAdmin();

        $pic = \App\Models\IcofrPic::findOrFail($id);

        $data = $request->validate([
            'name'       => ['nullable', 'string', 'max:255'],
            'position'   => ['required', 'string', 'max:255'],
            'unit'       => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:50'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ]);

        $pic->update($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Data PIC berhasil diperbarui.',
                'data'    => $pic,
            ]);
        }

        return back()->with('success', 'Data PIC Pelaksana Kontrol berhasil diperbarui.');
    }

    /**
     * Menghapus data PIC Pelaksana Kontrol.
     */
    public function destroyPic(Request $request, $id)
    {
        $this->authorizeAdmin();

        $pic = \App\Models\IcofrPic::findOrFail($id);
        $name = $pic->position;
        $pic->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Data PIC berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Data PIC "' . $name . '" berhasil dihapus.');
    }

    /**
     * Kirim list PIC ke Google Sheets webhook agar dibuatkan / diupdate sheet 'Master_PIC'.
     */
    public function pushPicsToGoogleSheets(Request $request)
    {
        $this->authorizeAdmin();

        $webhookUrl = \App\Models\Setting::where('key', 'icofr_sheets_webhook_url')->value('value') ?? '';
        if (empty($webhookUrl)) {
            return back()->withErrors(['webhook_url' => 'URL Webhook Google Apps Script belum dikonfigurasi.']);
        }

        $pics = \App\Models\IcofrPic::orderBy('unit')->orderBy('position')->get();
        $payload = $pics->map(fn($p, $idx) => [
            'no'         => $idx + 1,
            'name'       => $p->name ?? '',
            'position'   => $p->position ?? '',
            'unit'       => $p->unit ?? '',
            'department' => $p->department ?? '',
        ])->toArray();

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)->post($webhookUrl, [
                'action' => 'sync_pics',
                'data'   => $payload,
            ]);

            if (!$response->successful()) {
                throw new \Exception("Server Google merespons error HTTP {$response->status()}.");
            }

            $result = $response->json();
            if (isset($result['status']) && $result['status'] === 'error') {
                throw new \Exception($result['message'] ?? 'Terjadi kesalahan pada skrip Google Spreadsheet.');
            }

            return back()->with('success', $result['message'] ?? 'Master PIC berhasil disinkronisasi ke Google Spreadsheet (Sheet: Master_PIC)!');
        } catch (\Throwable $e) {
            return back()->withErrors(['webhook_error' => 'Gagal push ke spreadsheet: ' . $e->getMessage()]);
        }
    }
}
