<?php

namespace App\Exports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Ambil semua data karyawan dengan eager loading untuk semua relasi yang dibutuhkan
        return Karyawan::with(['unitKerja', 'departemen', 'provinsi'])->get();
    }

    /**
     * @param mixed $karyawan
     * @return array
     */
    public function map($karyawan): array
    {
        // Peta data model ke baris Excel
        return [
            $karyawan->id,
            $karyawan->no_sap,
            $karyawan->nik_karyawan,
            $karyawan->nama_karyawan,
            $karyawan->pekerjaan,
            $karyawan->pendidikan,
            $karyawan->kebangsaan,
            $karyawan->tempat_lahir,
            $karyawan->tanggal_lahir,
            $karyawan->umur,
            $karyawan->tinggi_badan,
            $karyawan->berat_badan,
            $karyawan->jenis_kelamin,
            $karyawan->golongan_darah,
            $karyawan->agama,
            $karyawan->status_pernikahan,
            $karyawan->hubungan,
            $karyawan->jabatan,
            $karyawan->eselon,
            $karyawan->suami_istri,
            $karyawan->pekerjaan_suami_istri,
            $karyawan->alamat,
            $karyawan->no_hp,
            $karyawan->email,
            $karyawan->departemen->nama_departemen ?? 'N/A',
            $karyawan->unitKerja->nama_unit_kerja ?? 'N/A',
            $karyawan->provinsi->nama_provinsi ?? 'N/A',
            $karyawan->nama_kabupaten ?? 'N/A', 
            $karyawan->nama_kecamatan ?? 'N/A',
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
            'No SAP',
            'NIK Karyawan',
            'Nama Karyawan',
            'Pekerjaan',
            'Pendidikan',
            'Kebangsaan',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Umur',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'Jenis Kelamin',
            'Golongan Darah',
            'Agama',
            'Status Pernikahan',
            'Hubungan',
            'Jabatan',
            'Eselon',
            'Nama Suami/Istri',
            'Pekerjaan Suami/Istri',
            'Alamat',
            'No HP',
            'Email',
            'Departemen',
            'Unit Kerja',
            'Provinsi',
            'Kabupaten',
            'Kecamatan',
        ];
    }
}