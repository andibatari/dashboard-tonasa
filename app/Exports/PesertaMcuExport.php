<?php

namespace App\Exports;

use App\Models\PesertaMcu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PesertaMcuExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Ambil data dari tabel peserta_mcus
        // Filter di mana 'karyawan_id' adalah null, karena ini menandakan pasien non-PTST
        // Load relasi yang diperlukan (jika ada, sesuai skema tabel)
        return PesertaMcu::whereNull('karyawan_id')
                         ->with(['provinsi'])
                         ->get();
    }

    /**
     * @param mixed $pesertaMcu
     * @return array
     */
    public function map($pesertaMcu): array
    {
        // Peta data dari model PesertaMcu ke baris Excel
        return [
            $pesertaMcu->id,
            $pesertaMcu->karyawan_id, // Akan null untuk pasien non-PTST, tapi tetap kita sertakan untuk referensi
            $pesertaMcu->no_sap,
            $pesertaMcu->nik_pasien,
            $pesertaMcu->nama_lengkap,
            $pesertaMcu->tipe_anggota,
            $pesertaMcu->jenis_kelamin,
            $pesertaMcu->tempat_lahir,
            $pesertaMcu->tanggal_lahir,
            $pesertaMcu->umur,
            $pesertaMcu->golongan_darah,
            $pesertaMcu->pendidikan,
            $pesertaMcu->pekerjaan,
            $pesertaMcu->perusahaan_asal,
            $pesertaMcu->agama,
            $pesertaMcu->alamat,
            $pesertaMcu->no_hp,
            $pesertaMcu->email,
            $pesertaMcu->provinsi->nama_provinsi ?? 'N/A',
            $pesertaMcu->nama_kabupaten ?? 'N/A', 
            $pesertaMcu->nama_kecamatan ?? 'N/A',
        ];
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        // Tentukan nama-nama kolom (header) untuk file Excel
        return [
            'ID',
            'Karyawan ID', // Ini akan membantu mengidentifikasi jika pasien terkait dengan karyawan atau tidak
            'SAP',
            'NIK Pasien',
            'Nama Lengkap',
            'Tipe Pasien',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Umur',
            'Golongan Darah',
            'Pendidikan',
            'Pekerjaan',
            'Perusahaan Asal',
            'Agama',
            'Alamat',
            'No. HP',
            'Email',
            'Provinsi',
            'Kabupaten',
            'Kecamatan',
        ];
    }
}
