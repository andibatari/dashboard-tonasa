<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Karyawan;
use App\Models\EmployeeLogin;
use App\Models\Departemen;
use App\Models\UnitKerja;
use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Import kelas Log untuk debugging

class EditKaryawanForm extends Component
{
    // Properti publik untuk form yang akan di-bind dengan wire:model
    public Karyawan $karyawan;
    public $no_sap;
    public $nik_karyawan;
    public $nama_karyawan;
    public $pekerjaan;
    public $tempat_lahir;
    public $tanggal_lahir;
    public $umur;
    public $jenis_kelamin;
    public $golongan_darah;
    public $agama;
    public $hubungan;
    public $status_pernikahan;
    public $kebangsaan;
    public $jabatan;
    public $eselon;
    public $pendidikan;
    public $alamat;
    public $no_hp;
    public $email;
    public $password = ''; // Nilai awal kosong agar tidak terisi secara otomatis
    public $password_confirmation = '';

    // Properti baru untuk tinggi dan berat badan
    public $tinggi_badan;
    public $berat_badan;

    // Properti untuk dropdown
    public $departemens = [];
    public $unitKerjas = [];
    public $provinsis = [];
    
    // Properti yang terikat dengan dropdown
    public $departemens_id = null;
    public $unit_kerjas_id = null;
    public $provinsi_id = null;

    // START PERUBAHAN UTAMA UNTUK LOKASI (Ganti ID dengan String/Nama)
    public $nama_kabupaten = ''; // Baru: untuk input teks (string nama kabupaten)
    public $nama_kecamatan = ''; // Baru: untuk input teks (string nama kecamatan)

    /**
     * Metode mount akan dijalankan saat komponen diinisialisasi.
     * Ini digunakan untuk memuat data awal dari objek Karyawan.
     */
    public function mount(Karyawan $karyawan)
    {
        $this->karyawan = $karyawan;

        // Mengisi properti Livewire dengan data karyawan yang ada
        $this->fill($karyawan->toArray());

        // Memastikan umur adalah integer saat form dimuat
        if ($this->tanggal_lahir) {
            $this->umur = (int)Carbon::parse($this->tanggal_lahir)->diffInYears(Carbon::now());
        }

        // Memuat ID dari relasi
        $this->departemens_id = $karyawan->departemens_id;
        $this->unit_kerjas_id = $karyawan->unit_kerjas_id;
        
        
        // START PERUBAHAN UNTUK LOKASI
        // Jika data lama tersimpan sebagai ID, kita harus memuat nama provinsi
        // Dan memuat string nama kabupaten/kecamatan dari kolom ID yang lama (dengan asumsi tipe kolom di DB sudah diganti ke STRING)
        
        // Memuat ID Provinsi (asumsi Provinsi tetap dropdown)
        $this->provinsi_id = $karyawan->provinsi_id ?? null; // Ambil dari kolom provinsi_id

        // Memuat Nama Kabupaten/Kecamatan dari kolom yang dulunya ID (Diasumsikan sekarang berisi String Nama)
        // **CATATAN PENTING**: Jika kolom `kabupaten_id` dan `kecamatan_id` di DB masih bertipe Integer ID, 
        // Anda harus mengubahnya menjadi VARCHAR/String. Jika tidak, baris ini akan menyimpan ID 
        // di properti string dan akan *crash* jika ID tersebut bukan nama yang valid.
        $this->nama_kabupaten = $karyawan->nama_kabupaten; 
        $this->nama_kecamatan = $karyawan->nama_kecamatan;
        // END PERUBAHAN UNTUK LOKASI

        // Memuat data awal untuk semua dropdown
        $this->loadDropdowns();
    }

    /**
     * Memuat data untuk semua dropdown saat komponen dimuat.
     */
    protected function loadDropdowns()
    {
        $this->departemens = Departemen::all();
        $this->provinsis = Provinsi::all();

        // Muat unit kerja jika departemen sudah dipilih
        if ($this->departemens_id) {
            $this->unitKerjas = UnitKerja::where('departemens_id', $this->departemens_id)->get();
        }
    }

    /**
     * Hook yang dijalankan saat properti tanggal_lahir diubah.
     * Digunakan untuk menghitung ulang umur.
     */
    public function updatedTanggalLahir($value)
    {
        if (!empty($value)) {
            $birthDate = Carbon::parse($value);
            $this->umur = (int)$birthDate->diffInYears(Carbon::now());
        } else {
            $this->umur = null;
        }
    }

    public function updatedDepartemensId($value)
    {
        $this->unit_kerjas_id = null; // Reset unit kerja saat departemen berubah
        $this->unitKerjas = UnitKerja::where('departemens_id', $value)->get();
    }

    /**
     * Menangkap event 'departemenUpdated' dari komponen child (SearchableDepartemen)
     * Menggunakan sintaks parameter array sesuai dengan dispatch di child.
     */
    #[On('departemenUpdated')]
    public function updateDepartemenId($id) 
    {
        // Deteksi: Jika yang masuk adalah array, ambil angka di dalam key 'id'
        // Jika sudah berupa angka, langsung gunakan.
        $this->departemens_id = is_array($id) ? $id['id'] : $id;
        
        // Reset unit kerja karena departemen berubah
        $this->unit_kerjas_id = null; 
    }

    #[On('unitKerjaUpdated')]
    public function updateUnitKerjaId($id) 
    {
        $this->unit_kerjas_id = is_array($id) ? $id['id'] : $id;
    }
    
    /**
     * Logika dropdown berantai untuk Lokasi (Provinsi -> Kabupaten)
     * Dijalankan saat provinsi_id diubah.
     */
    public function updatedProvinsiId($value)
    {
        $this->nama_kabupaten = null; // Reset input teks Kabupaten/Kota
        $this->nama_kecamatan = null; // Reset input teks Kecamatan
    }
    
    /**
     * Aturan validasi untuk form.
     */
    protected function rules()
    {
        return [
            'no_sap' => ['required', 'string', Rule::unique('karyawans', 'no_sap')->ignore($this->karyawan->id)],
            'nik_karyawan' => ['nullable', 'string', Rule::unique('karyawans', 'nik_karyawan')->ignore($this->karyawan->id)],
            'nama_karyawan' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'umur' => 'nullable|integer', // Mengubah aturan validasi untuk umur
            'jenis_kelamin' => 'nullable|string',
            'golongan_darah' => 'nullable|string',
            'agama' => 'nullable|string',
            'status_pernikahan' => 'nullable|string',
            'hubungan' => 'nullable|string',
            'kebangsaan' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'eselon' => 'nullable|string',
            'pendidikan' => 'nullable|string',
            'departemens_id' => 'nullable|integer|exists:departemens,id',
            'unit_kerjas_id' => 'nullable|integer|exists:unit_kerjas,id',
            'provinsi_id' => 'nullable|integer|exists:provinsis,id',
            'nama_kabupaten' => 'nullable|string|max:255',
            'nama_kecamatan' => 'nullable|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('karyawans', 'email')->ignore($this->karyawan->id)],
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'password' => 'nullable|min:6|confirmed', // Menambahkan aturan konfirmasi password
            'tinggi_badan' => 'nullable|numeric|min:1',
            'berat_badan' => 'nullable|numeric|min:1',
        ];
    }

    public function updateKaryawan()
    {
        // Trik Debugging: Tangkap error validasi dan tulis ke log
        try {
            $validatedData = $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ini akan mencatat semua error validasi ke file storage/logs/laravel.log
            Log::error('Validasi Gagal di EditKaryawan:', $e->errors());
            throw $e;
        }
        
        // Logging data yang divalidasi untuk debugging
        Log::info('Validated Data:', $validatedData);
        Log::info('Karyawan ID:', ['id' => $this->karyawan->id]);

        // Data yang akan di-update ke tabel karyawans
        // Hapus 'password' dari data yang akan di-update ke tabel karyawans
        $karyawanData = collect($validatedData)->except('password')->toArray();

        // Memperbarui data karyawan di tabel karyawans
        $this->karyawan->update($karyawanData);

        // Perbarui password jika diisi
        if ($this->password) {
            $this->karyawan->employeeLogin()->updateOrCreate(
                ['karyawan_id' => $this->karyawan->id],
                [
                    'password' => Hash::make($this->password),
                    'no_sap' => $this->no_sap,
                ],
            );
        }

        // Memancarkan event untuk menampilkan notifikasi
        $this->dispatch('karyawanUpdated');
    }


    /**
     * Render tampilan komponen.
     */
    public function render()
    {
        return view('livewire.edit-karyawan-form');
    }
}
