<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class AdmissionQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message', 'status',
        'priority', 'source', 'assigned_to', 'response', 'response_date',
        'follow_up_date', 'is_active', 'created_by', 'notes'
    ];

    protected $casts = [
        'response_date' => 'datetime', 'follow_up_date' => 'datetime',
        'priority' => 'integer', 'is_active' => 'boolean'
    ];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeByAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 8);
    }

    public function scopeMediumPriority($query)
    {
        return $query->whereBetween('priority', [4, 7]);
    }

    public function scopeLowPriority($query)
    {
        return $query->where('priority', '<=', 3);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByFollowUpDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('follow_up_date', [$startDate, $endDate]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('follow_up_date', '<', now())
                    ->whereIn('status', ['pending', 'in_progress']);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('follow_up_date', today());
    }

    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('follow_up_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email', 'like', "%{$email}%");
    }

    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone', 'like', "%{$phone}%");
    }

    public function scopeBySubject($query, $subject)
    {
        return $query->where('subject', 'like', "%{$subject}%");
    }

    public function scopeByMessage($query, $message)
    {
        return $query->where('message', 'like', "%{$message}%");
    }

    public function scopeByResponse($query, $response)
    {
        return $query->where('response', 'like', "%{$response}%");
    }

    public function scopeByPriorityOrder($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('created_at', 'desc');
    }

    public function scopeByStatusOrder($query)
    {
        return $query->orderByRaw("FIELD(status, 'pending', 'in_progress', 'resolved', 'closed')")
                    ->orderBy('created_at', 'desc');
    }

    public function scopeByFollowUpDateOrder($query)
    {
        return $query->orderBy('follow_up_date', 'asc');
    }

    public function scopeByCreatedDateOrder($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByAssignedOrder($query)
    {
        return $query->orderBy('assigned_to')->orderBy('created_at', 'desc');
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'in_progress' => 'info',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'secondary'
        };
    }

    public function getPriorityDisplayAttribute(): string
    {
        return match($this->priority) {
            1 => 'Very Low',
            2 => 'Low',
            3 => 'Low-Medium',
            4 => 'Medium',
            5 => 'Medium-High',
            6 => 'High',
            7 => 'Very High',
            8 => 'Critical',
            9 => 'Urgent',
            10 => 'Emergency',
            default => 'Unknown'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            1, 2, 3 => 'success',
            4, 5 => 'info',
            6, 7 => 'warning',
            8, 9, 10 => 'danger',
            default => 'secondary'
        };
    }

    public function getSourceDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->source));
    }

    public function getResponseDateDisplayAttribute(): string
    {
        if (!$this->response_date) return 'Not responded yet';
        return $this->response_date->format('M d, Y \a\t H:i');
    }

    public function getFollowUpDateDisplayAttribute(): string
    {
        if (!$this->follow_up_date) return 'No follow-up scheduled';
        return $this->follow_up_date->format('M d, Y \a\t H:i');
    }

    public function getCreatedAtDisplayAttribute(): string
    {
        return $this->created_at->format('M d, Y \a\t H:i');
    }

    public function getUpdatedAtDisplayAttribute(): string
    {
        return $this->updated_at->format('M d, Y \a\t H:i');
    }

    public function getResponseDisplayAttribute(): string
    {
        return $this->response ?: 'No response yet';
    }

    public function getNotesDisplayAttribute(): string
    {
        return $this->notes ?: 'No additional notes';
    }

    public function getAssignedToDisplayAttribute(): string
    {
        if (!$this->assigned_to) return 'Unassigned';
        return $this->assignedTo->name ?? 'Unknown';
    }

    public function getCreatedByDisplayAttribute(): string
    {
        if (!$this->created_by) return 'System';
        return $this->createdBy->name ?? 'Unknown';
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->is_active;
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsInProgressAttribute(): bool
    {
        return $this->status === 'in_progress';
    }

    public function getIsResolvedAttribute(): bool
    {
        return $this->status === 'resolved';
    }

    public function getIsClosedAttribute(): bool
    {
        return $this->status === 'closed';
    }

    public function getIsHighPriorityAttribute(): bool
    {
        return $this->priority >= 8;
    }

    public function getIsMediumPriorityAttribute(): bool
    {
        return $this->priority >= 4 && $this->priority <= 7;
    }

    public function getIsLowPriorityAttribute(): bool
    {
        return $this->priority <= 3;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->follow_up_date && $this->follow_up_date < now() && 
               in_array($this->status, ['pending', 'in_progress']);
    }

    public function getIsDueTodayAttribute(): bool
    {
        return $this->follow_up_date && $this->follow_up_date->isToday();
    }

    public function getIsDueThisWeekAttribute(): bool
    {
        if (!$this->follow_up_date) return false;
        
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        return $this->follow_up_date->between($startOfWeek, $endOfWeek);
    }

    public function getDaysUntilFollowUpAttribute(): int
    {
        if (!$this->follow_up_date || $this->is_overdue) return 0;
        return now()->diffInDays($this->follow_up_date, false);
    }

    public function getDaysSinceCreationAttribute(): int
    {
        return now()->diffInDays($this->created_at);
    }

    public function getDaysSinceResponseAttribute(): int
    {
        if (!$this->response_date) return 0;
        return now()->diffInDays($this->response_date);
    }

    public function getDaysSinceLastUpdateAttribute(): int
    {
        return now()->diffInDays($this->updated_at);
    }

    public function getQuerySummaryAttribute(): string
    {
        $summary = $this->name . ' - ' . $this->subject;
        
        if ($this->status) {
            $summary .= ' (' . $this->status_display . ')';
        }
        
        if ($this->priority) {
            $summary .= ' - Priority: ' . $this->priority_display;
        }
        
        if ($this->assigned_to) {
            $summary .= ' - Assigned to: ' . $this->assigned_to_display;
        }
        
        return $summary;
    }

    public function getContactSummaryAttribute(): string
    {
        $summary = [];
        
        if ($this->email) {
            $summary[] = 'Email: ' . $this->email;
        }
        
        if ($this->phone) {
            $summary[] = 'Phone: ' . $this->phone;
        }
        
        if ($this->source) {
            $summary[] = 'Source: ' . $this->source_display;
        }
        
        return empty($summary) ? 'No contact information' : implode(' | ', $summary);
    }

    public function getTimelineSummaryAttribute(): string
    {
        $summary = [];
        
        $summary[] = 'Created: ' . $this->created_at_display;
        
        if ($this->response_date) {
            $summary[] = 'Responded: ' . $this->response_date_display;
        }
        
        if ($this->follow_up_date) {
            $summary[] = 'Follow-up: ' . $this->follow_up_date_display;
        }
        
        if ($this->updated_at && $this->updated_at != $this->created_at) {
            $summary[] = 'Updated: ' . $this->updated_at_display;
        }
        
        return implode(' | ', $summary);
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['pending', 'in_progress']);
    }

    public function canBeAssigned(): bool
    {
        return !$this->assigned_to;
    }

    public function canBeReassigned(): bool
    {
        return $this->assigned_to && in_array($this->status, ['pending', 'in_progress']);
    }

    public function canBeResponded(): bool
    {
        return in_array($this->status, ['pending', 'in_progress']);
    }

    public function canBeResolved(): bool
    {
        return in_array($this->status, ['pending', 'in_progress']);
    }

    public function canBeClosed(): bool
    {
        return in_array($this->status, ['resolved']);
    }

    public function canBeReopened(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function canBeScheduledFollowUp(): bool
    {
        return in_array($this->status, ['pending', 'in_progress']);
    }

    public function canBeDeleted(): bool
    {
        return in_array($this->status, ['closed']);
    }

    public function assignTo(User $user): void
    {
        if ($this->can_be_assigned || $this->can_be_reassigned) {
            $this->assigned_to = $user->id;
            $this->save();
        }
    }

    public function unassign(): void
    {
        if ($this->can_be_assigned || $this->can_be_reassigned) {
            $this->assigned_to = null;
            $this->save();
        }
    }

    public function respond(string $response): void
    {
        if ($this->can_be_responded) {
            $this->response = $response;
            $this->response_date = now();
            $this->status = 'in_progress';
            $this->save();
        }
    }

    public function resolve(string $notes = null): void
    {
        if ($this->can_be_resolved) {
            $this->status = 'resolved';
            
            if ($notes) {
                $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Resolved: ' . $notes;
            }
            
            $this->save();
        }
    }

    public function close(string $notes = null): void
    {
        if ($this->can_be_closed) {
            $this->status = 'closed';
            
            if ($notes) {
                $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Closed: ' . $notes;
            }
            
            $this->save();
        }
    }

    public function reopen(string $notes = null): void
    {
        if ($this->can_be_reopened) {
            $this->status = 'in_progress';
            
            if ($notes) {
                $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Reopened: ' . $notes;
            }
            
            $this->save();
        }
    }

    public function scheduleFollowUp(string $followUpDate, string $notes = null): void
    {
        if ($this->can_be_scheduled_follow_up) {
            $this->follow_up_date = $followUpDate;
            
            if ($notes) {
                $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Follow-up scheduled: ' . $notes;
            }
            
            $this->save();
        }
    }

    public function updatePriority(int $newPriority): void
    {
        if ($newPriority >= 1 && $newPriority <= 10) {
            $this->priority = $newPriority;
            $this->save();
        }
    }

    public function addNote(string $note): void
    {
        $this->notes = ($this->notes ? $this->notes . "\n" : '') . $note;
        $this->save();
    }

    public function getAdmissionQueryStatistics(): array
    {
        return [
            'status' => $this->status,
            'priority' => $this->priority,
            'source' => $this->source,
            'assigned_to' => $this->assigned_to,
            'response_date' => $this->response_date,
            'follow_up_date' => $this->follow_up_date,
            'is_active' => $this->is_active,
            'is_pending' => $this->is_pending,
            'is_in_progress' => $this->is_in_progress,
            'is_resolved' => $this->is_resolved,
            'is_closed' => $this->is_closed,
            'is_high_priority' => $this->is_high_priority,
            'is_medium_priority' => $this->is_medium_priority,
            'is_low_priority' => $this->is_low_priority,
            'is_overdue' => $this->is_overdue,
            'is_due_today' => $this->is_due_today,
            'is_due_this_week' => $this->is_due_this_week,
            'days_until_follow_up' => $this->days_until_follow_up,
            'days_since_creation' => $this->days_since_creation,
            'days_since_response' => $this->days_since_response,
            'days_since_last_update' => $this->days_since_last_update,
            'can_be_edited' => $this->can_be_edited,
            'can_be_assigned' => $this->can_be_assigned,
            'can_be_reassigned' => $this->can_be_reassigned,
            'can_be_responded' => $this->can_be_responded,
            'can_be_resolved' => $this->can_be_resolved,
            'can_be_closed' => $this->can_be_closed,
            'can_be_reopened' => $this->can_be_reopened,
            'can_be_scheduled_follow_up' => $this->can_be_scheduled_follow_up,
            'can_be_deleted' => $this->can_be_deleted
        ];
    }
}
