<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassFeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'academic_year',
        'tuition_fee',
        'registration_fee',
        'library_fee',
        'laboratory_fee',
        'sports_fee',
        'technology_fee',
        'examination_fee',
        'activity_fee',
        'transport_fee',
        'hostel_fee',
        'meal_fee',
        'uniform_fee',
        'book_fee',
        'miscellaneous_fee',
        'total_mandatory_fees',
        'total_optional_fees',
        'total_fees',
        'payment_frequency',
        'installments_allowed',
        'late_fee_percentage',
        'grace_period_days',
        'is_active',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'tuition_fee' => 'decimal:2',
        'registration_fee' => 'decimal:2',
        'library_fee' => 'decimal:2',
        'laboratory_fee' => 'decimal:2',
        'sports_fee' => 'decimal:2',
        'technology_fee' => 'decimal:2',
        'examination_fee' => 'decimal:2',
        'activity_fee' => 'decimal:2',
        'transport_fee' => 'decimal:2',
        'hostel_fee' => 'decimal:2',
        'meal_fee' => 'decimal:2',
        'uniform_fee' => 'decimal:2',
        'book_fee' => 'decimal:2',
        'miscellaneous_fee' => 'decimal:2',
        'total_mandatory_fees' => 'decimal:2',
        'total_optional_fees' => 'decimal:2',
        'total_fees' => 'decimal:2',
        'late_fee_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    // Relationships
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('effective_from', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('effective_to')
                          ->orWhere('effective_to', '>=', now());
                    });
    }

    public function scopeForAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    // Mutators and Accessors
    public function setTotalFeesAttribute()
    {
        $mandatoryFees = collect([
            'tuition_fee', 'registration_fee', 'library_fee', 
            'laboratory_fee', 'examination_fee'
        ])->sum(fn($fee) => $this->attributes[$fee] ?? 0);

        $optionalFees = collect([
            'sports_fee', 'technology_fee', 'activity_fee', 
            'transport_fee', 'hostel_fee', 'meal_fee', 
            'uniform_fee', 'book_fee', 'miscellaneous_fee'
        ])->sum(fn($fee) => $this->attributes[$fee] ?? 0);

        $this->attributes['total_mandatory_fees'] = $mandatoryFees;
        $this->attributes['total_optional_fees'] = $optionalFees;
        $this->attributes['total_fees'] = $mandatoryFees + $optionalFees;
    }

    // Helper methods
    public function getMandatoryFeesArray(): array
    {
        return [
            'tuition_fee' => $this->tuition_fee,
            'registration_fee' => $this->registration_fee,
            'library_fee' => $this->library_fee,
            'laboratory_fee' => $this->laboratory_fee,
            'examination_fee' => $this->examination_fee,
        ];
    }

    public function getOptionalFeesArray(): array
    {
        return [
            'sports_fee' => $this->sports_fee,
            'technology_fee' => $this->technology_fee,
            'activity_fee' => $this->activity_fee,
            'transport_fee' => $this->transport_fee,
            'hostel_fee' => $this->hostel_fee,
            'meal_fee' => $this->meal_fee,
            'uniform_fee' => $this->uniform_fee,
            'book_fee' => $this->book_fee,
            'miscellaneous_fee' => $this->miscellaneous_fee,
        ];
    }

    public function calculateInstallmentAmount(): float
    {
        return $this->total_fees / $this->installments_allowed;
    }

    public function isEffectiveOn($date): bool
    {
        $checkDate = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
        
        return $this->effective_from <= $checkDate && 
               (is_null($this->effective_to) || $this->effective_to >= $checkDate);
    }
}