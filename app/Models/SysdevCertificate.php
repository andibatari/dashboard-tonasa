<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysdevCertificate extends Model
{
    use HasFactory;

    protected $table = 'sysdev_certificates';

    protected $fillable = [
        'kode',
        'kategori_grup',
        'kategori_singkat',
        'jenis_sertifikat',
        'nomor_sertifikat',
        'nama_produk',
        'lembaga_penerbit',
        'tanggal_terbit',
        'tanggal_expired',
        'link_sertifikat',
        'pic_email',
        'hari_tersisa_manual',
        'status_validitas',
        'notifikasi',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_expired' => 'date',
        'notifikasi' => 'boolean',
        'hari_tersisa_manual' => 'integer',
    ];

    protected $appends = [
        'hari_tersisa',
        'kode_display',
    ];

    /**
     * Hitung sisa hari secara dinamis berdasarkan tanggal expired atau manual override.
     */
    public function getHariTersisaAttribute(): ?int
    {
        if ($this->hari_tersisa_manual !== null) {
            return $this->hari_tersisa_manual;
        }

        if (!$this->tanggal_expired) {
            return null;
        }

        return (int) Carbon::today()->diffInDays(Carbon::parse($this->tanggal_expired), false);
    }

    /**
     * Format kode untuk tampilan tombol, misal: S01 -> S.01
     */
    public function getKodeDisplayAttribute(): string
    {
        $code = trim($this->kode);
        if (preg_match('/^S0?(\d+)$/i', $code, $matches)) {
            return sprintf('S.%02d', (int) $matches[1]);
        }
        return $code;
    }

    /**
     * Helper untuk menentukan grup kategori secara otomatis jika kosong.
     */
    public static function detectCategoryGroup(?string $kategoriSingkat, ?string $jenisSertifikat): string
    {
        $cat = strtoupper(trim($kategoriSingkat ?? ''));
        $jenis = strtoupper(trim($jenisSertifikat ?? ''));

        if (str_contains($cat, 'SNI') || str_contains($jenis, 'SNI')) {
            return 'FILE SERTIFIKAT SNI';
        }
        if (str_contains($cat, 'TKDN') || str_contains($jenis, 'TKDN')) {
            return 'FILE SERTIFIKAT TKDN';
        }
        if (str_contains($cat, 'GREEN') || str_contains($cat, 'GL') || str_contains($jenis, 'GREEN LABEL')) {
            return 'FILE SERTIFIKAT GREEN LABEL';
        }

        return 'FILE SERTIFIKAT ISO INTERGRASI';
    }
}

