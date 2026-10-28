<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Departemen;
use App\Models\UnitKerja;
use App\Models\Provinsi;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\PesertaMcuLogin;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AddKeluargaKaryawan extends Component
{
    // Properti data (Dideklarasikan di awal)
    public ?Karyawan $karyawan = null; 
    public $karyawan_id;
    public $tipe_anggota = '';
    public $no_sap;
    public $nik_pasien;
    public $nama_lengkap;
    public $jenis_kelamin;
    public $tempat_lahir;
    public $tanggal_lahir;
    public $umur;
    public $golongan_darah;
    public $pendidikan;
    public $pekerjaan;
    public $perusahaan_asal;
    public $agama;
    public $no_hp;
    public $email;
    public $alamat;
    public $password;
    public $password_confirmation;
    public $tinggi_badan;
    public $berat_badan;
    public $provinsi_id;
    public $nama_kabupaten;
    public $nama_kecamatan;
    public $fcm_token;
    public $foto_profil;

    public $provinsis = [];
    public bool $isNonKaryawan = false;

    protected $listeners = ['updateUmur'];

    public function mount($karyawan_id = null)
    {
        $this->karyawan_id = $karyawan_id;
        
        if ($this->karyawan_id) {
            $this->karyawan = Karyawan::find($this->karyawan_id);
        }
        
        $this->isNonKaryawan = is_null($this->karyawan);
        $this->provinsis = Provinsi::orderBy('nama_provinsi')->get();

        // Set default tipe anggota
        if ($this->isNonKaryawan) {
            $this->tipe_anggota = 'Non-Karyawan';
        } else {
            $this->tipe_anggota = 'Istri'; 
        }
    }

    protected function rules()
    {
        $rules = [
            'karyawan_id' => $this->tipe_anggota == 'Non-Karyawan' ? 'nullable' : 'required|exists:karyawans,id',
            'tipe_anggota' => 'required|string',
            'no_sap' => 'nullable|string|max:255',
            // FIX: NIK unik, tapi hanya di tabel login (peserta_mcu_logins)
            'nik_pasien' => 'required|string|max:255|unique:peserta_mcu_logins,nik_pasien', 
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'umur' => 'nullable|integer',
            'tinggi_badan' => 'nullable|numeric|min:1',
            'berat_badan' => 'nullable|numeric|min:1',
            'golongan_darah' => 'nullable|string',
            'pendidikan' => 'nullable|string',
            'pekerjaan' => 'nullable|string|max:255',
            'perusahaan_asal' => $this->tipe_anggota == 'Non-Karyawan' ? 'required|string|max:255' : 'nullable|string|max:255',
            'agama' => 'nullable|string',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'foto_profil' => 'nullable|string',
            'provinsi_id' => 'nullable|exists:provinsis,id',
            'nama_kabupaten' => 'nullable|string|max:255',
            'nama_kecamatan' => 'nullable|string|max:255',
        ];

        if ($this->tipe_anggota == 'Non-Karyawan' || in_array($this->tipe_anggota, ['Istri', 'Suami'])) {
            $rules['password'] = 'required|string|min:6|confirmed';
            $rules['password_confirmation'] = 'required|string|min:6';
        }

        return $rules;
    }
    
    public function updateUmur($age)
    {
        $this->umur = $age;
    }


    public function updatedProvinsiId($value)
    {
        $this->nama_kabupaten = null; // Reset input teks Kabupaten
        $this->nama_kecamatan = null; // Reset input teks Kecamatan
    }

    public function save()
    {
        $this->validate();
        // Definisikan variabel pendukung agar tidak error
        $isPasienUmum = ($this->tipe_anggota == 'Non-Karyawan');

        try {
            DB::beginTransaction();
            
            // -----------------------------------------------------
            // FIX: Gunakan array properti yang hanya berisi data model
            // -----------------------------------------------------
            $data = [
                'karyawan_id' => $isPasienUmum ? null : $this->karyawan_id,
                'tipe_anggota' => $this->tipe_anggota,
                'no_sap' => $this->no_sap,
                'nik_pasien' => $this->nik_pasien,
                'nama_lengkap' => $this->nama_lengkap,
                'jenis_kelamin' => $this->jenis_kelamin,
                'tempat_lahir' => $this->tempat_lahir,
                'tanggal_lahir' => $this->tanggal_lahir,
                'umur' => $this->umur,
                'golongan_darah' => $this->golongan_darah,
                'pendidikan' => $this->pendidikan,
                'pekerjaan' => $this->pekerjaan,
                'perusahaan_asal' => $this->perusahaan_asal,
                'agama' => $this->agama,
                'no_hp' => $this->no_hp,
                'email' => $this->email,
                'alamat' => $this->alamat,
                'provinsi_id' => $this->provinsi_id,
                'nama_kabupaten' => $this->nama_kabupaten,
                'nama_kecamatan' => $this->nama_kecamatan,
                'tinggi_badan' => $this->tinggi_badan,
                'berat_badan' => $this->berat_badan,
            ];
            // Simpan data ke tabel peserta_mcus
            $pesertaMcu = PesertaMcu::create($data); 
            
            // Simpan data login (WAJIB ADA PASSWORD)
            if ($isPasienUmum || in_array($this->tipe_anggota, ['Istri', 'Suami'])) {
                PesertaMcuLogin::create([
                    'peserta_mcu_id' => $pesertaMcu->id, 
                    'nik_pasien' => $this->nik_pasien,
                    'password' => Hash::make($this->password),
                ]);
            }
            
            DB::commit();
            // Tentukan URL tujuan dengan parameter tab 'non-ptst' (Peserta Umum)
            $redirectUrl = route('karyawan.index', ['tab' => 'non-ptst']);

            // Tampilkan Notifikasi Sukses via Browser Event (SweetAlert)
            $this->dispatch('show-success-popup', [
                'title'    => 'Berhasil!',
                'message'  => 'Data peserta berhasil ditambahkan ke database.',
                'redirect' => $redirectUrl // <-- Ini yang akan ditangkap oleh Javascript
            ]);
            
            // Bersihkan form setelah sukses (optional)
            $this->reset([
                'karyawan_id', 'tipe_anggota', 'no_sap', 'nik_pasien', 'nama_lengkap',
                'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'umur', 'golongan_darah',
                'pendidikan', 'pekerjaan', 'perusahaan_asal', 'agama', 'no_hp', 'email',
                'alamat', 'password', 'password_confirmation', 'tinggi_badan', 'berat_badan',
                'provinsi_id', 'nama_kabupaten', 'nama_kecamatan', 'fcm_token', 'foto_profil'
            ]);
            $this->dispatch($isPasienUmum ? 'pesertaSaved' : 'karyawanSaved');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('DATABASE ERROR: ' . $e->getMessage());
            
            // UBAH NAMA EVENT INI:
            $this->dispatch('show-error-popup', [
                'title' => 'Gagal Menyimpan!',
                'message' => 'Detail Error: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.add-keluarga-karyawan');
    }
}