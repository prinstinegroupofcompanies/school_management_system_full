<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'sku', 'barcode', 'description', 'category_id', 'supplier_id',
        'unit_of_measure', 'unit_cost', 'selling_price', 'current_stock',
        'minimum_stock', 'maximum_stock', 'reorder_level', 'reorder_quantity',
        'location', 'shelf', 'expiry_date', 'last_restocked', 'status',
        'is_trackable', 'requires_approval', 'specifications', 'images', 'metadata'
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
        'reorder_level' => 'integer',
        'reorder_quantity' => 'integer',
        'expiry_date' => 'date',
        'last_restocked' => 'date',
        'is_trackable' => 'boolean',
        'requires_approval' => 'boolean',
        'specifications' => 'array',
        'images' => 'array',
        'metadata' => 'array'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(InventorySupplier::class, 'supplier_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'item_id');
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

    public function scopeDiscontinued($query)
    {
        return $query->where('status', 'discontinued');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= minimum_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', 0);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', Carbon::now()->addDays($days));
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<', Carbon::now());
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'discontinued' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->current_stock <= $this->minimum_stock) {
            return 'low_stock';
        } elseif ($this->maximum_stock && $this->current_stock >= $this->maximum_stock) {
            return 'overstock';
        }
        return 'normal';
    }

    public function getStockStatusColorAttribute(): string
    {
        return match($this->stock_status) {
            'out_of_stock' => 'danger',
            'low_stock' => 'warning',
            'overstock' => 'info',
            'normal' => 'success',
            default => 'secondary'
        };
    }

    public function getStockStatusTextAttribute(): string
    {
        return match($this->stock_status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'overstock' => 'Overstock',
            'normal' => 'Normal',
            default => 'Unknown'
        };
    }

    public function getTotalValueAttribute(): float
    {
        return $this->current_stock * $this->unit_cost;
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(Carbon::now()) <= 30;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getNeedsReorderAttribute(): bool
    {
        return $this->current_stock <= $this->reorder_level;
    }

    public function getCategoryNameAttribute(): string
    {
        return $this->category?->name ?? 'No Category';
    }

    public function getSupplierNameAttribute(): string
    {
        return $this->supplier?->name ?? 'No Supplier';
    }

    // Methods
    public function generateSku(): string
    {
        $categoryCode = $this->category?->code ?? 'GEN';
        $count = static::where('sku', 'like', $categoryCode . '%')->count() + 1;
        return $categoryCode . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function updateStock(int $quantity, string $type = 'adjustment'): bool
    {
        $stockBefore = $this->current_stock;
        $stockAfter = match($type) {
            'in', 'return' => $stockBefore + $quantity,
            'out', 'damage', 'loss' => max(0, $stockBefore - $quantity),
            'adjustment' => $quantity,
            default => $stockBefore
        };

        $this->update(['current_stock' => $stockAfter]);

        // Create transaction record
        $this->transactions()->create([
            'transaction_number' => $this->generateTransactionNumber(),
            'type' => $type,
            'quantity' => abs($quantity),
            'unit_cost' => $this->unit_cost,
            'total_cost' => abs($quantity) * $this->unit_cost,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'status' => 'completed',
            'created_by' => auth()->id(),
            'transaction_date' => now()
        ]);

        return true;
    }

    public function generateTransactionNumber(): string
    {
        $prefix = 'TXN';
        $count = InventoryTransaction::count() + 1;
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    public function canBeDeleted(): bool
    {
        return $this->transactions()->count() === 0;
    }

    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    public function discontinue(): void
    {
        $this->update(['status' => 'discontinued']);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function isDiscontinued(): bool
    {
        return $this->status === 'discontinued';
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }

    public function isOverstock(): bool
    {
        return $this->maximum_stock && $this->current_stock >= $this->maximum_stock;
    }
}
