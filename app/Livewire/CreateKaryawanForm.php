<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Karyawan;
use App\Models\EmployeeLogin;
use App\Models\Departemen;
use App\Models\UnitKerja;
// use App\Models\Kecamatan;
// use App\Models\Kabupaten;
use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class CreateKaryawanForm extends Component
{
    // Properti form
    public $no_sap, $nik_karyawan, $nama_karyawan, $pekerjaan;
    public $tempat_lahir, $tanggal_lahir, $umur = 0;
    public $jenis_kelamin, $golongan_darah, $agama, $hubungan;
    public $status_pernikahan, $kebangsaan, $jabatan, $eselon;
    public $pendidikan, $unit_kerjas_id, $departemens_id;
    public $provinsi_id, $nama_kabupaten, $nama_kecamatan;
    public $email, $password;
    public $alamat, $no_hp;

    // Tambahkan properti baru
    public $tinggi_badan, $berat_badan;

    protected $listeners = [
        'departemenUpdated',
        'unitKerjaUpdated',
    ];

    // Update event dropdown Departemen
    public function departemenUpdated($payload)
    {
        $this->departemens_id = $payload['id'] ?? null;
        $this->unit_kerjas_id = null;
    }

    // Update event dropdown Unit Kerja
    public function unitKerjaUpdated($payload)
    {
        $this->unit_kerjas_id = $payload['id'] ?? null;
    }

    // Hitung umur otomatis
    public function updatedTanggalLahir($value)
    {
        if (!empty($value)) {
            $birthDate = Carbon::parse($value);
            $this->umur = (int)$birthDate->diffInYears(Carbon::now());
        } else {
            $this->umur = null;
        }
    }

    // Aturan validasi
    protected $rules = [
        'no_sap' => 'required|string|unique:karyawans,no_sap',
        'nik_karyawan' => 'nullable|string|unique:karyawans,nik_karyawan',
        'nama_karyawan' => 'nullable|string|max:255',
        'jenis_kelamin' => 'nullable|string',
        'unit_kerjas_id' => 'nullable|integer|exists:unit_kerjas,id',
        'departemens_id' => 'nullable|integer|exists:departemens,id',
        'tanggal_lahir' => 'nullable|date',
        'alamat' => 'nullable|string',
        'email' => 'nullable|email|unique:karyawans,email',
        'no_hp' => 'nullable|string',
        'password' => 'nullable|min:6',
        'pendidikan' => 'nullable|string',
        'kebangsaan' => 'nullable|string',
        'tempat_lahir' => 'nullable|string',
        'golongan_darah' => 'nullable|string',
        'pekerjaan' => 'nullable|string',
        'agama' => 'nullable|string',
        'status_pernikahan' => 'nullable|string',
        'hubungan' => 'nullable|string',
        'jabatan' => 'nullable|string',
        'eselon' => 'nullable|string',
        'provinsi_id' => 'nullable|integer|exists:provinsis,id',
        'nama_kabupaten' => 'nullable|string|max:255', // PERUBAHAN: String, bukan ID
        'nama_kecamatan' => 'nullable|string|max:255', // PERUBAHAN: String, bukan ID
        'umur' => 'nullable|integer|min:0',
        // Tambahkan aturan validasi untuk tinggi dan berat badan
        'tinggi_badan' => 'nullable|numeric|min:1',
        'berat_badan' => 'nullable|numeric|min:1',
    ];

    public function saveKaryawan()
    {
        $this->validate();

        $karyawan = Karyawan::create([
            'no_sap' => $this->no_sap,
            'nik_karyawan' => $this->nik_karyawan,
            'nama_karyawan' => $this->nama_karyawan,
            'jenis_kelamin' => $this->jenis_kelamin,
            'pendidikan' => $this->pendidikan,
            'kebangsaan' => $this->kebangsaan,
            'tempat_lahir' => $this->tempat_lahir,
            'umur' => $this->umur,
            // Simpan data tinggi dan berat badan
            'tinggi_badan' => $this->tinggi_badan,
            'berat_badan' => $this->berat_badan,
            'golongan_darah' => $this->golongan_darah,
            'pekerjaan' => $this->pekerjaan,
            'agama' => $this->agama,
            'status_pernikahan' => $this->status_pernikahan,
            'hubungan' => $this->hubungan,
            'jabatan' => $this->jabatan,
            'eselon' => $this->eselon,
            'unit_kerjas_id' => $this->unit_kerjas_id,
            'departemens_id' => $this->departemens_id,
            'tanggal_lahir' => $this->tanggal_lahir,
            'alamat' => $this->alamat,
            'email' => $this->email,
            'no_hp' => $this->no_hp,
            'provinsi_id' => $this->provinsi_id,
            'nama_kabupaten' => $this->nama_kabupaten, // PERUBAHAN: Simpan string nama
            'nama_kecamatan' => $this->nama_kecamatan, // PERUBAHAN: Simpan string nama
        ]);

        EmployeeLogin::create([
            'karyawan_id' => $karyawan->id,
            'no_sap' => $this->no_sap,
            'password' => Hash::make($this->password),
        ]);

        $this->dispatch('karyawanSaved');

        $this->reset([
            'no_sap', 'nik_karyawan', 'nama_karyawan', 'jenis_kelamin',
            'pendidikan', 'kebangsaan', 'tempat_lahir', 'umur',
            'tinggi_badan', 'berat_badan', 'golongan_darah', 'pekerjaan', 'agama', 'status_pernikahan',
            'hubungan', 'jabatan', 'eselon', 'unit_kerjas_id', 'departemens_id',
            'tanggal_lahir', 'alamat', 'email', 'no_hp', 'password',
            'provinsi_id', 'nama_kabupaten', 'nama_kecamatan'
        ]);
    }

    public function render()
    {
        // Ambil semua provinsi untuk dropdown Provinsi
        $provinsis = Provinsi::all();

        // Mengirim data provinsi ke view
        return view('livewire.create-karyawan-form', [
            'provinsis' => $provinsis,
        ]);
    }
}