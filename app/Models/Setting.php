<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    public static function getDirectors(): array
    {
        $setting = static::where('key', 'official_directors')->first();
        if ($setting && !empty($setting->value)) {
            $decoded = json_decode($setting->value, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        return [
            [
                'jabatan' => 'President Director',
                'direktorat' => 'President Director',
                'nama' => 'Anis, SE., MM.'
            ],
            [
                'jabatan' => 'Director of Finance, Risk Management & General',
                'direktorat' => 'Finance, Risk Management & General',
                'nama' => 'Sulaiha Muhyiddin, SE, M.BUS, Ak'
            ],
            [
                'jabatan' => 'Director of Operation',
                'direktorat' => 'Operation',
                'nama' => 'Mochamad Alfin Zaini, Ir., MM.'
            ]
        ];
    }

    public static function setDirectors(array $directors): void
    {
        static::updateOrCreate(
            ['key' => 'official_directors'],
            [
                'value' => json_encode($directors),
                'description' => 'Daftar Pejabat Direksi PT Semen Tonasa untuk Sertifikasi Berjenjang'
            ]
        );
    }

    /**
     * Mengambil daftar tahapan resmi KMST (Roadmap Kompetisi)
     */
    public static function getKmstTahapan(): array
    {
        $setting = static::where('key', 'kmst_tahapan')->first();
        if ($setting && !empty($setting->value)) {
            $decoded = json_decode($setting->value, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        // Default 4 Tahapan resmi KMST
        return [
            [
                'id'         => 1,
                'nomor'      => '1',
                'judul'      => 'Pendaftaran & Upload',
                'deskripsi'  => 'Registrasi susunan tim dan unggah berkas makalah final (.pdf / .docx maks. 30MB).',
                'badge'      => 'Tahap Aktif',
                'info_bawah' => 'Batas Akhir Segera Diumumkan →',
                'is_active'  => true,
                'icon_type'  => 'number',
            ],
            [
                'id'         => 2,
                'nomor'      => '2',
                'judul'      => 'Seleksi & Reviu',
                'deskripsi'  => 'Verifikasi administratif dan penilaian kelayakan makalah komprehensif oleh dewan juri ahli.',
                'badge'      => 'Tahap Berikutnya',
                'info_bawah' => 'Penjurian Tahap 1',
                'is_active'  => false,
                'icon_type'  => 'number',
            ],
            [
                'id'         => 3,
                'nomor'      => '3',
                'judul'      => 'Konvensi Mutu',
                'deskripsi'  => 'Presentasi karya inovasi langsung di hadapan dewan juri dan unjuk keunggulan solusi nyata.',
                'badge'      => 'Tahap Presentasi',
                'info_bawah' => 'Sesi Tanya Jawab',
                'is_active'  => false,
                'icon_type'  => 'number',
            ],
            [
                'id'         => 4,
                'nomor'      => '4',
                'judul'      => 'Penganugerahan',
                'deskripsi'  => 'Penetapan juara mutu Tonasa dan tiket emas delegasi resmi ke TKMPN nasional & ICQCC.',
                'badge'      => 'Awarding Night',
                'info_bawah' => 'Grand Champion KMST',
                'is_active'  => false,
                'icon_type'  => 'trophy',
            ],
        ];
    }

    /**
     * Menyimpan seluruh daftar tahapan resmi KMST
     */
    public static function setKmstTahapan(array $tahapan): void
    {
        static::updateOrCreate(
            ['key' => 'kmst_tahapan'],
            [
                'value'       => json_encode(array_values($tahapan)),
                'description' => 'Konfigurasi Tahapan & Roadmap Konvensi Mutu Semen Tonasa (KMST)'
            ]
        );
    }

    /**
     * Mengaktifkan salah satu tahapan tertentu berdasarkan ID atau nomor
     */
    public static function activateKmstTahap($targetId): void
    {
        $tahapan = static::getKmstTahapan();
        foreach ($tahapan as &$t) {
            $match = (isset($t['id']) && (string)$t['id'] === (string)$targetId) || 
                     (isset($t['nomor']) && (string)$t['nomor'] === (string)$targetId);
            $t['is_active'] = $match;
            if ($match && empty($t['badge'])) {
                $t['badge'] = 'Tahap Aktif';
            }
        }
        static::setKmstTahapan($tahapan);
    }
}