<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_number',
        'registration_number',
        'vehicle_type',
        'make',
        'model',
        'year_of_manufacture',
        'color',
        'seating_capacity',
        'engine_number',
        'chassis_number',
        'fuel_type',
        'fuel_efficiency',
        'insurance_number',
        'insurance_expiry_date',
        'fitness_certificate_number',
        'fitness_expiry_date',
        'permit_number',
        'permit_expiry_date',
        'driver_name',
        'driver_license_number',
        'driver_license_expiry_date',
        'driver_phone',
        'driver_address',
        'conductor_name',
        'conductor_phone',
        'conductor_address',
        'purchase_price',
        'purchase_currency',
        'purchase_date',
        'current_value',
        'current_currency',
        'status',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'year_of_manufacture' => 'integer',
        'seating_capacity' => 'integer',
        'fuel_efficiency' => 'decimal:2',
        'insurance_expiry_date' => 'date',
        'fitness_expiry_date' => 'date',
        'permit_expiry_date' => 'date',
        'driver_license_expiry_date' => 'date',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
        'purchase_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function transportRoute(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByVehicleType($query, $vehicleType)
    {
        return $query->where('vehicle_type', $vehicleType);
    }

    public function scopeByFuelType($query, $fuelType)
    {
        return $query->where('fuel_type', $fuelType);
    }

    public function scopeBySeatingCapacity($query, $minCapacity, $maxCapacity)
    {
        return $query->whereBetween('seating_capacity', [$minCapacity, $maxCapacity]);
    }

    public function scopeByMake($query, $make)
    {
        return $query->where('make', 'like', "%{$make}%");
    }

    public function scopeByModel($query, $model)
    {
        return $query->where('model', 'like', "%{$model}%");
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year_of_manufacture', $year);
    }

    public function scopeByRoute($query, $routeId)
    {
        return $query->where('route_id', $routeId);
    }

    public function scopeInsuranceExpiringSoon($query, $days = 30)
    {
        return $query->where('insurance_expiry_date', '<=', now()->addDays($days))
                    ->where('insurance_expiry_date', '>', now());
    }

    public function scopeFitnessExpiringSoon($query, $days = 30)
    {
        return $query->where('fitness_expiry_date', '<=', now()->addDays($days))
                    ->where('fitness_expiry_date', '>', now());
    }

    public function scopePermitExpiringSoon($query, $days = 30)
    {
        return $query->where('permit_expiry_date', '<=', now()->addDays($days))
                    ->where('permit_expiry_date', '>', now());
    }

    public function scopeDriverLicenseExpiringSoon($query, $days = 30)
    {
        return $query->where('driver_license_expiry_date', '<=', now()->addDays($days))
                    ->where('driver_license_expiry_date', '>', now());
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
            'repair' => 'danger',
            'out_of_service' => 'dark',
            'reserved' => 'info',
            default => 'secondary'
        };
    }

    public function getVehicleTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->vehicle_type));
    }

    public function getFuelTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->fuel_type));
    }

    public function getIsInsuranceExpiredAttribute(): bool
    {
        return $this->insurance_expiry_date < now();
    }

    public function getIsFitnessExpiredAttribute(): bool
    {
        return $this->fitness_expiry_date < now();
    }

    public function getIsPermitExpiredAttribute(): bool
    {
        return $this->permit_expiry_date < now();
    }

    public function getIsDriverLicenseExpiredAttribute(): bool
    {
        return $this->driver_license_expiry_date < now();
    }

    public function getDaysUntilInsuranceExpiryAttribute(): int
    {
        if ($this->is_insurance_expired) return 0;
        return now()->diffInDays($this->insurance_expiry_date, false);
    }

    public function getDaysUntilFitnessExpiryAttribute(): int
    {
        if ($this->is_fitness_expired) return 0;
        return now()->diffInDays($this->fitness_expiry_date, false);
    }

    public function getDaysUntilPermitExpiryAttribute(): int
    {
        if ($this->is_permit_expired) return 0;
        return now()->diffInDays($this->permit_expiry_date, false);
    }

    public function getDaysUntilDriverLicenseExpiryAttribute(): int
    {
        if ($this->is_driver_license_expired) return 0;
        return now()->diffInDays($this->driver_license_expiry_date, false);
    }

    public function getAgeAttribute(): int
    {
        return now()->year - $this->year_of_manufacture;
    }

    public function getAgeDisplayAttribute(): string
    {
        $age = $this->age;
        if ($age == 0) return 'New';
        if ($age == 1) return '1 year old';
        return $age . ' years old';
    }

    public function getPurchasePriceDisplayAttribute(): string
    {
        return $this->purchase_currency . ' ' . number_format($this->purchase_price, 2);
    }

    public function getCurrentValueDisplayAttribute(): string
    {
        return $this->current_currency . ' ' . number_format($this->current_value, 2);
    }

    public function getFuelEfficiencyDisplayAttribute(): string
    {
        if (!$this->fuel_efficiency) return 'N/A';
        return $this->fuel_efficiency . ' km/l';
    }

    public function getFullVehicleNameAttribute(): string
    {
        return $this->year_of_manufacture . ' ' . $this->make . ' ' . $this->model;
    }

    public function getDriverInfoAttribute(): string
    {
        $info = $this->driver_name;
        if ($this->driver_phone) {
            $info .= ' (' . $this->driver_phone . ')';
        }
        return $info;
    }

    public function getConductorInfoAttribute(): string
    {
        if (!$this->conductor_name) return 'No conductor assigned';
        
        $info = $this->conductor_name;
        if ($this->conductor_phone) {
            $info .= ' (' . $this->conductor_phone . ')';
        }
        return $info;
    }

    public function getIsOperationalAttribute(): bool
    {
        return $this->is_active && 
               $this->status === 'active' && 
               !$this->is_insurance_expired &&
               !$this->is_fitness_expired &&
               !$this->is_permit_expired &&
               !$this->is_driver_license_expired;
    }

    public function getOperationalStatusAttribute(): string
    {
        if ($this->is_operational) return 'Operational';
        
        if ($this->is_insurance_expired) return 'Insurance Expired';
        if ($this->is_fitness_expired) return 'Fitness Expired';
        if ($this->is_permit_expired) return 'Permit Expired';
        if ($this->is_driver_license_expired) return 'Driver License Expired';
        
        return 'Not Operational';
    }

    public function getOperationalStatusColorAttribute(): string
    {
        return $this->is_operational ? 'success' : 'danger';
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->transportRoute ? $this->transportRoute->current_capacity : 0;
    }

    public function getAvailableSeatsAttribute(): int
    {
        return max(0, $this->seating_capacity - $this->total_students);
    }

    public function getOccupancyPercentageAttribute(): float
    {
        if ($this->seating_capacity == 0) return 0;
        return round(($this->total_students / $this->seating_capacity) * 100, 2);
    }
}
