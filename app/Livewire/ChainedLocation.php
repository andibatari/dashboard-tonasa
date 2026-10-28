<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;

class ChainedLocation extends Component
{
    // Public properties that will be synced with the view
    public $provinsis = [];
    public $kabupatens = [];
    public $kecamatans = [];

    public $selectedProvinsi = null;
    public $selectedProvinsiName = '';
    public $selectedKabupaten = null;
    public $selectedKabupatenName = '';
    public $selectedKecamatan = null;
    public $selectedKecamatanName = '';
    
    public $initialKecamatanId;
    public $isProvinsiDropdownOpen = false;
    public $isKabupatenDropdownOpen = false;
    public $isKecamatanDropdownOpen = false;
    public $searchProvinsi = '';
    public $searchKabupaten = '';
    public $searchKecamatan = '';
    public $isAddingNewProvinsi = false;
    public $newProvinsiName = '';
    public $isAddingNewKabupaten = false;
    public $newKabupatenName = '';
    public $isAddingNewKecamatan = false;
    public $newKecamatanName = '';


    public function mount($initialKecamatanId = null)
    {
        $this->provinsis = Provinsi::all();
        $this->kabupatens = collect();
        $this->kecamatans = collect();
        
        if ($initialKecamatanId) {
            $this->initialKecamatanId = $initialKecamatanId;
            $kecamatan = Kecamatan::find($initialKecamatanId);
            if ($kecamatan) {
                $this->selectedKecamatan = $kecamatan->id;
                $this->selectedKecamatanName = $kecamatan->nama_kecamatan;
                $this->selectedKabupaten = $kecamatan->kabupaten_id;
                $this->selectedKabupatenName = $kecamatan->kabupaten->nama_kabupaten;
                $this->selectedProvinsi = $kecamatan->kabupaten->provinsi_id;
                $this->selectedProvinsiName = $kecamatan->kabupaten->provinsi->nama_provinsi;

                $this->kabupatens = Kabupaten::where('provinsi_id', $this->selectedProvinsi)->get();
                $this->kecamatans = Kecamatan::where('kabupaten_id', $this->selectedKabupaten)->get();
            }
        }
    }
    
    public function toggleProvinsiDropdown()
    {
        $this->isProvinsiDropdownOpen = !$this->isProvinsiDropdownOpen;
    }

    public function selectProvinsi($id, $name)
    {
        $this->selectedProvinsi = $id;
        $this->selectedProvinsiName = $name;
        $this->isProvinsiDropdownOpen = false;
        $this->searchProvinsi = '';
        $this->isKabupatenDropdownOpen = true;

        $this->kabupatens = Kabupaten::where('provinsi_id', $this->selectedProvinsi)->get();
        $this->kecamatans = collect();
        $this->selectedKabupaten = null;
        $this->selectedKabupatenName = '';
        $this->selectedKecamatan = null;
        $this->selectedKecamatanName = '';

        $this->dispatch('provinsiUpdated', ['id' => $id]);
    }
    
    public function toggleKabupatenDropdown()
    {
        if ($this->selectedProvinsi) {
            $this->isKabupatenDropdownOpen = !$this->isKabupatenDropdownOpen;
        }
    }

    public function selectKabupaten($id, $name)
    {
        $this->selectedKabupaten = $id;
        $this->selectedKabupatenName = $name;
        $this->isKabupatenDropdownOpen = false;
        $this->searchKabupaten = '';
        $this->isKecamatanDropdownOpen = true;

        $this->kecamatans = Kecamatan::where('kabupaten_id', $this->selectedKabupaten)->get();
        $this->selectedKecamatan = null;
        $this->selectedKecamatanName = '';

        $this->dispatch('kabupatenUpdated', ['id' => $id]);
    }

    public function toggleKecamatanDropdown()
    {
        if ($this->selectedKabupaten) {
            $this->isKecamatanDropdownOpen = !$this->isKecamatanDropdownOpen;
        }
    }

    public function selectKecamatan($id, $name)
    {
        $this->selectedKecamatan = $id;
        $this->selectedKecamatanName = $name;
        $this->isKecamatanDropdownOpen = false;
        $this->searchKecamatan = '';

        $this->dispatch('kecamatanUpdated', ['id' => $id]);
    }

    public function render()
    {
        if ($this->searchProvinsi) {
            $this->provinsis = Provinsi::where('nama_provinsi', 'like', '%' . $this->searchProvinsi . '%')->get();
        } else {
            $this->provinsis = Provinsi::all();
        }

        if ($this->selectedProvinsi && $this->searchKabupaten) {
            $this->kabupatens = Kabupaten::where('provinsi_id', $this->selectedProvinsi)
                                         ->where('nama_kabupaten', 'like', '%' . $this->searchKabupaten . '%')
                                         ->get();
        } elseif ($this->selectedProvinsi) {
            $this->kabupatens = Kabupaten::where('provinsi_id', $this->selectedProvinsi)->get();
        } else {
            $this->kabupatens = collect();
        }

        if ($this->selectedKabupaten && $this->searchKecamatan) {
            $this->kecamatans = Kecamatan::where('kabupaten_id', $this->selectedKabupaten)
                                         ->where('nama_kecamatan', 'like', '%' . $this->searchKecamatan . '%')
                                         ->get();
        } elseif ($this->selectedKabupaten) {
            $this->kecamatans = Kecamatan::where('kabupaten_id', $this->selectedKabupaten)->get();
        } else {
            $this->kecamatans = collect();
        }
        
        return view('livewire.chained-location');
    }
    
    // Tambahkan metode untuk menambah data baru jika diperlukan
    public function toggleAddProvinsi() { $this->isAddingNewProvinsi = !$this->isAddingNewProvinsi; }
    public function addNewProvinsi() { /* logika */ }
    public function toggleAddKabupaten() { $this->isAddingNewKabupaten = !$this->isAddingNewKabupaten; }
    public function addNewKabupaten() { /* logika */ }
    public function toggleAddKecamatan() { $this->isAddingNewKecamatan = !$this->isAddingNewKecamatan; }
    public function addNewKecamatan() { /* logika */ }
}
