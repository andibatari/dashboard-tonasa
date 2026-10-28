<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PesertaMcu;
use Carbon\Carbon;
use Illuminate\Support\Collection; // Import Collection

class PesertaMcuDetailManager extends Component
{
    // Properti publik untuk menyimpan data
    public $pesertaMcu;
    public $activeTab = 'data';
    
    // TAMBAH: Properti untuk Filter Tahun
    public $selectedYear = ''; 
    
    // TAMBAH: Listener untuk memuat ulang data saat filter berubah
    protected $listeners = ['refreshComponent' => '$refresh']; 

    public function mount(PesertaMcu $pesertaMcu)
    {
        // Eager load relasi yang diperlukan, termasuk jadwal MCU dan dokternya
        $this->pesertaMcu = $pesertaMcu->load([
            'karyawan', 
            'provinsi',
            'jadwalMcu.dokter' // WAJIB: Eager load jadwal MCU
        ]);
    }

    // Metode untuk mengganti tab di panel kanan
    public function changeTab($tabName)
    {
        $this->activeTab = $tabName;
    }
    
    // TAMBAH: Computed property untuk mendapatkan riwayat yang difilter
    public function getFilteredMcuRecordsProperty(): Collection
    {
        if (!$this->pesertaMcu || !$this->pesertaMcu->jadwalMcu) {
            return collect();
        }

        $records = $this->pesertaMcu->jadwalMcu;

        // Terapkan filter tahun jika selectedYear terisi
        if ($this->selectedYear) {
            $records = $records->filter(function ($record) {
                // Pastikan tanggal_mcu bukan null sebelum parsing
                if (!$record->tanggal_mcu) return false;
                return Carbon::parse($record->tanggal_mcu)->year == $this->selectedYear;
            });
        }
        
        return $records->sortByDesc('tanggal_mcu');
    }

    // TAMBAH: Computed property untuk menghasilkan daftar tahun unik
    public function getAvailableYearsProperty(): Collection
    {
        if (!$this->pesertaMcu || !$this->pesertaMcu->jadwalMcu) {
            return collect();
        }

        return $this->pesertaMcu->jadwalMcu
            ->map(fn($record) => $record->tanggal_mcu ? Carbon::parse($record->tanggal_mcu)->year : null)
            ->filter() // Menghapus nilai null
            ->unique()
            ->sortDesc()
            ->values();
    }


    public function render()
    {
        // Variabel yang dilewatkan ke view:
        $filteredRecords = $this->filteredMcuRecords;
        $availableYears = $this->availableYears;

        return view('livewire.peserta-mcu-detail-manager', compact('filteredRecords', 'availableYears'));
    }
}