<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number', 'item_id', 'type', 'quantity', 'unit_cost',
        'total_cost', 'stock_before', 'stock_after', 'reference_number',
        'notes', 'status', 'created_by', 'approved_by', 'approved_at',
        'transaction_date', 'location_from', 'location_to', 'supplier_id', 'metadata'
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
        'approved_at' => 'datetime',
        'transaction_date' => 'datetime',
        'metadata' => 'array'
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(InventorySupplier::class, 'supplier_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'completed' => 'success',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'in' => 'success',
            'out' => 'danger',
            'transfer' => 'info',
            'adjustment' => 'warning',
            'return' => 'primary',
            'damage' => 'danger',
            'loss' => 'dark',
            default => 'secondary'
        };
    }

    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'transfer' => 'Transfer',
            'adjustment' => 'Adjustment',
            'return' => 'Return',
            'damage' => 'Damage',
            'loss' => 'Loss',
            default => ucfirst($this->type)
        };
    }

    public function getFormattedUnitCostAttribute(): string
    {
        return '$' . number_format($this->unit_cost, 2);
    }

    public function getFormattedTotalCostAttribute(): string
    {
        return '$' . number_format($this->total_cost, 2);
    }

    public function getStockChangeAttribute(): int
    {
        return $this->stock_after - $this->stock_before;
    }

    public function getStockChangeTextAttribute(): string
    {
        $change = $this->stock_change;
        return $change > 0 ? "+{$change}" : (string)$change;
    }

    public function getStockChangeColorAttribute(): string
    {
        $change = $this->stock_change;
        return $change > 0 ? 'success' : ($change < 0 ? 'danger' : 'secondary');
    }

    // Methods
    public function generateTransactionNumber(): string
    {
        $prefix = 'TXN';
        $count = static::count() + 1;
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    public function approve($userId): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now()
        ]);
    }

    public function reject($userId): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $userId,
            'approved_at' => now()
        ]);
    }

    public function complete(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['pending', 'rejected']);
    }

    public function canBeDeleted(): bool
    {
        return $this->status === 'pending';
    }
}
