<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'type', 'value', 'currency',
        'min_amount', 'max_amount', 'min_grade', 'max_grade',
        'min_attendance', 'max_attendance', 'start_date', 'end_date',
        'is_active', 'is_stackable', 'auto_apply', 'created_by', 'notes'
    ];

    protected $casts = [
        'value' => 'decimal:2', 'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2', 'min_grade' => 'decimal:2',
        'max_grade' => 'decimal:2', 'min_attendance' => 'decimal:2',
        'max_attendance' => 'decimal:2', 'start_date' => 'date',
        'end_date' => 'date', 'is_active' => 'boolean',
        'is_stackable' => 'boolean', 'auto_apply' => 'boolean'
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    public function scopeByValueRange($query, $minValue, $maxValue)
    {
        return $query->whereBetween('value', [$minValue, $maxValue]);
    }

    public function scopeByAmountRange($query, $minAmount, $maxAmount)
    {
        return $query->whereBetween('min_amount', [$minAmount, $maxAmount])
                    ->orWhereBetween('max_amount', [$minAmount, $maxAmount]);
    }

    public function scopeByGradeRange($query, $minGrade, $maxGrade)
    {
        return $query->whereBetween('min_grade', [$minGrade, $maxGrade])
                    ->orWhereBetween('max_grade', [$minGrade, $maxGrade]);
    }

    public function scopeByAttendanceRange($query, $minAttendance, $maxAttendance)
    {
        return $query->whereBetween('min_attendance', [$minAttendance, $maxAttendance])
                    ->orWhereBetween('max_attendance', [$minAttendance, $maxAttendance]);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
    }

    public function scopeCurrentlyValid($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('end_date', [
            now(),
            now()->addDays($days)
        ]);
    }

    public function scopePercentageDiscounts($query)
    {
        return $query->where('type', 'percentage');
    }

    public function scopeFixedAmountDiscounts($query)
    {
        return $query->where('type', 'fixed_amount');
    }

    public function scopeFixedValueDiscounts($query)
    {
        return $query->where('type', 'fixed_value');
    }

    public function scopeStackable($query)
    {
        return $query->where('is_stackable', true);
    }

    public function scopeNonStackable($query)
    {
        return $query->where('is_stackable', false);
    }

    public function scopeAutoApply($query)
    {
        return $query->where('auto_apply', true);
    }

    public function scopeManualApply($query)
    {
        return $query->where('auto_apply', false);
    }

    public function scopeByCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeByValueOrder($query)
    {
        return $query->orderBy('value', 'desc');
    }

    public function scopeByStartDateOrder($query)
    {
        return $query->orderBy('start_date', 'desc');
    }

    public function scopeByEndDateOrder($query)
    {
        return $query->orderBy('end_date', 'asc');
    }

    public function scopeByTypeOrder($query)
    {
        return $query->orderBy('type')->orderBy('value', 'desc');
    }

    public function getTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->type));
    }

    public function getValueDisplayAttribute(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }
        
        return $this->currency . ' ' . number_format($this->value, 2);
    }

    public function getMinAmountDisplayAttribute(): string
    {
        if (!$this->min_amount) return 'No minimum';
        return $this->currency . ' ' . number_format($this->min_amount, 2);
    }

    public function getMaxAmountDisplayAttribute(): string
    {
        if (!$this->max_amount) return 'No maximum';
        return $this->currency . ' ' . number_format($this->max_amount, 2);
    }

    public function getMinGradeDisplayAttribute(): string
    {
        if (!$this->min_grade) return 'No minimum grade';
        return $this->min_grade . '/4.0';
    }

    public function getMaxGradeDisplayAttribute(): string
    {
        if (!$this->max_grade) return 'No maximum grade';
        return $this->max_grade . '/4.0';
    }

    public function getMinAttendanceDisplayAttribute(): string
    {
        if (!$this->min_attendance) return 'No minimum attendance';
        return $this->min_attendance . '%';
    }

    public function getMaxAttendanceDisplayAttribute(): string
    {
        if (!$this->max_attendance) return 'No maximum attendance';
        return $this->max_attendance . '%';
    }

    public function getStartDateDisplayAttribute(): string
    {
        if (!$this->start_date) return 'No start date';
        return $this->start_date->format('M d, Y');
    }

    public function getEndDateDisplayAttribute(): string
    {
        if (!$this->end_date) return 'No end date';
        return $this->end_date->format('M d, Y');
    }

    public function getNotesDisplayAttribute(): string
    {
        return $this->notes ?: 'No additional notes';
    }

    public function getCreatedByDisplayAttribute(): string
    {
        if (!$this->created_by) return 'System';
        return 'Unknown'; // You can add relationship if needed
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->is_active;
    }

    public function getIsStackableAttribute(): bool
    {
        return $this->is_stackable;
    }

    public function getIsAutoApplyAttribute(): bool
    {
        return $this->auto_apply;
    }

    public function getIsCurrentlyValidAttribute(): bool
    {
        if (!$this->start_date || !$this->end_date) return false;
        return $this->start_date <= now() && $this->end_date >= now();
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date && $this->end_date < now();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->end_date || $this->is_expired) return false;
        
        $expiryDate = now()->addDays(30);
        return $this->end_date <= $expiryDate;
    }

    public function getIsPercentageDiscountAttribute(): bool
    {
        return $this->type === 'percentage';
    }

    public function getIsFixedAmountDiscountAttribute(): bool
    {
        return $this->type === 'fixed_amount';
    }

    public function getIsFixedValueDiscountAttribute(): bool
    {
        return $this->type === 'fixed_value';
    }

    public function getDaysUntilStartAttribute(): int
    {
        if (!$this->start_date) return 0;
        
        if ($this->start_date < now()) return 0;
        
        return now()->diffInDays($this->start_date, false);
    }

    public function getDaysUntilEndAttribute(): int
    {
        if (!$this->end_date || $this->is_expired) return 0;
        return now()->diffInDays($this->end_date, false);
    }

    public function getDaysSinceCreationAttribute(): int
    {
        return now()->diffInDays($this->created_at);
    }

    public function getDaysSinceLastUpdateAttribute(): int
    {
        return now()->diffInDays($this->updated_at);
    }

    public function getDiscountSummaryAttribute(): string
    {
        $summary = $this->name . ' (' . $this->code . ')';
        
        $summary .= ' - ' . $this->type_display . ' discount';
        
        if ($this->type === 'percentage') {
            $summary .= ' of ' . $this->value . '%';
        } else {
            $summary .= ' of ' . $this->value_display;
        }
        
        if ($this->is_expired) {
            $summary .= ' - EXPIRED';
        } elseif ($this->is_expiring_soon) {
            $summary .= ' - Expiring Soon';
        }
        
        return $summary;
    }

    public function getEligibilitySummaryAttribute(): string
    {
        $summary = [];
        
        if ($this->min_amount) {
            $summary[] = 'Min Amount: ' . $this->min_amount_display;
        }
        
        if ($this->max_amount) {
            $summary[] = 'Max Amount: ' . $this->max_amount_display;
        }
        
        if ($this->min_grade) {
            $summary[] = 'Min Grade: ' . $this->min_grade_display;
        }
        
        if ($this->max_grade) {
            $summary[] = 'Max Grade: ' . $this->max_grade_display;
        }
        
        if ($this->min_attendance) {
            $summary[] = 'Min Attendance: ' . $this->min_attendance_display;
        }
        
        if ($this->max_attendance) {
            $summary[] = 'Max Attendance: ' . $this->max_attendance_display;
        }
        
        return empty($summary) ? 'No eligibility criteria' : implode(' | ', $summary);
    }

    public function getTimelineSummaryAttribute(): string
    {
        $summary = [];
        
        if ($this->start_date) {
            $summary[] = 'Start: ' . $this->start_date_display;
        }
        
        if ($this->end_date) {
            $summary[] = 'End: ' . $this->end_date_display;
        }
        
        if ($this->is_currently_valid) {
            $summary[] = 'Currently Valid';
        } elseif ($this->is_expired) {
            $summary[] = 'Expired';
        } elseif ($this->is_expiring_soon) {
            $summary[] = 'Expiring Soon';
        }
        
        return empty($summary) ? 'No timeline information' : implode(' | ', $summary);
    }

    public function getBehaviorSummaryAttribute(): string
    {
        $summary = [];
        
        if ($this->is_stackable) {
            $summary[] = 'Stackable';
        } else {
            $summary[] = 'Non-Stackable';
        }
        
        if ($this->auto_apply) {
            $summary[] = 'Auto-Apply';
        } else {
            $summary[] = 'Manual Apply';
        }
        
        return implode(' | ', $summary);
    }

    public function canBeEdited(): bool
    {
        return $this->is_active && $this->students()->count() === 0;
    }

    public function canBeDeleted(): bool
    {
        return $this->students()->count() === 0;
    }

    public function canBeActivated(): bool
    {
        return !$this->is_active;
    }

    public function canBeDeactivated(): bool
    {
        return $this->is_active && $this->students()->count() === 0;
    }

    public function canBeApplied(): bool
    {
        return $this->is_active && $this->is_currently_valid;
    }

    public function canBeStacked(): bool
    {
        return $this->is_stackable;
    }

    public function activate(): void
    {
        if ($this->can_be_activated) {
            $this->is_active = true;
            $this->save();
        }
    }

    public function deactivate(): void
    {
        if ($this->can_be_deactivated) {
            $this->is_active = false;
            $this->save();
        }
    }

    public function updateValue(float $newValue): void
    {
        if ($newValue >= 0) {
            $this->value = $newValue;
            $this->save();
        }
    }

    public function updateDateRange(string $startDate, string $endDate): void
    {
        $this->start_date = $startDate;
        $this->end_date = $endDate;
        $this->save();
    }

    public function extendEndDate(string $newEndDate): void
    {
        $this->end_date = $newEndDate;
        $this->save();
    }

    public function toggleStackable(): void
    {
        $this->is_stackable = !$this->is_stackable;
        $this->save();
    }

    public function toggleAutoApply(): void
    {
        $this->auto_apply = !$this->auto_apply;
        $this->save();
    }

    public function addNote(string $note): void
    {
        $this->notes = ($this->notes ? $this->notes . "\n" : '') . $note;
        $this->save();
    }

    public function calculateDiscount(float $amount, float $grade = null, float $attendance = null): float
    {
        if (!$this->can_be_applied) {
            return 0;
        }
        
        // Check eligibility criteria
        if ($this->min_amount && $amount < $this->min_amount) {
            return 0;
        }
        
        if ($this->max_amount && $amount > $this->max_amount) {
            return 0;
        }
        
        if ($this->min_grade && $grade && $grade < $this->min_grade) {
            return 0;
        }
        
        if ($this->max_grade && $grade && $grade > $this->max_grade) {
            return 0;
        }
        
        if ($this->min_attendance && $attendance && $attendance < $this->min_attendance) {
            return 0;
        }
        
        if ($this->max_attendance && $attendance && $attendance > $this->max_attendance) {
            return 0;
        }
        
        // Calculate discount
        if ($this->type === 'percentage') {
            return ($amount * $this->value) / 100;
        } elseif ($this->type === 'fixed_amount') {
            return min($this->value, $amount);
        } elseif ($this->type === 'fixed_value') {
            return $this->value;
        }
        
        return 0;
    }

    public function getDiscountStatistics(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
            'currency' => $this->currency,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'min_grade' => $this->min_grade,
            'max_grade' => $this->max_grade,
            'min_attendance' => $this->min_attendance,
            'max_attendance' => $this->max_attendance,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'is_stackable' => $this->is_stackable,
            'auto_apply' => $this->auto_apply,
            'is_currently_valid' => $this->is_currently_valid,
            'is_expired' => $this->is_expired,
            'is_expiring_soon' => $this->is_expiring_soon,
            'is_percentage_discount' => $this->is_percentage_discount,
            'is_fixed_amount_discount' => $this->is_fixed_amount_discount,
            'is_fixed_value_discount' => $this->is_fixed_value_discount,
            'days_until_start' => $this->days_until_start,
            'days_until_end' => $this->days_until_end,
            'days_since_creation' => $this->days_since_creation,
            'days_since_last_update' => $this->days_since_last_update,
            'can_be_edited' => $this->can_be_edited,
            'can_be_deleted' => $this->can_be_deleted,
            'can_be_activated' => $this->can_be_activated,
            'can_be_deactivated' => $this->can_be_deactivated,
            'can_be_applied' => $this->can_be_applied,
            'can_be_stacked' => $this->can_be_stacked
        ];
    }
}
