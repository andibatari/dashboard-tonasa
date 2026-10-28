<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use Illuminate\Support\Collection;

class EmployeeDetailManager extends Component
{
    public $karyawan;
    public $pesertaIstri;
    public $pesertaSuami;
    public $activeUser;
    public $activeTab = 'data';

    // 1. TAMBAH PROPERTI UNTUK FILTER TAHUN
    public $selectedYear = ''; // Default: Semua Tahun

    public function mount(Karyawan $karyawan)
    {
        // Eager load relasi yang diperlukan
        $this->karyawan = $karyawan->load('jadwalMcu.dokter', 'keluargas.jadwalMcu.dokter'); // Pastikan relasi jadwalMcu dan dokter di-load

        // Cari peserta
        $this->pesertaIstri = $this->karyawan->keluargas->firstWhere('tipe_anggota', 'Istri');
        $this->pesertaSuami = $this->karyawan->keluargas->firstWhere('tipe_anggota', 'Suami');
        
        // Atur pengguna aktif awal sebagai karyawan
        $this->activeUser = $this->karyawan;
    }
    
    // Metode untuk menampilkan data karyawan
    public function selectKaryawan()
    {
        $this->activeUser = $this->karyawan;
        $this->activeTab = 'data';
        $this->selectedYear = ''; // Reset filter saat ganti user
    }
    
    // Metode untuk menampilkan data istri
    public function selectIstri()
    {
        if ($this->pesertaIstri) {
            $this->activeUser = $this->pesertaIstri;
            $this->activeTab = 'data';
            $this->selectedYear = ''; // Reset filter saat ganti user
        }
    }
    
    // Metode untuk menampilkan data suami
    public function selectSuami()
    {
        if ($this->pesertaSuami) {
            $this->activeUser = $this->pesertaSuami;
            $this->activeTab = 'data';
            $this->selectedYear = ''; // Reset filter saat ganti user
        }
    }

    // Metode untuk mengganti tab di panel kanan
    public function changeTab($tabName)
    {
        $this->activeTab = $tabName;
    }
    
    /**
     * 2. PROPERTI TERHITUNG (Computed Property) untuk mendapatkan data yang difilter.
     * Menggunakan #[Computed] (di Livewire 3) atau property getter (di versi lama Livewire)
     */
    public function getFilteredMcuRecordsProperty(): Collection
    {
        // Pastikan activeUser ada dan memiliki relasi jadwalMcu
        if (!$this->activeUser || !$this->activeUser->jadwalMcu) {
            return collect();
        }

        $records = $this->activeUser->jadwalMcu;

        // Terapkan filter tahun jika selectedYear terisi
        if ($this->selectedYear) {
            $records = $records->filter(function ($record) {
                // Konversi tanggal_mcu (string) menjadi tahun (integer) untuk perbandingan
                return \Carbon\Carbon::parse($record->tanggal_mcu)->year == $this->selectedYear;
            });
        }
        
        return $records->sortByDesc('tanggal_mcu');
    }

    // Properti terhitung (computed property) untuk mendapatkan daftar tahun unik untuk dropdown
    public function getAvailableYearsProperty(): Collection
    {
        if (!$this->activeUser || !$this->activeUser->jadwalMcu) {
            return collect();
        }

        return $this->activeUser->jadwalMcu
            ->map(fn($record) => \Carbon\Carbon::parse($record->tanggal_mcu)->year)
            ->unique()
            ->sortDesc()
            ->values();
    }

    public function render()
    {
        return view('livewire.employee-detail-manager', [
            // Gunakan filteredMcuRecords sebagai data yang akan di-loop di view
            'filteredRecords' => $this->filteredMcuRecords,
            'availableYears' => $this->availableYears,
        ]);
    }
}