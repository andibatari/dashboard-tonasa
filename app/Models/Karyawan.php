<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens; 
use App\Models\JadwalMcu;
use App\Models\Notif; 
use App\Models\UnitKerja;
use App\Models\Departemen;
use App\Models\Provinsi;
use App\Models\PesertaMcu; 
use Spatie\Activitylog\Traits\LogsActivity; 
use Spatie\Activitylog\LogOptions;

class Karyawan extends Model
{
    use Notifiable, HasFactory, LogsActivity, HasApiTokens;

    protected $table = 'karyawans';
    protected $guarded = [];

    protected $fillable = [
        'no_sap', 'nik_karyawan', 'nama_karyawan', 'pekerjaan', 'tempat_lahir',
        'tanggal_lahir', 'umur', 'jenis_kelamin', 'golongan_darah', 'agama',
        'status_pernikahan', 'hubungan', 'kebangsaan', 'jabatan', 'eselon',
        'pendidikan', 'departemens_id', 'unit_kerjas_id', 'provinsi_id',
        'nama_kabupaten', 'nama_kecamatan', 'email', 'alamat', 'no_hp', 'foto_profil',
        'tinggi_badan', 'berat_badan'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() 
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs(); 
    }

    public function getEmailKaryawanAttribute()
    {
        return $this->attributes['email']; 
    }

    // 🌟 ACCESSOR SAKTI: Tetap mengizinkan pemanggilan $karyawan->fcm_token 
    // namun di latar belakang mengambil data dari tabel employee_logins agar tidak merusak kodingan lamamu!
    public function getFcmTokenAttribute()
    {
        return $this->employeeLogin->fcm_token ?? null;
    }

    public function jadwalMcu()
    {
        return $this->hasMany(JadwalMcu::class, 'karyawan_id');
    }

    public function notifs()
    {
        return $this->hasMany(Notif::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerjas_id', 'id');
    }
 
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemens_id', 'id');
    }
    
    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id');
    }

    public function keluargas(): HasMany
    {
        return $this->hasMany(PesertaMcu::class, 'karyawan_id', 'id');
    }

    public function employeeLogin()
    {
        return $this->hasOne(EmployeeLogin::class, 'karyawan_id', 'id');
    }

    public function pasangan()
    {
        return $this->hasOne(PesertaMcu::class, 'karyawan_id', 'id')
                    ->whereIn('tipe_anggota', ['Suami', 'Istri']);
    }
}