<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PaketMcu;
use App\Models\Poli;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;

class PaketPoli extends Component
{
    // Properti untuk Tab Manajemen Paket
    #[Rule('required|string|min:3|unique:paket_mcus,nama_paket', message: 'Nama paket ini sudah ada.')]
    public $nama_paket = '';

    // Properti untuk Tab Manajemen Poli
    #[Rule('required|string|min:3|unique:polis,nama_poli', message: 'Nama poli ini sudah ada.')]
    public $nama_poli = '';

    // Properti untuk Tab Manajemen Paket-Poli
    #[Rule('required|exists:paket_mcus,id', message: 'Silakan pilih paket MCU.')]
    public $paket_mcus_id = null;
    
    public $poli_ids = []; 
    
    public $activeTab = 'paket';
    public $paketList = [];
    public $poliList = [];
    public $daftarPaket = [];
    public $daftarPoli = [];

    public function mount()
    {
        $this->loadData();
    }
    
    private function loadData()
    {
        $this->paketList = PaketMcu::all();
        $this->poliList = Poli::all();
        $this->daftarPaket = PaketMcu::with('poli')->get();
        $this->daftarPoli = $this->poliList;
    }
    
    public function updatedActiveTab($tab)
    {
        $this->loadData();
    }

    // Metode untuk mengelola Paket MCU
    public function savePaket()
    {
        $this->validateOnly('nama_paket');
        
        try {
            PaketMcu::create(['nama_paket' => $this->nama_paket]);
            $this->dispatch('dataSaved', ['message' => 'Paket MCU berhasil disimpan.']);
            $this->reset(['nama_paket']);
            $this->loadData(); // Memuat ulang data setelah berhasil
        } catch (\Exception $e) {
            $this->dispatch('dataGagal', ['message' => 'Gagal menyimpan paket: ' . $e->getMessage()]);
        }
    }

    public function deletePaket($id)
    {
        try {
            $paket = PaketMcu::findOrFail($id);
            $paket->poli()->detach();
            $paket->delete();
            $this->dispatch('dataSaved', ['message' => 'Paket MCU berhasil dihapus.']);
            $this->loadData(); // Memuat ulang data setelah berhasil
        } catch (\Exception $e) {
            $this->dispatch('dataGagal', ['message' => 'Gagal menghapus paket: ' . $e->getMessage()]);
        }
    }

    // Metode untuk mengelola Poli
    public function savePoli()
    {
        $this->validateOnly('nama_poli');
        
        try {
            Poli::create(['nama_poli' => $this->nama_poli]);
            $this->dispatch('dataSaved', ['message' => 'Poli berhasil disimpan.']);
            $this->reset(['nama_poli']);
            $this->loadData(); // Memuat ulang data setelah berhasil
        } catch (\Exception $e) {
            $this->dispatch('dataGagal', ['message' => 'Gagal menyimpan poli: ' . $e->getMessage()]);
        }
    }

    public function deletePoli($id)
    {
        try {
            $poli = Poli::findOrFail($id);
            $poli->paketMcus()->detach();
            $poli->delete();
            $this->dispatch('dataSaved', ['message' => 'Poli berhasil dihapus.']);
            $this->loadData(); // Memuat ulang data setelah berhasil
        } catch (\Exception $e) {
            $this->dispatch('dataGagal', ['message' => 'Gagal menghapus poli: ' . $e->getMessage()]);
        }
    }

    // Metode untuk mengelola Paket-Poli
    public function attachPoliToPaket()
    {
        $this->validate([
            'paket_mcus_id' => 'required|exists:paket_mcus,id',
            'poli_ids' => 'required|array|min:1',
            'poli_ids.*' => 'exists:polis,id',
        ]);

        try {
            $paket = PaketMcu::findOrFail($this->paket_mcus_id);
            $paket->poli()->syncWithoutDetaching($this->poli_ids);
            
            $this->dispatch('dataSaved', ['message' => 'Poli berhasil dihubungkan ke paket.']);
            $this->reset(['paket_mcus_id', 'poli_ids']);
            $this->loadData(); // Memuat ulang data setelah berhasil
        } catch (\Exception $e) {
            $this->dispatch('dataGagal', ['message' => 'Gagal menghubungkan poli: ' . $e->getMessage()]);
        }
    }

    public function detachPoliFromPaket($paketId, $poliId)
    {
        try {
            $paket = PaketMcu::findOrFail($paketId);
            $paket->poli()->detach($poliId);
            $this->dispatch('dataSaved', ['message' => 'Poli berhasil dihapus dari paket.']);
            $this->loadData(); // Memuat ulang data setelah berhasil
        } catch (\Exception $e) {
            $this->dispatch('dataGagal', ['message' => 'Gagal menghapus poli dari paket: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.paket-poli')
            ->layout('layouts.app');
    }
}