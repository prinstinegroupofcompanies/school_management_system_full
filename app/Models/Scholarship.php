<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'type', 'amount', 'currency',
        'percentage', 'max_amount', 'min_amount', 'academic_year',
        'class_id', 'subject_id', 'eligibility_criteria', 'requirements',
        'application_deadline', 'start_date', 'end_date', 'max_recipients',
        'current_recipients', 'is_active', 'is_merit_based', 'is_need_based',
        'is_sports_based', 'is_arts_based', 'is_academic_based',
        'created_by', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2', 'percentage' => 'decimal:2',
        'max_amount' => 'decimal:2', 'min_amount' => 'decimal:2',
        'application_deadline' => 'date', 'start_date' => 'date',
        'end_date' => 'date', 'max_recipients' => 'integer',
        'current_recipients' => 'integer', 'is_active' => 'boolean',
        'is_merit_based' => 'boolean', 'is_need_based' => 'boolean',
        'is_sports_based' => 'boolean', 'is_arts_based' => 'boolean',
        'is_academic_based' => 'boolean'
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(ScholarshipApplication::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeMeritBased($query)
    {
        return $query->where('is_merit_based', true);
    }

    public function scopeNeedBased($query)
    {
        return $query->where('is_need_based', true);
    }

    public function scopeSportsBased($query)
    {
        return $query->where('is_sports_based', true);
    }

    public function scopeArtsBased($query)
    {
        return $query->where('is_arts_based', true);
    }

    public function scopeAcademicBased($query)
    {
        return $query->where('is_academic_based', true);
    }

    public function scopeByAmountRange($query, $minAmount, $maxAmount)
    {
        return $query->whereBetween('amount', [$minAmount, $maxAmount]);
    }

    public function scopeByPercentageRange($query, $minPercentage, $maxPercentage)
    {
        return $query->whereBetween('percentage', [$minPercentage, $maxPercentage]);
    }

    public function scopeByDeadlineRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('application_deadline', [$startDate, $endDate]);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    public function scopeAvailable($query)
    {
        return $query->where('current_recipients', '<', 'max_recipients');
    }

    public function scopeFull($query)
    {
        return $query->where('current_recipients', '>=', 'max_recipients');
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

    public function scopeDeadlinePassed($query)
    {
        return $query->where('application_deadline', '<', now());
    }

    public function scopeDeadlineApproaching($query, $days = 7)
    {
        return $query->whereBetween('application_deadline', [
            now(),
            now()->addDays($days)
        ]);
    }

    public function scopeByCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeByAmountOrder($query)
    {
        return $query->orderBy('amount', 'desc');
    }

    public function scopeByPercentageOrder($query)
    {
        return $query->orderBy('percentage', 'desc');
    }

    public function scopeByDeadlineOrder($query)
    {
        return $query->orderBy('application_deadline', 'asc');
    }

    public function scopeByStartDateOrder($query)
    {
        return $query->orderBy('start_date', 'desc');
    }

    public function scopeByEndDateOrder($query)
    {
        return $query->orderBy('end_date', 'asc');
    }

    public function scopeByRecipientsOrder($query)
    {
        return $query->orderBy('current_recipients', 'desc');
    }

    public function getTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->type));
    }

    public function getAmountDisplayAttribute(): string
    {
        if (!$this->amount) return 'N/A';
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    public function getPercentageDisplayAttribute(): string
    {
        if (!$this->percentage) return 'N/A';
        return $this->percentage . '%';
    }

    public function getMaxAmountDisplayAttribute(): string
    {
        if (!$this->max_amount) return 'No limit';
        return $this->currency . ' ' . number_format($this->max_amount, 2);
    }

    public function getMinAmountDisplayAttribute(): string
    {
        if (!$this->min_amount) return 'No minimum';
        return $this->currency . ' ' . number_format($this->min_amount, 2);
    }

    public function getApplicationDeadlineDisplayAttribute(): string
    {
        if (!$this->application_deadline) return 'No deadline';
        return $this->application_deadline->format('M d, Y');
    }

    public function getStartDateDisplayAttribute(): string
    {
        if (!$this->start_date) return 'Not specified';
        return $this->start_date->format('M d, Y');
    }

    public function getEndDateDisplayAttribute(): string
    {
        if (!$this->end_date) return 'No end date';
        return $this->end_date->format('M d, Y');
    }

    public function getEligibilityCriteriaDisplayAttribute(): string
    {
        return $this->eligibility_criteria ?: 'No specific criteria';
    }

    public function getRequirementsDisplayAttribute(): string
    {
        return $this->requirements ?: 'No specific requirements';
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

    public function getIsMeritBasedAttribute(): bool
    {
        return $this->is_merit_based;
    }

    public function getIsNeedBasedAttribute(): bool
    {
        return $this->is_need_based;
    }

    public function getIsSportsBasedAttribute(): bool
    {
        return $this->is_sports_based;
    }

    public function getIsArtsBasedAttribute(): bool
    {
        return $this->is_arts_based;
    }

    public function getIsAcademicBasedAttribute(): bool
    {
        return $this->is_academic_based;
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->current_recipients < $this->max_recipients;
    }

    public function getIsFullAttribute(): bool
    {
        return $this->current_recipients >= $this->max_recipients;
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

    public function getIsDeadlinePassedAttribute(): bool
    {
        return $this->application_deadline && $this->application_deadline < now();
    }

    public function getIsDeadlineApproachingAttribute(): bool
    {
        if (!$this->application_deadline || $this->is_deadline_passed) return false;
        
        $approachingDate = now()->addDays(7);
        return $this->application_deadline <= $approachingDate;
    }

    public function getAvailableSlotsAttribute(): int
    {
        return max(0, $this->max_recipients - $this->current_recipients);
    }

    public function getOccupancyPercentageAttribute(): float
    {
        if ($this->max_recipients == 0) return 0;
        return round(($this->current_recipients / $this->max_recipients) * 100, 2);
    }

    public function getOccupancyColorAttribute(): string
    {
        $percentage = $this->occupancy_percentage;
        
        if ($percentage >= 90) return 'danger';
        if ($percentage >= 75) return 'warning';
        if ($percentage >= 50) return 'info';
        return 'success';
    }

    public function getDaysUntilDeadlineAttribute(): int
    {
        if (!$this->application_deadline || $this->is_deadline_passed) return 0;
        return now()->diffInDays($this->application_deadline, false);
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

    public function getScholarshipSummaryAttribute(): string
    {
        $summary = $this->name . ' (' . $this->code . ')';
        
        if ($this->type) {
            $summary .= ' - ' . $this->type_display;
        }
        
        if ($this->amount) {
            $summary .= ' - ' . $this->amount_display;
        } elseif ($this->percentage) {
            $summary .= ' - ' . $this->percentage_display;
        }
        
        if ($this->is_full) {
            $summary .= ' - Full';
        } else {
            $summary .= ' - ' . $this->available_slots . ' slots available';
        }
        
        return $summary;
    }

    public function getFinancialSummaryAttribute(): string
    {
        $summary = [];
        
        if ($this->amount) {
            $summary[] = 'Amount: ' . $this->amount_display;
        }
        
        if ($this->percentage) {
            $summary[] = 'Percentage: ' . $this->percentage_display;
        }
        
        if ($this->min_amount) {
            $summary[] = 'Min: ' . $this->min_amount_display;
        }
        
        if ($this->max_amount) {
            $summary[] = 'Max: ' . $this->max_amount_display;
        }
        
        if ($this->currency) {
            $summary[] = 'Currency: ' . $this->currency;
        }
        
        return empty($summary) ? 'No financial details' : implode(' | ', $summary);
    }

    public function getTimelineSummaryAttribute(): string
    {
        $summary = [];
        
        if ($this->application_deadline) {
            $summary[] = 'Deadline: ' . $this->application_deadline_display;
        }
        
        if ($this->start_date) {
            $summary[] = 'Start: ' . $this->start_date_display;
        }
        
        if ($this->end_date) {
            $summary[] = 'End: ' . $this->end_date_display;
        }
        
        return empty($summary) ? 'No timeline information' : implode(' | ', $summary);
    }

    public function getCategorySummaryAttribute(): string
    {
        $categories = [];
        
        if ($this->is_merit_based) $categories[] = 'Merit-based';
        if ($this->is_need_based) $categories[] = 'Need-based';
        if ($this->is_sports_based) $categories[] = 'Sports-based';
        if ($this->is_arts_based) $categories[] = 'Arts-based';
        if ($this->is_academic_based) $categories[] = 'Academic-based';
        
        return empty($categories) ? 'General' : implode(', ', $categories);
    }

    public function canBeEdited(): bool
    {
        return $this->is_active && $this->current_recipients === 0;
    }

    public function canBeDeleted(): bool
    {
        return $this->current_recipients === 0;
    }

    public function canBeActivated(): bool
    {
        return !$this->is_active;
    }

    public function canBeDeactivated(): bool
    {
        return $this->is_active && $this->current_recipients === 0;
    }

    public function canAcceptApplications(): bool
    {
        return $this->is_active && !$this->is_full && !$this->is_deadline_passed;
    }

    public function canAddRecipient(): bool
    {
        return $this->is_active && !$this->is_full;
    }

    public function canRemoveRecipient(): bool
    {
        return $this->current_recipients > 0;
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

    public function addRecipient(): bool
    {
        if (!$this->can_add_recipient) {
            return false;
        }
        
        $this->increment('current_recipients');
        return true;
    }

    public function removeRecipient(): bool
    {
        if (!$this->can_remove_recipient) {
            return false;
        }
        
        $this->decrement('current_recipients');
        return true;
    }

    public function updateAmount(float $newAmount): void
    {
        if ($newAmount >= 0) {
            $this->amount = $newAmount;
            $this->save();
        }
    }

    public function updatePercentage(float $newPercentage): void
    {
        if ($newPercentage >= 0 && $newPercentage <= 100) {
            $this->percentage = $newPercentage;
            $this->save();
        }
    }

    public function updateMaxRecipients(int $newMax): void
    {
        if ($newMax >= $this->current_recipients) {
            $this->max_recipients = $newMax;
            $this->save();
        }
    }

    public function extendDeadline(string $newDeadline): void
    {
        $this->application_deadline = $newDeadline;
        $this->save();
    }

    public function extendEndDate(string $newEndDate): void
    {
        $this->end_date = $newEndDate;
        $this->save();
    }

    public function addNote(string $note): void
    {
        $this->notes = ($this->notes ? $this->notes . "\n" : '') . $note;
        $this->save();
    }

    public function getScholarshipStatistics(): array
    {
        return [
            'type' => $this->type,
            'amount' => $this->amount,
            'percentage' => $this->percentage,
            'currency' => $this->currency,
            'max_amount' => $this->max_amount,
            'min_amount' => $this->min_amount,
            'academic_year' => $this->academic_year,
            'class_id' => $this->class_id,
            'subject_id' => $this->subject_id,
            'application_deadline' => $this->application_deadline,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'max_recipients' => $this->max_recipients,
            'current_recipients' => $this->current_recipients,
            'is_active' => $this->is_active,
            'is_merit_based' => $this->is_merit_based,
            'is_need_based' => $this->is_need_based,
            'is_sports_based' => $this->is_sports_based,
            'is_arts_based' => $this->is_arts_based,
            'is_academic_based' => $this->is_academic_based,
            'is_available' => $this->is_available,
            'is_full' => $this->is_full,
            'is_expired' => $this->is_expired,
            'is_expiring_soon' => $this->is_expiring_soon,
            'is_deadline_passed' => $this->is_deadline_passed,
            'is_deadline_approaching' => $this->is_deadline_approaching,
            'available_slots' => $this->available_slots,
            'occupancy_percentage' => $this->occupancy_percentage,
            'occupancy_color' => $this->occupancy_color,
            'days_until_deadline' => $this->days_until_deadline,
            'days_until_start' => $this->days_until_start,
            'days_until_end' => $this->days_until_end,
            'days_since_creation' => $this->days_since_creation,
            'days_since_last_update' => $this->days_since_last_update,
            'can_be_edited' => $this->can_be_edited,
            'can_be_deleted' => $this->can_be_deleted,
            'can_be_activated' => $this->can_be_activated,
            'can_be_deactivated' => $this->can_be_deactivated,
            'can_accept_applications' => $this->can_accept_applications,
            'can_add_recipient' => $this->can_add_recipient,
            'can_remove_recipient' => $this->can_remove_recipient
        ];
    }
}
