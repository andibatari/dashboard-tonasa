<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PemantauanLingkungan extends Model
{
    use HasFactory;

    protected $fillable = [
        'departemens_id', // BARU
        'unit_kerjas_id', // BARU
        'area',
        'lokasi',
        'tanggal_pemantauan',
        'nab_cahaya',
        'nab_bising',
        'nab_debu',
        'nab_suhu', // DIPINDAHKAN DARI JSON
        'data_pemantauan', // Data Pengukuran Lain
        'kesimpulan', // BARU
    ];

    protected $casts = [
        'data_pemantauan' => 'json',
    ];

    // Relasi untuk mendapatkan nama Departemen dan Unit Kerja
    public function departemen()
    {
        // Sesuaikan dengan nama Model Departemen Anda
        return $this->belongsTo(Departemen::class, 'departemens_id'); 
    }

    public function unitKerja()
    {
        // Sesuaikan dengan nama Model UnitKerja Anda
        return $this->belongsTo(UnitKerja::class, 'unit_kerjas_id');
    }
}
