<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'type', 'category', 'start_date', 'end_date',
        'start_time', 'end_time', 'venue', 'instructions', 'important_notes',
        'status', 'rejection_reason', 'is_recurring', 'recurrence_type',
        'recurrence_interval', 'recurrence_end_date', 'recurrence_days',
        'requires_approval', 'is_public', 'is_active', 'attachments',
        'metadata', 'created_by', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'recurrence_end_date' => 'date',
        'recurrence_days' => 'array',
        'is_recurring' => 'boolean',
        'requires_approval' => 'boolean',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'attachments' => 'array',
        'metadata' => 'array',
        'approved_at' => 'datetime'
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ScheduleApproval::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    public function scopeUpcoming($query, $days = 30)
    {
        return $query->where('start_date', '>=', now())
                    ->where('start_date', '<=', now()->addDays($days));
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function getFirstLevelApprovalAttribute()
    {
        return $this->approvals()->where('approval_level', 'first_level')->first();
    }

    public function getSecondLevelApprovalAttribute()
    {
        return $this->approvals()->where('approval_level', 'second_level')->first();
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'orange',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'exam' => 'red',
            'event' => 'blue',
            'class' => 'green',
            'meeting' => 'purple',
            'holiday' => 'orange',
            'other' => 'gray',
            default => 'gray'
        };
    }

    public function getDurationAttribute()
    {
        if ($this->start_time && $this->end_time) {
            $start = \Carbon\Carbon::createFromFormat('H:i:s', $this->start_time);
            $end = \Carbon\Carbon::createFromFormat('H:i:s', $this->end_time);
            return $start->diffInMinutes($end);
        }
        return null;
    }

    public function getDurationHoursAttribute()
    {
        $duration = $this->duration;
        return $duration ? round($duration / 60, 1) : null;
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function canBeSubmitted()
    {
        return $this->status === 'draft';
    }

    public function canBeApproved()
    {
        return in_array($this->status, ['submitted']);
    }

    public function canBeRejected()
    {
        return in_array($this->status, ['submitted']);
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['approved']);
    }

    public function submit()
    {
        if ($this->canBeSubmitted()) {
            $this->status = 'submitted';
            $this->save();
            
            if ($this->requires_approval) {
                // Create first level approval record
                $this->approvals()->create([
                    'approver_id' => $this->getDepartmentHeadId(),
                    'approval_level' => 'first_level',
                    'status' => 'pending'
                ]);
            } else {
                $this->status = 'approved';
                $this->approved_by = $this->created_by;
                $this->approved_at = now();
                $this->save();
            }
        }
    }

    public function approve($approverId, $level, $comments = null, $eSignature = null)
    {
        if ($this->canBeApproved()) {
            $approval = $this->approvals()->where('approval_level', $level)->first();
            
            if ($approval) {
                $approval->update([
                    'approver_id' => $approverId,
                    'status' => 'approved',
                    'comments' => $comments,
                    'e_signature' => $eSignature,
                    'approved_at' => now()
                ]);

                if ($level === 'first_level') {
                    // Create second level approval record
                    $this->approvals()->create([
                        'approver_id' => $this->getAdminId(),
                        'approval_level' => 'second_level',
                        'status' => 'pending'
                    ]);
                } else {
                    $this->status = 'approved';
                    $this->approved_by = $approverId;
                    $this->approved_at = now();
                    $this->save();
                }
            }
        }
    }

    public function reject($approverId, $level, $reason, $eSignature = null)
    {
        if ($this->canBeRejected()) {
            $approval = $this->approvals()->where('approval_level', $level)->first();
            
            if ($approval) {
                $approval->update([
                    'approver_id' => $approverId,
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                    'e_signature' => $eSignature,
                    'rejected_at' => now()
                ]);

                $this->status = 'rejected';
                $this->rejection_reason = $reason;
                $this->save();
            }
        }
    }

    public function cancel($reason = null)
    {
        if ($this->canBeCancelled()) {
            $this->status = 'cancelled';
            if ($reason) {
                $this->rejection_reason = $reason;
            }
            $this->save();
        }
    }

    private function getDepartmentHeadId()
    {
        // Logic to get department head ID
        return User::where('role', 'teacher')
            ->where('is_department_head', true)
            ->first()?->id ?? 1; // Fallback to admin
    }

    private function getAdminId()
    {
        return User::where('role', 'admin')->first()?->id ?? 1;
    }

    public function generateRecurringInstances()
    {
        if (!$this->is_recurring || !$this->recurrence_type) {
            return collect();
        }

        $instances = collect();
        $currentDate = $this->start_date->copy();
        $endDate = $this->recurrence_end_date ?? now()->addYear();

        while ($currentDate->lte($endDate)) {
            if ($currentDate->gt($this->start_date)) {
                $instance = $this->replicate();
                $instance->start_date = $currentDate->copy();
                if ($this->end_date) {
                    $instance->end_date = $currentDate->copy()->addDays($this->start_date->diffInDays($this->end_date));
                }
                $instance->status = 'draft';
                $instance->created_by = $this->created_by;
                $instance->save();
                $instances->push($instance);
            }

            switch ($this->recurrence_type) {
                case 'daily':
                    $currentDate->addDays($this->recurrence_interval ?? 1);
                    break;
                case 'weekly':
                    $currentDate->addWeeks($this->recurrence_interval ?? 1);
                    break;
                case 'monthly':
                    $currentDate->addMonths($this->recurrence_interval ?? 1);
                    break;
                case 'yearly':
                    $currentDate->addYears($this->recurrence_interval ?? 1);
                    break;
            }
        }

        return $instances;
    }
}
