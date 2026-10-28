<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KebugaranResult extends Model
{
    use HasFactory;

    // Nama tabel yang akan digunakan
    protected $table = 'kebugaran_results';

    // Kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'jadwal_poli_id',
        // 'poli_id', // Tambahkan poli_id untuk memudahkan filter
        'vo2_max',
        'durasi_menit',
        'beban_latihan',
        'denyut_nadi',
        'indeks_kebugaran',
        'kategori',
    ];

    /**
     * Relasi ke Jadwal MCU.
     */
    public function jadwalPoli()
    {
        return $this->belongsTo(JadwalPoli::class, 'jadwal_poli_id');
    }
}
