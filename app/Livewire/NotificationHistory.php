<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NotificationLog;

class NotificationHistory extends Component
{
    // Properti filter dan search dapat ditambahkan di sini
    
    public function render()
    {
        $logs = NotificationLog::with('admin')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('livewire.notification-history', [
            'logs' => $logs
        ])
        ->layout('layouts.app');;
    }
}