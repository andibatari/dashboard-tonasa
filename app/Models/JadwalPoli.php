<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPoli extends Model
{
    use HasFactory;

    protected $fillable = [
        'jadwal_mcus_id',
        'poli_id',
        'status',
        'no_antrean_poli',
    ];

    // REVISI DI SINI: Tambahkan relasi ke JadwalMcu
    public function jadwalMcu()
    {
        return $this->belongsTo(JadwalMcu::class, 'jadwal_mcus_id');
    }

    // REVISI DI SINI: Tambahkan relasi ke Poli
    public function poli()
    {
        return $this->belongsTo(Poli::class, 'poli_id');
    }
}