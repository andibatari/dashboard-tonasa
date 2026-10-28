<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable; 

class PesertaMcu extends Model
{
    use HasFactory, Notifiable; 

    protected $table = 'peserta_mcus'; 

    protected $fillable = [
        'karyawan_id', 'tipe_anggota', 'no_sap', 'nik_pasien', 'nama_lengkap',
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'umur', 'golongan_darah',
        'pendidikan', 'pekerjaan', 'perusahaan_asal', 'agama', 'alamat', 'no_hp',
        'email', 'foto_profil', 'provinsi_id', 'nama_kabupaten', 'nama_kecamatan',
        'tinggi_badan', 'berat_badan',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }

    public function jadwalMcu()
    {
        return $this->hasMany(JadwalMcu::class, 'peserta_mcus_id');
    }

    // 🌟 ACCESSOR SAKTI: Dialirkan mengambil dari tabel login pendampingnya
    public function getFcmTokenAttribute()
    {
        return $this->pesertaMcuLogin->fcm_token ?? null;
    }

    public function pesertaMcuLogin()
    {
        return $this->hasOne(PesertaMcuLogin::class, 'peserta_mcu_id', 'id');
    }
}