<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'website',
        'login_background_image',
        'timezone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function addons(): HasMany
    {
        return $this->hasMany(SchoolAddon::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** Check if this school has a feature add-on enabled */
    public function hasAddon(string $featureKey): bool
    {
        return $this->addons()->where('feature_key', $featureKey)->where('enabled', true)->exists();
    }

    /** Get admin user for this school (first user with admin role or user_type admin) */
    public function adminUser(): ?User
    {
        $user = $this->users()->where('user_type', 'admin')->first();
        if ($user) {
            return $user;
        }
        return $this->users()->whereHas('roles', fn ($q) => $q->where('name', 'admin'))->first();
    }
}
