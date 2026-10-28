<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\{Dokter, JadwalDokter};

class JadwalDokterManager extends Component 
{
    public $selectedDate, $dokter_id, $color = '#ef4444'; // Tambahkan $color dengan default merah

    public function saveJadwal() 
    {
        $this->validate([
            'selectedDate' => 'required',
            'dokter_id' => 'required',
            'color' => 'required'
        ]);

        // 1. Simpan Jadwal
        JadwalDokter::updateOrCreate(
            ['tanggal' => $this->selectedDate],
            ['dokter_id' => $this->dokter_id]
        );

        // 2. Update Warna Dokter secara Permanen di Database
        Dokter::where('id', $this->dokter_id)->update(['color' => $this->color]);

        // Reset pilihan
        $this->dokter_id = null;
        
        // Refresh kalender
        $this->dispatch('refreshCalendar');
    }

    // Fungsi opsional: agar saat dokter dipilih, warnanya otomatis mengikuti yang sudah ada di DB
    public function updatedDokterId($id)
    {
        if ($id) {
            $dokter = Dokter::find($id);
            $this->color = $dokter->color ?? '#ef4444';
        }
    }

    public function render() 
    {
        return view('livewire.admin.jadwal-dokter-manager', [
            'dokters' => Dokter::all()
        ])->layout('layouts.app');
    }
}