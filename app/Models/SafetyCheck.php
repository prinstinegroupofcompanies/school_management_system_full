<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SafetyCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_number', 'checked_by', 'check_type', 'area_checked', 'check_description',
        'status', 'findings', 'recommendations', 'corrective_actions', 'check_date',
        'next_check_date', 'checklist_items', 'photos', 'requires_follow_up',
        'follow_up_date', 'follow_up_notes', 'approved_by', 'approved_at',
        'approval_notes', 'notes', 'metadata'
    ];

    protected $casts = [
        'check_date' => 'date',
        'next_check_date' => 'date',
        'follow_up_date' => 'date',
        'checklist_items' => 'array',
        'photos' => 'array',
        'requires_follow_up' => 'boolean',
        'approved_at' => 'datetime',
        'metadata' => 'array'
    ];

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('check_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByArea($query, $area)
    {
        return $query->where('area_checked', $area);
    }

    public function scopePassed($query)
    {
        return $query->where('status', 'passed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeNeedsAttention($query)
    {
        return $query->where('status', 'needs_attention');
    }

    public function scopeCritical($query)
    {
        return $query->where('status', 'critical');
    }

    public function scopeRequiresFollowUp($query)
    {
        return $query->where('requires_follow_up', true);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('check_date', [$startDate, $endDate]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_check_date', '<', now())
            ->where('status', '!=', 'critical');
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('next_check_date', '<=', now()->addDays($days))
            ->where('next_check_date', '>=', now());
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'passed' => 'success',
            'failed' => 'danger',
            'needs_attention' => 'warning',
            'critical' => 'dark',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'passed' => 'Passed',
            'failed' => 'Failed',
            'needs_attention' => 'Needs Attention',
            'critical' => 'Critical',
            default => ucfirst($this->status)
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->check_type) {
            'fire_safety' => 'danger',
            'electrical' => 'warning',
            'structural' => 'info',
            'playground' => 'success',
            'equipment' => 'primary',
            default => 'secondary'
        };
    }

    public function getTypeTextAttribute(): string
    {
        return match($this->check_type) {
            'fire_safety' => 'Fire Safety',
            'electrical' => 'Electrical',
            'structural' => 'Structural',
            'playground' => 'Playground',
            'equipment' => 'Equipment',
            default => ucfirst(str_replace('_', ' ', $this->check_type))
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->next_check_date && $this->next_check_date < now();
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->next_check_date && $this->next_check_date <= now()->addDays(7);
    }

    public function getIsApprovedAttribute(): bool
    {
        return !is_null($this->approved_at);
    }

    public function getFormattedCheckDateAttribute(): string
    {
        return $this->check_date->format('M d, Y');
    }

    public function getFormattedNextCheckDateAttribute(): string
    {
        return $this->next_check_date ? $this->next_check_date->format('M d, Y') : 'N/A';
    }

    public function getFormattedFollowUpDateAttribute(): string
    {
        return $this->follow_up_date ? $this->follow_up_date->format('M d, Y') : 'N/A';
    }

    public function getFormattedApprovedAtAttribute(): string
    {
        return $this->approved_at ? $this->approved_at->format('M d, Y H:i') : 'N/A';
    }

    // Methods
    public function generateCheckNumber(): string
    {
        $prefix = 'SC';
        $count = static::count() + 1;
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    public function approve($approvedBy, $notes = null): void
    {
        $this->update([
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'approval_notes' => $notes
        ]);
    }

    public function markAsPassed(): void
    {
        $this->update(['status' => 'passed']);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function markAsNeedsAttention(): void
    {
        $this->update(['status' => 'needs_attention']);
    }

    public function markAsCritical(): void
    {
        $this->update(['status' => 'critical']);
    }

    public function scheduleFollowUp($followUpDate, $notes = null): void
    {
        $this->update([
            'requires_follow_up' => true,
            'follow_up_date' => $followUpDate,
            'follow_up_notes' => $notes
        ]);
    }

    public function completeFollowUp($notes = null): void
    {
        $this->update([
            'requires_follow_up' => false,
            'follow_up_notes' => $notes
        ]);
    }

    public function canBeEdited(): bool
    {
        return !$this->is_approved;
    }

    public function canBeApproved(): bool
    {
        return !$this->is_approved && in_array($this->status, ['passed', 'failed', 'needs_attention', 'critical']);
    }

    public function canBeDeleted(): bool
    {
        return !$this->is_approved;
    }

    public function getChecklistItemsFormatted(): array
    {
        if (!$this->checklist_items) {
            return [];
        }

        $formatted = [];
        foreach ($this->checklist_items as $item) {
            $formatted[] = [
                'item' => $item['item'] ?? '',
                'status' => $item['status'] ?? 'pending',
                'notes' => $item['notes'] ?? ''
            ];
        }

        return $formatted;
    }
}
