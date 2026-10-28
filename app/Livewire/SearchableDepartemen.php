<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Departemen;
use App\Models\UnitKerja;

class SearchableDepartemen extends Component
{
    // Properti untuk Departemen
    public $departemens;
    public $selectedDepartemenId = null;
    public $selectedDepartemenName = '';
    public $searchDepartemen = '';
    public $isDepartemenDropdownOpen = false;

    // Properti untuk fitur tambah departemen baru
    public $isAddingNewDepartemen = false;
    public $newDepartemenName = '';

    // Properti untuk Unit Kerja
    public $unitKerjas;
    public $selectedUnitKerjaId = null;
    public $selectedUnitKerjaName = '';
    public $searchUnitKerja = '';
    public $isUnitKerjaDropdownOpen = false;

    // Properti untuk fitur tambah unit kerja baru
    public $isAddingNewUnitKerja = false;
    public $newUnitKerjaName = '';

    /**
     * Gunakan parameter sesuai Blade:
     * @livewire('searchable-departemen', ['departemens_id' => $departemens_id, 'unit_kerjas_id' => $unit_kerjas_id])
     */
     public function mount($initialDepartemenId = null, $initialUnitKerjaId = null)
    {
        $this->departemens = Departemen::all();
        $this->unitKerjas = collect();
        
        // Memuat nilai awal untuk Unit Kerja
        if ($initialUnitKerjaId) {
            $unitKerja = UnitKerja::find($initialUnitKerjaId);
            if ($unitKerja) {
                $this->selectedUnitKerjaId = $unitKerja->id;
                $this->selectedUnitKerjaName = $unitKerja->nama_unit_kerja;
                $this->selectedDepartemenId = $unitKerja->departemens_id;
                $this->selectedDepartemenName = $unitKerja->departemen->nama_departemen;
            }
        }
        
        // Memuat nilai awal untuk Departemen jika unit kerja tidak ada atau berbeda
        if ($initialDepartemenId && !$this->selectedDepartemenId) {
            $departemen = Departemen::find($initialDepartemenId);
            if ($departemen) {
                $this->selectedDepartemenId = $departemen->id;
                $this->selectedDepartemenName = $departemen->nama_departemen;
            }
        }

        // Memuat unit kerja berdasarkan departemen yang sudah ada
        $this->loadUnitKerjas();

        // Memancarkan event awal untuk memastikan input tersembunyi diperbarui
        $this->dispatch('departemenUpdated', ['id' => $this->selectedDepartemenId]);
        $this->dispatch('unitKerjaUpdated', ['id' => $this->selectedUnitKerjaId]);
    }

    public function loadUnitKerjas()
    {
        if ($this->selectedDepartemenId) {
            $this->unitKerjas = UnitKerja::where('departemens_id', $this->selectedDepartemenId)->get();
        } else {
            $this->unitKerjas = collect();
        }
    }

    public function render()
    {
        // Filter pencarian departemen
        if ($this->searchDepartemen) {
            $this->departemens = Departemen::where('nama_departemen', 'like', '%' . $this->searchDepartemen . '%')->get();
        } else {
            $this->departemens = Departemen::all();
        }

        // Filter pencarian unit kerja
        if ($this->selectedDepartemenId && $this->searchUnitKerja) {
            $this->unitKerjas = UnitKerja::where('departemens_id', $this->selectedDepartemenId)
                                         ->where('nama_unit_kerja', 'like', '%' . $this->searchUnitKerja . '%')
                                         ->get();
        } elseif ($this->selectedDepartemenId) {
            $this->unitKerjas = UnitKerja::where('departemens_id', $this->selectedDepartemenId)->get();
        } else {
            $this->unitKerjas = collect();
        }

        return view('livewire.searchable-departemen');
    }

    // ==== Departemen ====
    public function toggleAddDepartemen()
    {
        $this->isAddingNewDepartemen = !$this->isAddingNewDepartemen;
    }

    public function addNewDepartemen()
    {
        if (!empty($this->newDepartemenName)) {
            $newDept = Departemen::create([
                'nama_departemen' => $this->newDepartemenName,
            ]);
            
            $this->departemens = Departemen::all();
            $this->selectDepartemen($newDept->id, $newDept->nama_departemen);

            $this->isAddingNewDepartemen = false;
            $this->newDepartemenName = '';
        }
    }

    public function toggleDepartemenDropdown()
    {
        $this->isDepartemenDropdownOpen = !$this->isDepartemenDropdownOpen;
    }

    // ==== Departemen ====
    public function selectDepartemen($id, $name)
    {
        $this->selectedDepartemenId = $id;
        $this->selectedDepartemenName = $name;
        $this->isDepartemenDropdownOpen = false;
        $this->searchDepartemen = '';
        $this->isUnitKerjaDropdownOpen = true; 
        $this->loadUnitKerjas(); 
        
        // 🌟 PERBAIKAN: Gunakan format Array murni agar tidak miskomunikasi
        $this->dispatch('departemenUpdated', ['id' => $id]);
    }

    // ==== Unit Kerja ====
    public function toggleUnitKerjaDropdown()
    {
        if ($this->selectedDepartemenId) {
            $this->isUnitKerjaDropdownOpen = !$this->isUnitKerjaDropdownOpen;
        }
    }

    public function selectUnitKerja($id, $name)
    {
        $this->selectedUnitKerjaId = $id;
        $this->selectedUnitKerjaName = $name;
        $this->isUnitKerjaDropdownOpen = false;
        $this->searchUnitKerja = '';
        
        // 🌟 PERBAIKAN: Gunakan format Array murni agar tidak miskomunikasi
        $this->dispatch('unitKerjaUpdated', ['id' => $id]);
    }

    public function addNewUnitKerja()
    {
        if ($this->selectedDepartemenId && !empty($this->newUnitKerjaName)) {
            $newUnit = UnitKerja::create([
                'nama_unit_kerja' => $this->newUnitKerjaName,
                'departemens_id' => $this->selectedDepartemenId,
            ]);
            
            $this->loadUnitKerjas();
            $this->selectUnitKerja($newUnit->id, $newUnit->nama_unit_kerja);

            $this->isAddingNewUnitKerja = false;
            $this->newUnitKerjaName = '';
        }
    }
}
