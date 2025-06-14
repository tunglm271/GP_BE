<?php

namespace App\Events;

use App;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;

class AppointmentCompletedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public Appointment $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $userId = $this->appointment->healthProfile->patient->user_id;
        return [
            new PrivateChannel("user.{$userId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'appointment.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'health_profile_id' => $this->appointment->health_profile_id,
            'medical_record_id' => $this->appointment->medical_record_id,
        ];
    }
}
