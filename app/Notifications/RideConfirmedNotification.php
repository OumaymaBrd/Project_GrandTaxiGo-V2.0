<?php

namespace App\Notifications;

use App\Models\RideRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class RideConfirmedNotification extends Notification
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

    public function toArray($notifiable)
    {
        return [
            'ride_request_id' => $this->rideRequest->id,
            'passenger_name' => $this->rideRequest->passenger->name,
            'scheduled_at' => $this->rideRequest->scheduled_at->format('d/m/Y H:i'),
            'pickup_address' => $this->rideRequest->pickup_address,
            'destination_address' => $this->rideRequest->destination_address
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Réservation confirmée',
            'message' => "Le passager {$this->rideRequest->passenger->name} a confirmé sa réservation pour le {$this->rideRequest->scheduled_at->format('d/m/Y H:i')}",
            'data' => $this->toArray($notifiable)
        ]);
    }
}

