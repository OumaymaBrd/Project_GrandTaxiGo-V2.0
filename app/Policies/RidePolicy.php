<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ride;

class RidePolicy
{
    public function complete(User $user, Ride $ride)
    {
        // Vérifier si l'utilisateur est le chauffeur de cette course
        return $user->id === $ride->driver_id && $ride->status === 'accepted';
    }
}
