<?php

namespace App\Services;

use App\Models\IcofrControl;
use App\Models\IcofrCycle;
use App\Models\IcofrProcess;
use App\Models\IcofrSubProcess;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Membaca file RCM (format "CRM GABUNG": 3 baris header, 1 baris = 1 kontrol)
 * lalu menyimpannya ke icofr_cycles > icofr_processes > icofr_sub_processes > icofr_controls.
 *
 * Aman diunggah ulang: data dicocokkan lewat row_key, sehingga status CSA
 * dan status dokumen proses yang sudah diisi tidak hilang.
 */
class RcmImportService
{
    /** Indeks kolom (mulai 0) pada sheet "CRM GABUNG". */
    private const COL = [
        'cycle' => 1, 'proc_ref' => 2, 'proc_name' => 3, 'sub_ref' => 4, 'sub_name' => 5,
        'company' => 7, 'location' => 8,
        'risk_code' => 9, 'risk_desc' => 10, 'risk_quant' => 11, 'risk_qual' => 12, 'risk_value' => 13, 'fraud' => 14,
        'ctrl_code' => 15, 'ctrl_desc' => 16, 'exec_fn' => 17, 'executor' => 18, 'timing' => 19,
        'nature' => 20, 'automation' => 21, 'key' => 22, 'frequency' => 23,
        'info_c' => 24, 'info_a' => 25, 'info_v' => 26, 'info_ra' => 27,
        'asr_c' => 28, 'asr_a' => 29, 'asr_eo' => 30, 'asr_co' => 31, 'asr_ro' => 32, 'asr_va' => 33, 'asr_pd' => 34,
        'app' => 35, 'fs_item' => 36, 'effective' => 37, 'attribute' => 38, 'docs' => 39, 'objective' => 40, 'procedure' => 41,
        'remarks' => 42,
    ];

    /** Nama siklus di Excel (kata kunci) => kode & nama siklus di sistem. */
    private const CYCLES = [
        'pendapatan'  => ['A', 'Pendapatan'],
        'persediaan'  => ['B', 'Persediaan & Produksi'],
        'gaji'        => ['C', 'Beban Gaji'],
        'penggajian'  => ['C', 'Beban Gaji'],
        'aset'        => ['D', 'Aset Tetap'],
        'bendahara'   => ['E', 'Perbendaharaan'],
        'perbendahara'=> ['E', 'Perbendaharaan'],
        'pajak'       => ['F', 'Perpajakan'],
        'pelaporan'   => ['G', 'Pelaporan Keuangan'],
    ];

    /** @return array{controls:int, created:int, updated:int, processes:int, skipped:array<int,string>} */
    public function import(string $path): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $book = $reader->load($path);

        $sheet = $book->getSheetByName('CRM GABUNG') ?? $book->getSheet(0);
        $rows  = $sheet->toArray(null, false, false, false);

        $stats = ['controls' => 0, 'created' => 0, 'updated' => 0, 'processes' => 0, 'skipped' => []];

        $cycleCache = $procCache = $subCache = $occurrence = [];

        DB::transaction(function () use ($rows, &$stats, &$cycleCache, &$procCache, &$subCache, &$occurrence) {
            foreach ($rows as $i => $r) {
                if ($i < 3) {
                    continue; // 3 baris header
                }

                $ctrlCode = $this->text($r[self::COL['ctrl_code']] ?? null);
                $cycleRaw = $this->text($r[self::COL['cycle']] ?? null);
                if ($ctrlCode === null || $cycleRaw === null) {
                    continue; // baris kosong / catatan kaki
                }

                $cycle = $this->resolveCycle($cycleRaw, $cycleCache);
                if (! $cycle) {
                    $stats['skipped'][] = 'Baris ' . ($i + 1) . ": siklus \"{$cycleRaw}\" tidak dikenali";
                    continue;
                }

                // ---- Proses (kunci: siklus + nama) ----
                $procName = $this->text($r[self::COL['proc_name']] ?? null) ?? 'Tanpa proses';
                $pKey     = $cycle->id . '|' . Str::lower($procName);
                if (! isset($procCache[$pKey])) {
                    $ref = $this->number($r[self::COL['proc_ref']] ?? null);
                    $proc = IcofrProcess::firstOrCreate(
                        ['icofr_cycle_id' => $cycle->id, 'name' => Str::limit($procName, 250, '')],
                        ['code' => sprintf('%02d', $ref ?: (IcofrProcess::where('icofr_cycle_id', $cycle->id)->count() + 1))]
                    );
                    if ($proc->wasRecentlyCreated) {
                        $stats['processes']++;
                    }
                    $procCache[$pKey] = $proc;
                }
                $proc = $procCache[$pKey];

                // ---- Sub proses (kunci: proses + nama; kode dibuat berurutan agar tidak bentrok) ----
                $sub     = null;
                $subName = $this->text($r[self::COL['sub_name']] ?? null);
                if ($subName !== null) {
                    $sKey = $proc->id . '|' . Str::lower($subName);
                    if (! isset($subCache[$sKey])) {
                        $existing = IcofrSubProcess::where('icofr_process_id', $proc->id)->where('name', Str::limit($subName, 250, ''))->first();
                        $subCache[$sKey] = $existing ?? IcofrSubProcess::create([
                            'icofr_process_id' => $proc->id,
                            'code'             => sprintf('%02d', IcofrSubProcess::where('icofr_process_id', $proc->id)->count() + 1),
                            'name'             => Str::limit($subName, 250, ''),
                        ]);
                    }
                    $sub = $subCache[$sKey];
                }

                // ---- Kontrol ----
                $riskCode = $this->text($r[self::COL['risk_code']] ?? null);
                $base     = implode('|', [$cycle->id, $proc->id, $sub?->id, $riskCode, Str::lower($ctrlCode)]);
                $occurrence[$base] = ($occurrence[$base] ?? 0) + 1;       // baris ganda dengan kunci sama tetap tersimpan
                $rowKey = md5($base . '|' . $occurrence[$base]);

                $key = Str::lower((string) $this->text($r[self::COL['key']] ?? null));

                $data = [
                    'icofr_process_id'     => $proc->id,
                    'icofr_sub_process_id' => $sub?->id,
                    'company'              => $this->text($r[self::COL['company']] ?? null),
                    'location'             => $this->text($r[self::COL['location']] ?? null),
                    'risk_code'            => $riskCode,
                    'risk_description'     => $this->text($r[self::COL['risk_desc']] ?? null),
                    'risk_quantitative'    => $this->text($r[self::COL['risk_quant']] ?? null),
                    'risk_qualitative'     => $this->text($r[self::COL['risk_qual']] ?? null),
                    'risk_value'           => $this->text($r[self::COL['risk_value']] ?? null),
                    'fraud_risk'           => $this->text($r[self::COL['fraud']] ?? null),
                    'control_code'         => Str::limit($ctrlCode, 10, ''),
                    'control_description'  => $this->text($r[self::COL['ctrl_desc']] ?? null),
                    'executor_function'    => $this->text($r[self::COL['exec_fn']] ?? null),
                    'executor'             => $this->text($r[self::COL['executor']] ?? null),
                    'control_timing'       => $this->text($r[self::COL['timing']] ?? null),
                    'control_nature'       => $this->text($r[self::COL['nature']] ?? null),
                    'automation_type'      => $this->text($r[self::COL['automation']] ?? null),
                    'is_key_control'       => $key === 'key',
                    'frequency'            => $this->text($r[self::COL['frequency']] ?? null),
                    'supporting_application' => $this->text($r[self::COL['app']] ?? null),
                    'fs_item'              => $this->text($r[self::COL['fs_item']] ?? null),
                    'effective_date'       => $this->date($r[self::COL['effective']] ?? null),
                    'control_attribute'    => $this->text($r[self::COL['attribute']] ?? null),
                    'supporting_documents' => $this->text($r[self::COL['docs']] ?? null),
                    'control_objective'    => $this->text($r[self::COL['objective']] ?? null),
                    'control_procedure'    => $this->text($r[self::COL['procedure']] ?? null),
                    'remarks'              => $this->text($r[self::COL['remarks']] ?? null),
                ];
                foreach (['info_c', 'info_a', 'info_v', 'info_ra', 'asr_c', 'asr_a', 'asr_eo', 'asr_co', 'asr_ro', 'asr_va', 'asr_pd'] as $flag) {
                    $data[$flag] = $this->flag($r[self::COL[$flag]] ?? null);
                }

                if ($this->text($r[self::COL['key']] ?? null) === null) {
                    $stats['skipped'][] = 'Baris ' . ($i + 1) . ": kontrol {$ctrlCode} tidak punya status Key/Non Key (disimpan sebagai Non Key)";
                }

                // csa_status sengaja tidak ikut ditimpa saat unggah ulang
                $control = IcofrControl::updateOrCreate(['row_key' => $rowKey], $data);
                $control->wasRecentlyCreated ? $stats['created']++ : $stats['updated']++;
                $stats['controls']++;
            }
        });

        return $stats;
    }

    private function resolveCycle(string $raw, array &$cache): ?IcofrCycle
    {
        $needle = Str::lower($raw);
        foreach (self::CYCLES as $keyword => [$code, $name]) {
            if (str_contains($needle, $keyword)) {
                return $cache[$code] ??= IcofrCycle::firstOrCreate(['code' => $code], ['name' => $name]);
            }
        }

        return null;
    }

    private function text($v): ?string
    {
        if ($v === null) {
            return null;
        }
        $s = trim((string) $v);

        return $s === '' ? null : $s;
    }

    private function number($v): int
    {
        return is_numeric($v) ? (int) $v : 0;
    }

    private function flag($v): bool
    {
        return in_array(Str::lower(trim((string) $v)), ['v', 'x', '✓', '1', 'ya', 'yes'], true);
    }

    private function date($v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        try {
            if (is_numeric($v)) {
                return Date::excelToDateTimeObject((float) $v)->format('Y-m-d');
            }

            return Carbon::parse((string) $v)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
