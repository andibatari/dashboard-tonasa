<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalMcu;
use App\Models\KmstRegistration;
use Carbon\Carbon;

class NotificationHeader extends Component
{
    public $unreadNotificationsCount = 0;
    public $latestMcuNotifications = [];
    public $latestKmstNotifications = [];

    public function mount()
    {
        $this->checkNotifications();
    }
    
    public function checkNotifications()
    {
        // 1. Notifikasi MCU Belum Dibaca
        $unreadMcu = JadwalMcu::where('status', 'Scheduled')
                              ->where('is_read_admin', false)
                              ->count();

        // 2. Pendaftaran KMST Inovasi terbaru (1 hari terakhir)
        $newKmst = KmstRegistration::where('created_at', '>=', Carbon::now()->subDay())->count();

        $this->unreadNotificationsCount = $unreadMcu + $newKmst;

        // 3. Ambil 5 data Jadwal MCU terbaru
        $this->latestMcuNotifications = JadwalMcu::with(['karyawan', 'pesertaMcu']) 
            ->where('status', 'Scheduled')
            ->latest('created_at') 
            ->take(5) 
            ->get();

        // 4. Ambil 5 pendaftaran KMST terbaru
        $this->latestKmstNotifications = KmstRegistration::with('user')
            ->latest('created_at')
            ->take(5)
            ->get();
    }

    public function markNotificationsAsRead()
    {
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
