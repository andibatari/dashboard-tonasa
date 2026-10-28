<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Poli;

class CardAntreanPoli extends Component
{
    public function render()
    {
        // Panggil semua poli, tapi HANYA ambil jadwal pasien hari ini yang berstatus Waiting/Calling
        $polis = Poli::with(['jadwalPoli' => function($query) {
            $query->whereIn('status', ['Waiting', 'Calling'])
                  ->whereDate('updated_at', today())
                  ->orderBy('updated_at', 'asc');
        }, 'jadwalPoli.jadwalMcu.karyawan', 'jadwalPoli.jadwalMcu.pesertaMcu'])->get();

        return view('livewire.admin.card-antrean-poli', [
            'polis' => $polis
        ]);
    }
}