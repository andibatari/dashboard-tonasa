<?php

namespace App\Exports;

use App\Models\JadwalMcu;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class PemeriksaanExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    // 1. Kita buat daftar poli statis agar gampang dipanggil di bawah
    protected $daftarPoli = [
        'laboratorium' => 'Laboratorium',
        'gigi'         => 'Gigi',
        'fisik'        => 'Fisik',
        'mata'         => 'Mata',
        'ekg'          => 'EKG',
        'audiometri'   => 'Audiometri',
        'spirometri'   => 'Spirometri',
        'kebugaran'    => 'Kebugaran',
        'treadmill'    => 'Treadmill',
        'thorax' => 'Thorax Photo',
        'usg'          => 'USG'
    ];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = JadwalMcu::query()
            ->leftJoin('peserta_mcus', 'jadwal_mcus.peserta_mcus_id', '=', 'peserta_mcus.id')
            ->leftJoin('karyawans', 'jadwal_mcus.karyawan_id', '=', 'karyawans.id')
            ->leftJoin('departemens', 'karyawans.departemens_id', '=', 'departemens.id')
            // Kode join ke tabel polis dihapus karena kita pakai Skenario 2 (JSON)
            ->select([
                'jadwal_mcus.*', 
                'peserta_mcus.nama_lengkap as nama_umum',
                'peserta_mcus.nik_pasien',
                'karyawans.nama_karyawan', 
                'karyawans.no_sap as sap_karyawan',
                'departemens.nama_departemen as dept_karyawan',
            ]);

        if (!empty($this->filters['date_start']) && !empty($this->filters['date_end'])) {
            $query->whereBetween('jadwal_mcus.tanggal_mcu', [
                $this->filters['date_start'] . ' 00:00:00', 
                $this->filters['date_end'] . ' 23:59:59'
            ]);
        }

        if (!empty($this->filters['status_kehadiran'])) {
            $query->where('jadwal_mcus.status', $this->filters['status_kehadiran']);
        }

        // FILTER JSON SAJA (Hapus filter poli_id database yang lama)
        if (!empty($this->filters['poli_id'])) {
            $query->where(function($q) {
                $q->whereNotNull('jadwal_mcus.resume_body->' . $this->filters['poli_id'])
                  ->where('jadwal_mcus.resume_body->' . $this->filters['poli_id'], '!=', 'null')
                  ->where('jadwal_mcus.resume_body->' . $this->filters['poli_id'], '!=', '');
            });
        }

        // Filter Departemen
        if (!empty($this->filters['departemens_id'])) {
            $query->where('karyawans.departemens_id', $this->filters['departemens_id']);
        } 
        // Filter Kategori (Karyawan vs Keluarga/Umum)
        elseif (!empty($this->filters['tipe_anggota'])) {
            if ($this->filters['tipe_anggota'] == 'Karyawan') {
                $query->whereNotNull('jadwal_mcus.karyawan_id');
            } else {
                $query->whereNull('jadwal_mcus.karyawan_id'); 
            }
        }

        return $query->orderBy('jadwal_mcus.tanggal_mcu', 'desc');
    }

    public function headings(): array
    {
        // 2. Siapkan Judul Kolom yang PASTI SELALU ADA di awal
        $headings = [
            "NIP/SAP",
            "Nama Pasien",
            "Departemen",
            "Tgl Periksa",
            "BMI"
        ];

        // 3. LOGIKA DINAMIS: Cek apakah user sedang memfilter poli
        if (!empty($this->filters['poli_id']) && array_key_exists($this->filters['poli_id'], $this->daftarPoli)) {
            // JIKA YA: Hanya masukkan judul poli yang dipilih (Misal: cuma "Mata")
            $headings[] = $this->daftarPoli[$this->filters['poli_id']];
        } else {
            // JIKA TIDAK (Semua Poli): Tampilkan semua kolom poli
            foreach ($this->daftarPoli as $label) {
                $headings[] = $label;
            }
        }

        // 4. Tambahkan Judul Kolom yang PASTI SELALU ADA di akhir
        $headings[] = "Kesimpulan (Kategori)";
        $headings[] = "Saran Dokter";

        return $headings;
    }

    public function map($jadwal): array
    {
        $resume = json_decode($jadwal->resume_body, true) ?? [];
        
        $namaFinal = $jadwal->nama_karyawan ?: ($jadwal->nama_umum ?: $jadwal->nama_pasien);
        $sapFinal = $jadwal->sap_karyawan ?: ($jadwal->nik_pasien ?: '-');

        // 5. Masukkan Data Baris yang PASTI SELALU ADA di awal
        $row = [
            $sapFinal,
            $namaFinal,
            $jadwal->dept_karyawan ?: 'Umum/Non-PTST',
            $jadwal->tanggal_mcu ? \Carbon\Carbon::parse($jadwal->tanggal_mcu)->format('d-m-Y') : '-',
            $resume['bmi'] ?? '-'
        ];

        // 6. LOGIKA DINAMIS: Sinkronkan isi baris dengan judul kolom di atas
        if (!empty($this->filters['poli_id']) && array_key_exists($this->filters['poli_id'], $this->daftarPoli)) {
            // JIKA YA: Tarik isi JSON khusus poli itu saja
            $val = $resume[$this->filters['poli_id']] ?? '-';
            $row[] = str_contains($val, '.') ? '📄 File Terlampir' : $val; // (Trik merapikan file upload)
        } else {
            // JIKA TIDAK: Tarik semua isi JSON
            foreach ($this->daftarPoli as $key => $label) {
                $val = $resume[$key] ?? '-';
                $row[] = str_contains($val, '.') ? '📄 File Terlampir' : $val;
            }
        }

        // 7. Masukkan Data Baris yang PASTI SELALU ADA di akhir
        $row[] = $jadwal->resume_kategori ?? '-';
        $row[] = $jadwal->resume_saran ?? '-';

        return $row;
    }
}