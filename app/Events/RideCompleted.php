<?php

namespace App\Events;

use App\Models\RideRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RideCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ride;

    public function __construct(RideRequest $ride)
    {
        $this->ride = $ride;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->ride->passenger_id);
    }

    public function broadcastAs()
    {
        return 'notification';
    }

    public function broadcastWith()
    {
        return [
            'type' => 'ride_completed',
            'ride_id' => $this->ride->id,
            'driver_name' => $this->ride->driver->name,
            'driver_image' => $this->ride->driver->profile_image_url,
            'message' => 'Votre course est terminée. Veuillez évaluer votre chauffeur.'
        ];
    }
}
