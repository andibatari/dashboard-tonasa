<?php

namespace App\Imports;

use App\Models\PesertaMcu;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;

class PesertaMcuImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Langsung return null jika tidak ada data yang valid
        if (!isset($row['nama_lengkap'])) {
            return null;
        }

        return new PesertaMcu([
            'karyawan_id'       => null, // Menandakan ini adalah pasien non-PTST
            'no_sap'           => $row['no_sap'] ?? null,
            'tipe_anggota'      => $row['tipe_anggota'] ?? null,
            'nik_pasien'        => $row['nik_pasien'] ?? null,
            'nama_lengkap'      => $row['nama_lengkap'] ?? null,
            'jenis_kelamin'     => $row['jenis_kelamin'] ?? null,
            'tempat_lahir'      => $row['tempat_lahir'] ?? null,
            'tanggal_lahir'     => $row['tanggal_lahir'] ?? null,
            'umur'              => $row['umur'] ?? null,
            'tinggi_badan'      => $row['tinggi_badan'] ?? null,
            'berat_badan'       => $row['berat_badan'] ?? null,
            'golongan_darah'    => $row['golongan_darah'] ?? null,
            'pendidikan'        => $row['pendidikan'] ?? null,
            'pekerjaan'         => $row['pekerjaan'] ?? null,
            'perusahaan_asal'   => $row['perusahaan_asal'] ?? null,
            'agama'             => $row['agama'] ?? null,
            'alamat'            => $row['alamat'] ?? null,
            'no_hp'             => $row['no_hp'] ?? null,
            'email'             => $row['email'] ?? null,
            'provinsi_id'       => $row['provinsi_id'] ?? null,
            'nama_kabupaten'      => $row['nama_kabupaten'] ?? null,
            'nama_kecamatan'      => $row['nama_kecamatan'] ?? null,
        ]);
    }
    
    public function headingRow(): int
    {
        return 1;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
