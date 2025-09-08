<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_name',
        'room_type_id',
        'building',
        'floor',
        'wing',
        'capacity',
        'current_occupancy',
        'room_size',
        'furniture',
        'amenities',
        'air_conditioning',
        'heating',
        'internet',
        'bathroom_type',
        'kitchen_facility',
        'laundry_facility',
        'monthly_rent',
        'currency',
        'rent_type',
        'security_deposit',
        'utility_charges',
        'status',
        'is_active',
        'description',
        'rules_regulations',
        'notes',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_occupancy' => 'integer',
        'room_size' => 'decimal:2',
        'amenities' => 'array',
        'air_conditioning' => 'boolean',
        'heating' => 'boolean',
        'internet' => 'boolean',
        'monthly_rent' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'utility_charges' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'hostel_room_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByRoomType($query, $roomTypeId)
    {
        return $query->where('room_type_id', $roomTypeId);
    }

    public function scopeByBuilding($query, $building)
    {
        return $query->where('building', $building);
    }

    public function scopeByFloor($query, $floor)
    {
        return $query->where('floor', $floor);
    }

    public function scopeByWing($query, $wing)
    {
        return $query->where('wing', $wing);
    }

    public function scopeAvailable($query)
    {
        return $query->where('current_occupancy', '<', 'capacity');
    }

    public function scopeByCapacity($query, $minCapacity, $maxCapacity)
    {
        return $query->whereBetween('capacity', [$minCapacity, $maxCapacity]);
    }

    public function scopeByRentRange($query, $minRent, $maxRent)
    {
        return $query->whereBetween('monthly_rent', [$minRent, $maxRent]);
    }

    public function scopeWithAmenity($query, $amenity)
    {
        return $query->whereJsonContains('amenities', $amenity);
    }

    public function scopeByOccupancy($query, $occupancy)
    {
        return $query->where('current_occupancy', $occupancy);
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'available' => 'success',
            'occupied' => 'warning',
            'maintenance' => 'danger',
            'reserved' => 'info',
            'cleaning' => 'secondary',
            'inactive' => 'dark',
            default => 'secondary'
        };
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->current_occupancy < $this->capacity;
    }

    public function getAvailableBedsAttribute(): int
    {
        return max(0, $this->capacity - $this->current_occupancy);
    }

    public function getOccupancyPercentageAttribute(): float
    {
        if ($this->capacity == 0) return 0;
        return round(($this->current_occupancy / $this->capacity) * 100, 2);
    }

    public function getRoomSizeDisplayAttribute(): string
    {
        if (!$this->room_size) return 'N/A';
        return $this->room_size . ' sq ft';
    }

    public function getMonthlyRentDisplayAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->monthly_rent, 2);
    }

    public function getSecurityDepositDisplayAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->security_deposit, 2);
    }

    public function getUtilityChargesDisplayAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->utility_charges, 2);
    }

    public function getTotalRentDisplayAttribute(): string
    {
        $total = $this->monthly_rent + $this->utility_charges;
        return $this->currency . ' ' . number_format($total, 2);
    }

    public function getFullRoomNameAttribute(): string
    {
        $name = $this->building;
        if ($this->floor) $name .= ' Floor ' . $this->floor;
        if ($this->wing) $name .= ' Wing ' . $this->wing;
        $name .= ' Room ' . $this->room_number;
        return $name;
    }

    public function getAmenitiesDisplayAttribute(): string
    {
        if (!$this->amenities || empty($this->amenities)) {
            return 'No amenities';
        }
        return implode(', ', $this->amenities);
    }

    public function getFurnitureDisplayAttribute(): string
    {
        if (!$this->furniture) return 'No furniture';
        return $this->furniture;
    }

    public function getBathroomTypeDisplayAttribute(): string
    {
        if (!$this->bathroom_type) return 'N/A';
        return ucwords(str_replace('_', ' ', $this->bathroom_type));
    }

    public function getKitchenFacilityDisplayAttribute(): string
    {
        return $this->kitchen_facility ? 'Available' : 'Not Available';
    }

    public function getLaundryFacilityDisplayAttribute(): string
    {
        return $this->laundry_facility ? 'Available' : 'Not Available';
    }

    public function getAirConditioningDisplayAttribute(): string
    {
        return $this->air_conditioning ? 'Available' : 'Not Available';
    }

    public function getHeatingDisplayAttribute(): string
    {
        return $this->heating ? 'Available' : 'Not Available';
    }

    public function getInternetDisplayAttribute(): string
    {
        return $this->internet ? 'Available' : 'Not Available';
    }

    public function getRentTypeDisplayAttribute(): string
    {
        if (!$this->rent_type) return 'N/A';
        return ucwords(str_replace('_', ' ', $this->rent_type));
    }

    public function canAddStudent(): bool
    {
        return $this->is_available && $this->is_active && $this->status === 'available';
    }

    public function addStudent(): bool
    {
        if (!$this->canAddStudent()) {
            return false;
        }
        
        $this->increment('current_occupancy');
        
        if ($this->current_occupancy >= $this->capacity) {
            $this->status = 'occupied';
        }
        
        $this->save();
        return true;
    }

    public function removeStudent(): bool
    {
        if ($this->current_occupancy <= 0) {
            return false;
        }
        
        $this->decrement('current_occupancy');
        
        if ($this->current_occupancy < $this->capacity) {
            $this->status = 'available';
        }
        
        $this->save();
        return true;
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->students()->count();
    }

    public function getRoommatesAttribute(): array
    {
        return $this->students()->pluck('name')->toArray();
    }
}
