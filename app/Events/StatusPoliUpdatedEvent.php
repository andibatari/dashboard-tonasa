<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatusPoliUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $jadwalId;

    public function __construct($jadwalId)
    {
        $this->jadwalId = $jadwalId;
    }

    // Kita gunakan channel yang sama dengan panggilan agar Flutter tidak repot subscribe ulang
    public function broadcastOn()
    {
        return new Channel('mcu-channel');
    }

    public function broadcastAs()
    {
        return 'StatusPoliUpdatedEvent';
    }
}