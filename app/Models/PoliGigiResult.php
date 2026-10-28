<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoliGigiResult extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'data_pemeriksaan' => 'array',
        
    ];

    public function jadwalPoli()
    {
        return $this->belongsTo(JadwalPoli::class, 'jadwal_poli_id');
    }
    // Buat relasi ke tabel dokter
    public function dokter()
    {
        return $this->belongsTo(\App\Models\User::class, 'dokter_id'); // Sesuaikan dengan model dokter Anda
    }

}