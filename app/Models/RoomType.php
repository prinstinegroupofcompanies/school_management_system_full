<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'capacity',
        'base_price',
        'currency',
        'pricing_type',
        'features',
        'amenities',
        'room_size_range',
        'bathroom_type',
        'kitchen_facility',
        'laundry_facility',
        'internet_access',
        'air_conditioning',
        'heating',
        'display_order',
        'status',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'base_price' => 'decimal:2',
        'features' => 'array',
        'amenities' => 'array',
        'room_size_range' => 'array',
        'kitchen_facility' => 'boolean',
        'laundry_facility' => 'boolean',
        'internet_access' => 'boolean',
        'air_conditioning' => 'boolean',
        'heating' => 'boolean',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(HostelRoom::class, 'room_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCapacity($query, $capacity)
    {
        return $query->where('capacity', $capacity);
    }

    public function scopeByPricingType($query, $pricingType)
    {
        return $query->where('pricing_type', $pricingType);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('base_price', [$minPrice, $maxPrice]);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    public function scopeWithAmenity($query, $amenity)
    {
        return $query->whereJsonContains('amenities', $amenity);
    }

    public function scopeWithFeature($query, $feature)
    {
        return $query->whereJsonContains('features', $feature);
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getPricingTypeDisplayAttribute(): string
    {
        if (!$this->pricing_type) return 'N/A';
        return ucwords(str_replace('_', ' ', $this->pricing_type));
    }

    public function getBathroomTypeDisplayAttribute(): string
    {
        if (!$this->bathroom_type) return 'N/A';
        return ucwords(str_replace('_', ' ', $this->bathroom_type));
    }

    public function getBasePriceDisplayAttribute(): string
        {
        return $this->currency . ' ' . number_format($this->base_price, 2);
    }

    public function getRoomSizeRangeDisplayAttribute(): string
    {
        if (!$this->room_size_range || empty($this->room_size_range)) {
            return 'N/A';
        }
        
        if (count($this->room_size_range) === 1) {
            return $this->room_size_range[0] . ' sq ft';
        }
        
        return $this->room_size_range[0] . ' - ' . $this->room_size_range[1] . ' sq ft';
    }

    public function getCapacityDisplayAttribute(): string
    {
        if ($this->capacity == 1) {
            return 'Single Occupancy';
        } elseif ($this->capacity == 2) {
            return 'Double Occupancy';
        } elseif ($this->capacity == 3) {
            return 'Triple Occupancy';
        } else {
            return $this->capacity . ' Person';
        }
    }

    public function getFeaturesDisplayAttribute(): string
    {
        if (!$this->features || empty($this->features)) {
            return 'No special features';
        }
        return implode(', ', $this->features);
    }

    public function getAmenitiesDisplayAttribute(): string
    {
        if (!$this->amenities || empty($this->amenities)) {
            return 'No amenities';
        }
        return implode(', ', $this->amenities);
    }

    public function getKitchenFacilityDisplayAttribute(): string
    {
        return $this->kitchen_facility ? 'Available' : 'Not Available';
    }

    public function getLaundryFacilityDisplayAttribute(): string
    {
        return $this->laundry_facility ? 'Available' : 'Not Available';
    }

    public function getInternetAccessDisplayAttribute(): string
    {
        return $this->internet_access ? 'Available' : 'Not Available';
    }

    public function getAirConditioningDisplayAttribute(): string
    {
        return $this->air_conditioning ? 'Available' : 'Not Available';
    }

    public function getHeatingDisplayAttribute(): string
    {
        return $this->heating ? 'Available' : 'Not Available';
    }

    public function getTotalRoomsAttribute(): int
    {
        return $this->rooms()->count();
    }

    public function getAvailableRoomsAttribute(): int
    {
        return $this->rooms()->where('status', 'available')->count();
    }

    public function getOccupiedRoomsAttribute(): int
    {
        return $this->rooms()->where('status', 'occupied')->count();
    }

    public function getMaintenanceRoomsAttribute(): int
    {
        return $this->rooms()->where('status', 'maintenance')->count();
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->rooms()->sum('current_occupancy');
    }

    public function getTotalCapacityAttribute(): int
    {
        return $this->rooms()->sum('capacity');
    }

    public function getOccupancyPercentageAttribute(): float
    {
        if ($this->total_capacity == 0) return 0;
        return round(($this->total_students / $this->total_capacity) * 100, 2);
    }

    public function getAverageRoomSizeAttribute(): float
    {
        $rooms = $this->rooms()->whereNotNull('room_size');
        if ($rooms->count() == 0) return 0;
        
        return round($rooms->avg('room_size'), 2);
    }

    public function getAverageRentAttribute(): float
    {
        $rooms = $this->rooms()->whereNotNull('monthly_rent');
        if ($rooms->count() == 0) return 0;
        
        return round($rooms->avg('monthly_rent'), 2);
    }

    public function getAverageRentDisplayAttribute(): string
    {
        if ($this->average_rent == 0) return 'N/A';
        return $this->currency . ' ' . number_format($this->average_rent, 2);
    }

    public function getPopularityScoreAttribute(): float
    {
        $totalRooms = $this->total_rooms;
        if ($totalRooms == 0) return 0;
        
        $occupiedRooms = $this->occupied_rooms;
        $maintenanceRooms = $this->maintenance_rooms;
        
        // Calculate popularity based on occupancy and maintenance status
        $score = ($occupiedRooms / $totalRooms) * 100;
        $score -= ($maintenanceRooms / $totalRooms) * 20; // Penalty for maintenance
        
        return round(max(0, min(100, $score)), 2);
    }

    public function getPopularityColorAttribute(): string
    {
        $score = $this->popularity_score;
        
        if ($score >= 80) return 'success';
        if ($score >= 60) return 'info';
        if ($score >= 40) return 'warning';
        if ($score >= 20) return 'secondary';
        return 'danger';
    }

    public function getPopularityDisplayAttribute(): string
    {
        $score = $this->popularity_score;
        
        if ($score >= 80) return 'Very Popular';
        if ($score >= 60) return 'Popular';
        if ($score >= 40) return 'Moderate';
        if ($score >= 20) return 'Low';
        return 'Very Low';
    }
}
