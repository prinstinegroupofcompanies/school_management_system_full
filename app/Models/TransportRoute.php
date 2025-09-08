<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

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
        'distance_km' => 'decimal:2',
        'estimated_duration_minutes' => 'integer',
        'morning_pickup_time' => 'datetime',
        'morning_dropoff_time' => 'datetime',
        'afternoon_pickup_time' => 'datetime',
        'afternoon_dropoff_time' => 'datetime',
        'fare_amount' => 'decimal:2',
        'max_capacity' => 'integer',
        'current_capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function stops(): HasMany
    {
        return $this->hasMany(RouteStop::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByFareType($query, $fareType)
    {
        return $query->where('fare_type', $fareType);
    }

    public function scopeAvailable($query)
    {
        return $query->where('current_capacity', '<', 'max_capacity');
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where(function($q) use ($location) {
            $q->where('start_location', 'like', "%{$location}%")
              ->orWhere('end_location', 'like', "%{$location}%");
        });
    }

    public function scopeByDistance($query, $minDistance, $maxDistance)
    {
        return $query->whereBetween('distance_km', [$minDistance, $maxDistance]);
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'maintenance' => 'warning',
            'suspended' => 'danger',
            'planned' => 'info',
            default => 'secondary'
        };
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->current_capacity < $this->max_capacity;
    }

    public function getAvailableSeatsAttribute(): int
    {
        return max(0, $this->max_capacity - $this->current_capacity);
    }

    public function getOccupancyPercentageAttribute(): float
    {
        if ($this->max_capacity == 0) return 0;
        return round(($this->current_capacity / $this->max_capacity) * 100, 2);
    }

    public function getDistanceDisplayAttribute(): string
    {
        return $this->distance_km . ' km';
    }

    public function getDurationDisplayAttribute(): string
    {
        if (!$this->estimated_duration_minutes) return 'N/A';
        
        $hours = intval($this->estimated_duration_minutes / 60);
        $minutes = $this->estimated_duration_minutes % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    public function getMorningScheduleAttribute(): string
    {
        if (!$this->morning_pickup_time || !$this->morning_dropoff_time) {
            return 'No morning service';
        }
        
        return $this->morning_pickup_time->format('H:i') . ' - ' . 
               $this->morning_dropoff_time->format('H:i');
    }

    public function getAfternoonScheduleAttribute(): string
    {
        if (!$this->afternoon_pickup_time || !$this->afternoon_dropoff_time) {
            return 'No afternoon service';
        }
        
        return $this->afternoon_pickup_time->format('H:i') . ' - ' . 
               $this->afternoon_dropoff_time->format('H:i');
    }

    public function getFareDisplayAttribute(): string
    {
        return 'LRD ' . number_format($this->fare_amount, 2);
    }

    public function getRouteSummaryAttribute(): string
    {
        return $this->start_location . ' → ' . $this->end_location;
    }

    public function canAddStudent(): bool
    {
        return $this->is_available && $this->is_active;
    }

    public function addStudent(): bool
    {
        if (!$this->canAddStudent()) {
            return false;
        }
        
        $this->increment('current_capacity');
        return true;
    }

    public function removeStudent(): bool
    {
        if ($this->current_capacity <= 0) {
            return false;
        }
        
        $this->decrement('current_capacity');
        return true;
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->students()->count();
    }

    public function getTotalVehiclesAttribute(): int
    {
        return $this->vehicles()->count();
    }
}
