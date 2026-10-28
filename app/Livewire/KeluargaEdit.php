<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Keluarga; 
use App\Models\Provinsi;
use App\Models\Departemen;
use App\Models\UnitKerja;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\PesertaMcuLogin; // Digunakan untuk validasi NIK
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KeluargaEdit extends Component
{
    public Keluarga $keluarga;
    public $no_sap,$nik_pasien, $nama_lengkap, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $umur, $golongan_darah;
    public $pendidikan, $pekerjaan, $agama, $alamat, $no_hp, $email, $perusahaan_asal, $tinggi_badan, $berat_badan;
    public $provinsi_id = null;
    public $nama_kabupaten = '';
    public $nama_kecamatan = '';
    
    // TAMBAHAN UNTUK PASSWORD (Walaupun tidak ada di model Keluarga, tapi di form ada)
    public $password;
    public $password_confirmation;

    public $provinsis = [];

    protected function rules()
    {
        return [
            // FIX NIK: Abaikan NIK milik record yang sedang di-edit (di,asumsikan NIK ada di tabel Keluarga)
            'no_sap' => ['nullable', 'string', 'max:255', 
                            Rule::unique('keluargas', 'no_sap')->ignore($this->keluarga->id)],
            'nik_pasien' => ['nullable', 'string', 'max:255', 
                            Rule::unique('peserta_mcu_logins', 'nik_pasien')->ignore($this->keluarga->id, 'peserta_mcu_id')],
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'umur' => 'nullable|integer',
            'tinggi_badan' => 'nullable|integer',
            'berat_badan' => 'nullable|integer',
            'golongan_darah' => 'nullable|string|max:255',
            'pendidikan' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'agama' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'provinsi_id' => 'nullable|exists:provinsis,id',
            'nama_kabupaten' => 'nullable|string|max:255',
            'nama_kecamatan' => 'nullable|string|max:255',
            'perusahaan_asal' => 'nullable|string|max:255',
            
            // Password hanya jika diisi (untuk update akun login)
            'password' => 'nullable|string|min:6|confirmed',
            'password_confirmation' => 'nullable|required_with:password',
        ];
    }

    public function mount(Keluarga $keluarga)
    {
        $this->keluarga = $keluarga->load('karyawan');
        
        // Isi properti Livewire dengan data dari model Keluarga
        $this->fill($keluarga->toArray());
        
        // Jika model menggunakan tanggal string, format kembali ke Y-m-d untuk input type="date"
        if ($this->tanggal_lahir) {
            $this->tanggal_lahir = Carbon::parse($this->tanggal_lahir)->format('Y-m-d');
            $this->umur = Carbon::parse($this->tanggal_lahir)->age;
        }

        $this->provinsis = Provinsi::orderBy('nama_provinsi')->get();
        // Memuat Nama Kabupaten/Kecamatan dari kolom yang dulunya ID (Diasumsikan sekarang berisi String Nama)
        // **CATATAN PENTING**: Jika kolom `kabupaten_id` dan `kecamatan_id` di DB masih bertipe Integer ID, 
        // Anda harus mengubahnya menjadi VARCHAR/String. Jika tidak, baris ini akan menyimpan ID 
        // di properti string dan akan *crash* jika ID tersebut bukan nama yang valid.
        $this->nama_kabupaten = $keluarga->nama_kabupaten; 
        $this->nama_kecamatan = $keluarga->nama_kecamatan;
    }

    public function updatedTanggalLahir($value)
    {
        $this->umur = $value ? Carbon::parse($value)->age : null;
    }

    public function updatedProvinsiId($value)
    {
        $this->nama_kabupaten = null; // Reset input teks Kabupaten/Kota
        $this->nama_kecamatan = null;    
    }

    public function updateKeluarga()
    {
        // Validasi data
        $this->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik_pasien' => 'required|string',
            'password' => 'nullable|min:6|confirmed',
        ]);
        
        try {
            DB::beginTransaction();

            // Saring data yang hanya ada di tabel peserta_mcus
            $data = $this->only([
                'nik_pasien', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
                'golongan_darah', 'pendidikan', 'pekerjaan', 'agama', 'alamat', 'no_hp', 
                'email', 'provinsi_id', 'nama_kabupaten', 'nama_kecamatan', 'perusahaan_asal',
                'tinggi_badan', 'berat_badan', 'umur', 'no_sap'
            ]);

            // 1. Update Tabel Utama (peserta_mcus)
            $this->keluarga->update($data);

            // 2. Update Password di tabel login jika password baru diisi
            if (!empty($this->password)) {
                // Gunakan id keluarga untuk mencari login agar lebih akurat
                $login = PesertaMcuLogin::where('peserta_mcu_id', $this->keluarga->id)->first();
                
                if ($login) {
                    $login->update([
                        'password' => Hash::make($this->password),
                        'nik_pasien' => $this->nik_pasien // Pastikan NIK login ikut terupdate
                    ]);
                }
            }

            DB::commit();
            $this->dispatch('keluargaUpdated');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.keluarga-edit');
    }
}