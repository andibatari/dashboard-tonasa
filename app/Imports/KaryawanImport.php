<?php

namespace App\Imports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeLogin; // Import model EmployeeLogin

class KaryawanImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Langsung return null jika tidak ada data yang valid
        if (!isset($row['no_sap']) || !isset($row['nik_karyawan'])) {
            return null;
        }

        // Gunakan transaksi untuk memastikan kedua entri tersimpan atau tidak sama sekali
        DB::beginTransaction();
        try{
            $karyawan = new Karyawan([
            'id'                    => $row['id'] ?? null,
            'no_sap'                => $row['no_sap'] ?? null,
            'nik_karyawan'          => $row['nik_karyawan'] ?? null,
            'nama_karyawan'         => $row['nama_karyawan'] ?? null,
            'pekerjaan'             => $row['pekerjaan'] ?? null,
            'pendidikan'            => $row['pendidikan'] ?? null,
            'kebangsaan'            => $row['kebangsaan'] ?? null,
            'tempat_lahir'          => $row['tempat_lahir'] ?? null,
            'tanggal_lahir'         => $row['tanggal_lahir'] ?? null,
            'umur'                  => $row['umur'] ?? null,
            'jenis_kelamin'         => $row['jenis_kelamin'] ?? null,
            'tinggi_badan'          => $row['tinggi_badan'] ?? null,
            'berat_badan'           => $row['berat_badan'] ?? null,
            'golongan_darah'        => $row['golongan_darah'] ?? null,
            'agama'                 => $row['agama'] ?? null,
            'status_pernikahan'     => $row['status_pernikahan'] ?? null,
            'hubungan'              => $row['hubungan'] ?? null,
            'jabatan'               => $row['jabatan'] ?? null,
            'eselon'                => $row['eselon'] ?? null,
            'alamat'                => $row['alamat'] ?? null,
            'no_hp'                 => $row['no_hp'] ?? null,
            'email'                 => $row['email'] ?? null,
            'fcm_token'             => $row['fcm_token'] ?? null,
            'departemens_id'        => $row['departemens_id'] ?? null,
            'unit_kerjas_id'        => $row['unit_kerjas_id'] ?? null,
            'provinsi_id'           => $row['provinsi_id'] ?? null,
            'nama_kabupaten'        => $row['nama_kabupaten'] ?? null,
            'nama_kecamatan'        => $row['nama_kecamatan'] ?? null,
            'password'              => bcrypt($row['password'] ?? 'password'),
        ]);

        $karyawan->save(); // Simpan karyawan untuk mendapatkan ID
        // Kita gunakan fallback 'password' jika kolom password di excel kosong
        $passwordLogin = $row['password'] ?? 'password';

        $login = new EmployeeLogin([
            'karyawan_id' => $karyawan->id, 
            'no_sap'      => $row['no_sap'],
            'password'    => bcrypt($passwordLogin),
        ]);
        $login->save();

        DB::commit();
        return $karyawan;
        } catch (\Exception $e) {
            DB::rollBack();
            // Anda bisa log error di sini
            return null;
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
    
    public function headingRow(): int
    {
        return 1;
    }
}
