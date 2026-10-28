<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IcofrControl extends Model
{
    /** Pilihan hasil CSA (dipakai form, validasi, dan dasbor). */
    public const HASIL_CSA = [
        'belum_dinilai' => 'Belum dinilai',
        'in_progress'   => 'Sedang berjalan',
        'efektif'       => 'Efektif',
        'tidak_efektif' => 'Tidak efektif (defisiensi)',
        'tidak_ada'     => 'Tidak ada transaksi',
    ];

    protected $fillable = [
        'icofr_process_id', 'icofr_sub_process_id', 'unit_id', 'row_key',
        'company', 'location',
        'risk_code', 'risk_description', 'risk_quantitative', 'risk_qualitative', 'risk_value', 'fraud_risk',
        'control_code', 'control_description', 'executor_function', 'executor', 'executor_position', 'executor_unit',
        'control_timing', 'control_nature', 'automation_type', 'is_key_control',
        'frequency',
        'info_c', 'info_a', 'info_v', 'info_ra',
        'asr_c', 'asr_a', 'asr_eo', 'asr_co', 'asr_ro', 'asr_va', 'asr_pd',
        'supporting_application', 'fs_item', 'effective_date',
        'control_attribute', 'supporting_documents', 'control_objective', 'control_procedure', 'remarks',
    ];

    protected $casts = [
        'is_key_control' => 'boolean',
        'info_c' => 'boolean', 'info_a' => 'boolean', 'info_v' => 'boolean', 'info_ra' => 'boolean',
        'asr_c' => 'boolean', 'asr_a' => 'boolean', 'asr_eo' => 'boolean', 'asr_co' => 'boolean',
        'asr_ro' => 'boolean', 'asr_va' => 'boolean', 'asr_pd' => 'boolean',
        'effective_date' => 'date',
    ];

    public function process()
    {
        return $this->belongsTo(IcofrProcess::class, 'icofr_process_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function subProcess()
    {
        return $this->belongsTo(IcofrSubProcess::class, 'icofr_sub_process_id');
    }

    public function csaReports()
    {
        return $this->hasMany(IcofrCsaReport::class, 'icofr_control_id');
    }
}
