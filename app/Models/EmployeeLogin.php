<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

class EmployeeLogin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; 

    protected $table = 'employee_logins';

    protected $fillable = [
        'karyawan_id',
        'no_sap', 
        'password',
        'fcm_token', // 🌟 Diizinkan mass assignment
    ];

    protected $hidden = [
        'password',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}