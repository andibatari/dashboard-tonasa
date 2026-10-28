<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FisikResult extends Model
{
    use HasFactory;

    protected $table = 'fisik_results';

    protected $fillable = [
        'jadwal_poli_id',
        'dokter_id',
        'data_fisik', // Kolom JSON untuk menyimpan semua data pemeriksaan
        'kesimpulan',
        'keterangan',
        'file_path',
    ];

    protected $casts = [
        'data_fisik' => 'array',
    ];

    public function jadwalPoli()
    {
        return $this->belongsTo(JadwalPoli::class, 'jadwal_poli_id');
    }

    public function dokter()
    {
        // Sesuaikan dengan nama model Dokter Anda
        return $this->belongsTo(Dokter::class, 'dokter_id'); 
    }
}
