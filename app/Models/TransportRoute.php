<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_name',
        'route_code',
        'description',
        'route_details',
        'start_location',
        'end_location',
        'distance_km',
        'estimated_duration_minutes',
        'morning_pickup_time',
        'morning_dropoff_time',
        'afternoon_pickup_time',
        'afternoon_dropoff_time',
        'fare_amount',
        'currency',
        'fare_type',
        'max_capacity',
        'current_capacity',
        'status',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'route_details' => 'array',
        'fare_amount' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transport(): BelongsTo
    {
        return $this->belongsTo(Transport::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'transport_route_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getCurrentPassengersAttribute(): int
    {
        return $this->students()->count();
    }

    public function getAvailableSeatsAttribute(): int
    {
        return max(0, $this->transport->capacity - $this->current_passengers);
    }

    public function getIsFullAttribute(): bool
    {
        return $this->current_passengers >= $this->transport->capacity;
    }
}