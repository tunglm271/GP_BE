<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;
use App\Models\Role\Doctor;

class DoctorAssignedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Appointment $appointment;
    public Doctor $doctor;
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->doctor = $appointment->doctor;
    }

    public function broadcastOn(): array
    {
        $userId = $this->appointment->healthProfile->patient->user_id;
        return [
            new PrivateChannel("users.$userId"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'appointments.assigned';
    }
}
