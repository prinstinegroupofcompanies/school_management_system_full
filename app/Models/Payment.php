<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'payable_type',
        'payable_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'description',
        'payment_date',
        'reference_number',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the student that owns the payment
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the payable item (polymorphic relationship)
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the status color for display
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'green',
            'pending' => 'yellow',
            'failed' => 'red',
            'refunded' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Get the payment method display name
     */
    public function getPaymentMethodDisplayAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'mobile_money' => 'Mobile Money',
            'card' => 'Card Payment',
            default => ucfirst($this->payment_method),
        };
    }

    /**
     * Get the payable type display name
     */
    public function getPayableTypeDisplayAttribute(): string
    {
        return match($this->payable_type) {
            'hostel' => 'Hostel Fees',
            'transport' => 'Transport Fees',
            'library' => 'Library Fines',
            'tuition' => 'Tuition Fees',
            default => ucfirst($this->payable_type),
        };
    }

    /**
     * Scope for completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for payments by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('payable_type', $type);
    }

    /**
     * Scope for payments by student
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope for payments by date range
     */
    public function scopeByDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('payment_date', [$fromDate, $toDate]);
    }
}
