<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// GUNAKAN ShouldBroadcastNow (Sangat penting agar langsung terkirim)
class PanggilPasienEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $jadwalId;
    public $namaPoli;

    public function __construct($jadwalId, $namaPoli)
    {
        $this->jadwalId = $jadwalId;
        $this->namaPoli = $namaPoli;
    }

    // Nama saluran (channel) radio tempat Flutter akan mendengarkan
    public function broadcastOn()
    {
        return new Channel('mcu-channel');
    }

    // Nama sinyal spesifik yang akan ditangkap Flutter
    public function broadcastAs()
    {
        return 'PanggilPasienEvent';
    }
}