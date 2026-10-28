<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Karyawan; // Impor model Karyawan

class Keluarga extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     * Secara default, Laravel akan mencari tabel 'keluargas'. 
     * Kita perlu menentukannya secara eksplisit karena tabel yang digunakan adalah 'peserta_mcus'.
     *
     * @var string
     */
    protected $table = 'peserta_mcus';

    /**
     * Kolom-kolom yang dapat diisi (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'karyawan_id',
        'nip_sap',
        'tipe_anggota',
        'nik_pasien',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'umur',
        'golongan_darah',
        'pendidikan',
        'pekerjaan',
        'perusahaan_asal',
        'agama',
        'alamat',
        'no_hp',
        'email',
        'foto_profil',
        'provinsi_id',
        'nama_kabupaten',
        'nama_kecamatan',
        'departemens_id',
        'unit_kerjas_id',
        'tinggi_badan',
        'berat_badan',
    ];

    /**
     * Tentukan relasi 'belongs to' dengan model Karyawan.
     * Ini akan menghubungkan setiap data keluarga ke data karyawan utamanya.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    /**
     * Tentukan relasi 'belongs to' dengan model Provinsi.
     */
    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }
}