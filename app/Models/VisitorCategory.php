<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitorCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'icon', 'color',
        'requires_approval', 'requires_escort', 'max_visits_per_day',
        'allowed_areas', 'restricted_areas', 'is_active', 'sort_order', 'metadata'
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'requires_escort' => 'boolean',
        'max_visits_per_day' => 'integer',
        'allowed_areas' => 'array',
        'restricted_areas' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array'
    ];

    public function visitors(): HasMany
    {
        return $this->hasMany(Visitor::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    public function scopeRequiresEscort($query)
    {
        return $query->where('requires_escort', true);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getVisitorCountAttribute(): int
    {
        return $this->visitors()->count();
    }

    public function getActiveVisitorCountAttribute(): int
    {
        return $this->visitors()->where('is_blacklisted', false)->count();
    }

    public function getFormattedAllowedAreasAttribute(): string
    {
        if (!$this->allowed_areas) {
            return 'All Areas';
        }
        return implode(', ', $this->allowed_areas);
    }

    public function getFormattedRestrictedAreasAttribute(): string
    {
        if (!$this->restricted_areas) {
            return 'None';
        }
        return implode(', ', $this->restricted_areas);
    }

    // Methods
    public function generateCode(): string
    {
        $prefix = 'VC';
        $count = static::count() + 1;
        return $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function canBeDeleted(): bool
    {
        return $this->visitors()->count() === 0;
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function isAreaAllowed(string $area): bool
    {
        if (!$this->allowed_areas) {
            return true; // If no restrictions, all areas allowed
        }
        return in_array($area, $this->allowed_areas);
    }

    public function isAreaRestricted(string $area): bool
    {
        if (!$this->restricted_areas) {
            return false; // If no restrictions, no areas restricted
        }
        return in_array($area, $this->restricted_areas);
    }

    public function canAccessArea(string $area): bool
    {
        return $this->isAreaAllowed($area) && !$this->isAreaRestricted($area);
    }
}
