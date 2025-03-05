<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends \TCG\Voyager\Models\User
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'profile_image_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function announcements(): HasMany
    {
        return $this->hasMany(DriverAnnouncement::class);
    }

    public function activeAnnouncements()
    {
        return $this->announcements()->where('is_active', true);
    }

    public function isChauffeur(): bool
    {
        return $this->role === 'chauffeur';
    }

    public function isPassager(): bool
    {
        return $this->role === 'passager';
    }

    public function getProfileImageUrlAttribute(): string
    {
        if ($this->profile_image) {
            return asset('storage/' . $this->profile_image);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}

