<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'class_id',
        'academic_year',
        'fee_type',
        'amount',
        'total_amount',
        'discount_percentage',
        'discount_amount',
        'final_amount',
        'due_date',
        'grace_period_days',
        'late_fee_percentage',
        'late_fee_amount',
        'allow_installments',
        'max_installments',
        'status',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'due_date' => 'date',
        'grace_period_days' => 'integer',
        'late_fee_percentage' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'allow_installments' => 'boolean',
        'max_installments' => 'integer',
        'is_active' => 'boolean',
    ];

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }

    public function feePayments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    public function scopeByFeeType($query, $feeType)
    {
        return $query->where('fee_type', $feeType);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('due_date', '<=', now()->addDays($days));
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
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

    public function getLateFeeAmountAttribute(): float
    {
        if (!$this->is_overdue) return 0;
        
        $daysOverdue = $this->days_overdue;
        if ($daysOverdue <= $this->grace_period_days) return 0;
        
        $effectiveDays = $daysOverdue - $this->grace_period_days;
        $lateFee = ($this->final_amount * $this->late_fee_percentage / 100) * $effectiveDays;
        
        return min($lateFee, $this->late_fee_amount);
    }

    public function getTotalCollectedAttribute(): float
    {
        return $this->feePayments()
            ->where('payment_status', 'paid')
            ->sum('amount_paid');
    }

    public function getTotalOutstandingAttribute(): float
    {
        return $this->final_amount - $this->total_collected;
    }

    public function getCollectionPercentageAttribute(): float
    {
        if ($this->final_amount == 0) return 0;
        return round(($this->total_collected / $this->final_amount) * 100, 2);
    }
}
