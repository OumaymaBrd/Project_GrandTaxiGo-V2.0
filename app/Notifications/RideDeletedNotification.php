<?php

namespace App\Notifications;

use App\Models\RideRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class RideDeletedNotification extends Notification implements ShouldQueue
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
            'type' => 'ride_deleted',
            'ride_request_id' => $this->rideRequest->id,
            'passenger_name' => $this->rideRequest->passenger->name,
            'passenger_phone' => $this->rideRequest->passenger->phone,
            'scheduled_at' => $this->rideRequest->scheduled_at->format('d/m/Y H:i'),
            'pickup_address' => $this->rideRequest->pickup_address,
            'destination_address' => $this->rideRequest->destination_address,
            'created_at' => now()->format('d/m/Y H:i:s'),
            'message' => "Le passager {$this->rideRequest->passenger->name} a supprimé sa réservation prévue pour le {$this->rideRequest->scheduled_at->format('d/m/Y H:i')}"
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}

