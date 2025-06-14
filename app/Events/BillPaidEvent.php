<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillPaidEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    
    public Appointment $appointment; 

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    public function broadcastOn(): array
    {
        $userId = $this->appointment->medicalFacility->user_id;
        return [
            new PrivateChannel("users.$userId"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'bills.paid';
    }
}
