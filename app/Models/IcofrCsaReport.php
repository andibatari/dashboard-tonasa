<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IcofrCsaReport extends Model
{
    protected $fillable = [
        'icofr_control_id', 'tahun', 'triwulan', 'hasil_csa', 
        'keterangan_tidak_efektif', 'root_cause', 'referensi_kertas_kerja', 
        'rencana_remediasi', 'status_remediasi'
    ];

    // Relasi balik ke tabel master control
    public function control()
    {
        return $this->belongsTo(IcofrControl::class, 'icofr_control_id');
    }
}