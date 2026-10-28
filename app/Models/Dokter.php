<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nik',
        'nama_lengkap',
        'spesialisasi',
        'tanggal_lahir',
        'golongan_darah',
        'no_hp',
        'email',
        'password',
        'role',
        'color',
    ];

    // Relasi ke tabel admin_users
    public function adminUser()
    {
        return $this->hasOne(AdminUser::class, 'dokter_id');
    }
}