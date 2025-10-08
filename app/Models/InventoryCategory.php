<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'icon', 'color',
        'is_active', 'sort_order', 'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
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

    public function getItemCountAttribute(): int
    {
        return $this->items()->count();
    }

    public function getActiveItemCountAttribute(): int
    {
        return $this->items()->where('status', 'active')->count();
    }

    // Methods
    public function generateCode(): string
    {
        $prefix = strtoupper(substr($this->name, 0, 3));
        $count = static::where('code', 'like', $prefix . '%')->count() + 1;
        return $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function canBeDeleted(): bool
    {
        return $this->items()->count() === 0;
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
