<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; 

class PesertaMcuLogin extends Authenticatable
{
    use HasFactory, HasApiTokens; 

    protected $table = 'peserta_mcu_logins';

    protected $fillable = [
        'peserta_mcu_id',
        'nik_pasien',
        'password',
        'fcm_token', // 🌟 Diizinkan mass assignment
    ];

    protected $hidden = [
        'password',
    ];

    public function pasien()
    {
        return $this->belongsTo(PesertaMcu::class, 'peserta_mcu_id');
    }
}