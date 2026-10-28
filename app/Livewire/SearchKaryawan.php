<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Karyawan; 
use App\Models\UnitKerja;
use App\Models\Departemen;
use App\Models\PesertaMcu; 

class SearchKaryawan extends Component
{
    use WithPagination;

    // Filter Pencarian
    public $searchSap = '';
    public $searchNama = '';
    public $searchUnitKerja = ''; 
    public $searchDepartemen = ''; 

    public $searchNik = '';
    public $searchNamaPasien = '';
    public $searchPerusahaanAsal = '';

    public $activeTab = 'ptst';
    
    // 🌟 TAMBAHAN: Properti untuk kontrol jumlah data per halaman (Default 10)
    public $perPage = 10; 

    // List untuk Dropdown
    public $listDepartemen = [];
    public $listUnitKerja = [];

    protected $queryString = [
        'activeTab' => ['except' => 'ptst', 'as' => 'tab'], 
        'searchSap', 
        'searchNama', 
        'searchUnitKerja', 
        'searchDepartemen', 
        'searchNik', 
        'searchNamaPasien', 
        'searchPerusahaanAsal',
        'perPage' // 🌟 TAMBAHAN: Agar pilihan per halaman tersimpan di URL
    ];

    public function mount()
    {
        if (request()->has('tab')) {
            $this->activeTab = request()->query('tab');
        }

        $this->listDepartemen = Departemen::all();
        
        if ($this->searchDepartemen) {
            $this->listUnitKerja = UnitKerja::where('departemens_id', $this->searchDepartemen)->get();
        }
    }

    public function updating($key)
    {
        // 🌟 PERBAIKAN: Masukkan perPage ke trigger, dan reset page spesifik sesuai nama tabel
        if (in_array($key, ['searchSap', 'searchNama', 'searchUnitKerja', 'searchDepartemen', 'searchNik', 'searchNamaPasien', 'searchPerusahaanAsal', 'perPage'])) {
            $this->resetPage('ptstPage');
            $this->resetPage('nonPtstPage');
        }
    }

    public function updatedSearchDepartemen($value)
    {
        $this->searchUnitKerja = ''; 
        
        if ($value) {
            $this->listUnitKerja = UnitKerja::where('departemens_id', $value)->get();
        } else {
            $this->listUnitKerja = [];
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->searchSap = '';
        $this->searchNama = '';
        $this->searchUnitKerja = '';
        $this->searchDepartemen = '';
        $this->searchNik = '';
        $this->searchNamaPasien = '';
        $this->searchPerusahaanAsal = '';
        $this->listUnitKerja = [];

        // Reset page spesifik saat ganti tab
        $this->resetPage('ptstPage');
        $this->resetPage('nonPtstPage');
    }
    
    public function render()
    {
        if ($this->activeTab === 'ptst') {
            $query = Karyawan::query()->with(['unitKerja', 'departemen']);

            $query->when($this->searchSap, fn($q) => $q->where('no_sap', 'like', '%' . $this->searchSap . '%'));
            $query->when($this->searchNama, fn($q) => $q->where('nama_karyawan', 'like', '%' . $this->searchNama . '%'));
            
            $query->when($this->searchDepartemen, fn($q) => $q->where('departemens_id', $this->searchDepartemen));
            $query->when($this->searchUnitKerja, fn($q) => $q->where('unit_kerjas_id', $this->searchUnitKerja));
            
            // 🌟 TAMBAHAN: Ganti angka 10 statis menjadi dinamis mengikuti variabel $perPage
            $items = $query->paginate($this->perPage, pageName: 'ptstPage');
        } else {
            $query = PesertaMcu::query()->with('karyawan');
            
            $query->when($this->searchNik, fn($q) => $q->where('nik_pasien', 'like', '%' . $this->searchNik . '%'));
            $query->when($this->searchNamaPasien, fn($q) => $q->where('nama_lengkap', 'like', '%' . $this->searchNamaPasien . '%'));
            $query->when($this->searchPerusahaanAsal, fn($q) => $q->where('perusahaan_asal', 'like', '%' . $this->searchPerusahaanAsal . '%'));

            // 🌟 TAMBAHAN: Ganti angka 10 statis menjadi dinamis mengikuti variabel $perPage
            $items = $query->paginate($this->perPage, pageName: 'nonPtstPage');
        }

        return view('livewire.search-karyawan', [
            'items' => $items,
        ]);
    }
}