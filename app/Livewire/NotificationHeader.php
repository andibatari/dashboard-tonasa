<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalMcu; 

class NotificationHeader extends Component
{
    public $unreadNotificationsCount = 0;
    public $latestNotifications = []; // Variabel baru untuk menampung daftar notifikasi

    public function mount()
    {
        $this->checkNotifications();
    }
    
    public function checkNotifications()
    {
        // 1. Hitung HANYA yang belum dibaca untuk memunculkan Angka Merah di Lonceng
        $this->unreadNotificationsCount = JadwalMcu::where('status', 'Scheduled')
                                                   ->where('is_read_admin', false)
                                                   ->count();

        // 2. SELALU ambil 5 data terbaru (Tidak peduli sudah dibaca atau belum)
        // Hapus `where('is_read_admin', false)` dari sini agar datanya tidak hilang tiba-tiba!
        $this->latestNotifications = JadwalMcu::with('karyawan','pesertaMcu') 
            ->where('status', 'Scheduled')
            ->latest('created_at') 
            ->take(5) 
            ->get();
    }

    public function markNotificationsAsRead()
    {
        // Ubah status "belum dibaca" menjadi "sudah dibaca"
        if ($this->unreadNotificationsCount > 0) {
            JadwalMcu::where('status', 'Scheduled')
                     ->where('is_read_admin', false)
                     ->update(['is_read_admin' => true]);
        }
        
        $this->checkNotifications(); 
    }

    public function render()
    {
        return view('livewire.notification-header');
    }
}