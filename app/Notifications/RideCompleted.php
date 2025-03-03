<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\RideRequest;

class RideCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $rideRequest;

    public function __construct(RideRequest $rideRequest)
    {
        $this->rideRequest = $rideRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Votre course est terminée',
            'ride_id' => $this->rideRequest->id,
            'status' => 'completed',
            'driver_name' => $this->rideRequest->driver->name,
            'completed_at' => $this->rideRequest->completed_at,
            'pickup_address' => $this->rideRequest->pickup_address,
            'destination_address' => $this->rideRequest->destination_address
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}

