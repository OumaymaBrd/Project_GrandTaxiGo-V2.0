<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverPost extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'latitude',
        'longitude',
        'address',
        'is_available'
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

