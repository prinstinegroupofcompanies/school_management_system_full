<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventorySupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'contact_person', 'email', 'phone', 'address',
        'city', 'state', 'country', 'postal_code', 'website', 'notes',
        'status', 'credit_limit', 'payment_terms_days', 'tax_id', 'metadata'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_terms_days' => 'integer',
        'metadata' => 'array'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'supplier_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'supplier_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'warning',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code
        ]);
        return implode(', ', $parts);
    }

    public function getItemCountAttribute(): int
    {
        return $this->items()->count();
    }

    public function getActiveItemCountAttribute(): int
    {
        return $this->items()->where('status', 'active')->count();
    }

    public function getTotalPurchaseValueAttribute(): float
    {
        return $this->transactions()
            ->where('type', 'in')
            ->where('status', 'completed')
            ->sum('total_cost');
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
        return $this->items()->count() === 0 && $this->transactions()->count() === 0;
    }

    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
