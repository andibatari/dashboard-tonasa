<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity; 
use Spatie\Activitylog\LogOptions;

class JadwalMcu extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'jadwal_mcus';
    protected $guarded = [];
    
    protected $fillable = [
        'qr_code_id',
        'peserta_mcus_id',
        'karyawan_id',
        'paket_mcus_id',
        'poli_id',
        'tanggal_mcu',
        'tanggal_pendaftaran',
        'no_antrean',
        'no_sap',
        'nama_pasien',
        'nik_pasien',
        'perusahaan_asal',
        'dokter_id',
        'status',
        'resume_body',
        'resume_saran',
        'resume_kategori',
        'is_read_admin',
    ];

    // 3. Tambahkan fungsi konfigurasi ini
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Pantau semua kolom di tabel ini
            ->logOnlyDirty() // HANYA rekam jika nilainya benar-benar berubah (hemat storage)
            ->dontSubmitEmptyLogs(); // Jangan buat log jika tidak ada perubahan
    }

    // Relasi ke model Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    // Relasi ke model PesertaMcu
    public function pesertaMcu()
    {
        return $this->belongsTo(PesertaMcu::class, 'peserta_mcus_id');
    }
    
    // Relasi ke model Dokter
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    // Perbaiki relasi ke model PaketMcu
    public function paketMcu()
    {
        // Pastikan nama kolom foreign key adalah `paket_mcus_id`
        return $this->belongsTo(PaketMcu::class, 'paket_mcus_id');
    }
    
    public function jadwalPoli()
    {
        return $this->hasMany(JadwalPoli::class, 'jadwal_mcus_id');
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'poli_id');
    }

     /**
     * Accessor untuk mendapatkan objek Pasien (Karyawan atau Peserta MCU).
     * Digunakan sebagai pengganti relasi dinamis yang bermasalah saat Eager Loading.
     * Dapat diakses via $jadwal->patient
     */
    public function getPatientAttribute()
    {
        // Cek Eager Loading relasi yang sudah ada untuk efisiensi
        if ($this->relationLoaded('karyawan') && $this->karyawan) {
            return $this->karyawan;
        }

        if ($this->relationLoaded('pesertaMcu') && $this->pesertaMcu) {
            return $this->pesertaMcu;
        }
        
        // Jika data pasien tidak dimuat melalui relasi (contoh: di Livewire), muat secara langsung
        if ($this->karyawan_id) {
            return $this->karyawan()->first(); // Muat Karyawan
        }

        if ($this->peserta_mcus_id) {
            return $this->pesertaMcu()->first(); // Muat PesertaMcu
        }

        // Fallback: Mengembalikan objek Pasien dummy dari data lokal JadwalMcu
        return (object)[
            'nama_lengkap' => $this->nama_pasien ?? 'Pasien Tidak Terdaftar',
            'nik_karyawan' => $this->nik_pasien ?? 'N/A',
            'no_sap' => $this->no_sap ?? 'N/A',
            // Tambahkan properti lain yang mungkin diperlukan di view
            'email_karyawan' => null, // Email harus di-handle di Livewire
            'fcm_token' => null,
        ];
    }
}
