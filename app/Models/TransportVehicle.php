<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_number',
        'vehicle_type',
        'capacity',
        'driver_name',
        'driver_phone',
        'route_id',
        'status',
        'insurance_expiry',
        'registration_number',
        'model_year',
        'manufacturer',
        'color',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'insurance_expiry' => 'date',
        'model_year' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the route that this vehicle is assigned to.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class);
    }

    /**
     * Get the students assigned to this vehicle.
     */
    public function students(): HasMany
    {
        return $this->hasMany(TransportStudent::class);
    }

    /**
     * Scope to get only active vehicles.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

    /**
     * Scope to get vehicles by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('vehicle_type', $type);
    }

    /**
     * Get the available capacity of the vehicle.
     */
    public function getAvailableCapacityAttribute(): int
    {
        $assignedStudents = $this->students()->where('status', 'active')->count();
        return max(0, $this->capacity - $assignedStudents);
    }

    /**
     * Check if the vehicle has available capacity.
     */
    public function hasAvailableCapacity(): bool
    {
        return $this->available_capacity > 0;
    }

    /**
     * Get the vehicle status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'red',
            'maintenance' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Check if insurance is expired.
     */
    public function isInsuranceExpired(): bool
    {
        return $this->insurance_expiry && $this->insurance_expiry->isPast();
    }

    /**
     * Check if insurance is expiring soon (within 30 days).
     */
    public function isInsuranceExpiringSoon(): bool
    {
        return $this->insurance_expiry && $this->insurance_expiry->isFuture() && $this->insurance_expiry->diffInDays(now()) <= 30;
    }
}
