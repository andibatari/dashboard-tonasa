<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalDokter extends Model
{
    use HasFactory;

    // Pastikan field ini bisa diisi
    protected $fillable = [
        'dokter_id',
        'tanggal',
    ];

    // INI YANG PALING PENTING AGAR ->with('dokter') TIDAK ERROR
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }
}