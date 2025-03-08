<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RideRequest extends Model
{
    protected $fillable = [
        'passenger_id',
        'driver_id',
        'announcement_id',
        'pickup_lat',
        'pickup_lng',
        'pickup_address',
        'destination_lat',
        'destination_lng',
        'destination_address',
        'scheduled_at',
        'status',
        'note',
        'driver_response_at',
        'passenger_confirmation_at'
    ];

    protected $casts = [
        'pickup_lat' => 'decimal:8',
        'pickup_lng' => 'decimal:8',
        'destination_lat' => 'decimal:8',
        'destination_lng' => 'decimal:8',
        'scheduled_at' => 'datetime',
        'driver_response_at' => 'datetime',
        'passenger_confirmation_at' => 'datetime'
    ];

    protected $appends = [
        'can_be_cancelled',
        'time_until_departure',
        'minutes_until_departure',
        'can_be_deleted'
    ];

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(DriverAnnouncement::class);
    }

    public function getCanBeDeletedAttribute(): bool
    {
        return $this->status === 'accepted' && $this->passenger_confirmation_at !== null;
    }

    public function getCanBeCancelledAttribute(): bool
    {
        if (!$this->scheduled_at) {
            return false;
        }

        return $this->scheduled_at->diffInMinutes(now(), false) > 5;
    }

    public function getMinutesUntilDepartureAttribute(): int
    {
        if (!$this->scheduled_at) {
            return 0;
        }

        return max(0, $this->scheduled_at->diffInMinutes(now(), false));
    }

    public function getTimeUntilDepartureAttribute(): string
    {
        if (!$this->scheduled_at) {
            return 'Non programmé';
        }

        return $this->scheduled_at->diffForHumans([
            'parts' => 2,
            'join' => true,
            'syntax' => Carbon::DIFF_RELATIVE_TO_NOW
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeModified(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled', 'rejected']);
    }

    public function isConfirmedByPassenger(): bool
    {
        return $this->passenger_confirmation_at !== null;
    }
    public function rating()
{
    return $this->hasOne(Rating::class, 'ride_id');
}
}

