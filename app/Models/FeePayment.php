<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_no',
        'student_id',
        'fee_structure_id',
        'amount',
        'amount_paid',
        'amount_due',
        'amount_total',
        'discount_amount',
        'late_fee_amount',
        'fine_amount',
        'balance_amount',
        'payment_method',
        'transaction_id',
        'bank_name',
        'check_number',
        'mobile_money_provider',
        'mobile_money_number',
        'payment_status',
        'payment_date',
        'due_date',
        'installment_number',
        'payment_notes',
        'receipt_notes',
        'receipt_number',
        'collected_by',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'amount_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'payment_date' => 'date',
        'due_date' => 'date',
        'installment_number' => 'integer',
        'is_active' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('due_date', '<=', now()->addDays($days));
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < now();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) return 0;
        return now()->diffInDays($this->due_date);
    }

    public function getPaymentPercentageAttribute(): float
    {
        if ($this->amount_total == 0) return 0;
        return round(($this->amount_paid / $this->amount_total) * 100, 2);
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->amount_total - $this->amount_paid;
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->amount_paid >= $this->amount_total;
    }

    public function getPaymentMethodDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->payment_method));
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->payment_status));
    }
}
