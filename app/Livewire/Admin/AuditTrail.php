<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class AuditTrail extends Component
{
    use WithPagination;

    public $search = '';

    // Me-reset pagination ke halaman 1 setiap kali admin mengetik di kolom pencarian
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Mengambil data log dari database. 
        // Relasi 'causer' otomatis menarik data user/admin yang melakukan perubahan.
        $logs = Activity::with('causer')
            ->where('description', 'like', '%' . $this->search . '%')
            ->orWhere('subject_type', 'like', '%' . $this->search . '%')
            ->latest() // Urutkan dari aktivitas paling baru
            ->paginate(15);

        return view('livewire.admin.audit-trail', [
            'logs' => $logs
        ])->layout('layouts.app'); // Pastikan layout utama dipanggil jika kamu menggunakan layout khusus
    }  // Pastikan ini sesuai dengan struktur layout yang kamu gunakan
}