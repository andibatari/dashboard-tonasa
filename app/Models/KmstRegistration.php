<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KmstRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'kategori', 'nama_tim', 'data_peserta', 'bidang_benefit', 'file_makalah'
    ];

    protected $casts = [
        'bidang_benefit' => 'array', // Mengubah JSON dari database menjadi Array otomatis
        'data_peserta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}