<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class StudentTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'title', 'description', 'type', 'category', 'date',
        'time', 'location', 'status', 'priority', 'is_important',
        'is_public', 'attachments', 'metadata', 'created_by', 'notes'
    ];

    protected $casts = [
        'date' => 'date', 'time' => 'datetime', 'priority' => 'integer',
        'is_important' => 'boolean', 'is_public' => 'boolean',
        'attachments' => 'array', 'metadata' => 'array'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByTimeRange($query, $startTime, $endTime)
    {
        return $query->whereBetween('time', [$startTime, $endTime]);
    }

    public function scopeUpcoming($query, $days = 30)
    {
        return $query->where('date', '>=', now())
                    ->where('date', '<=', now()->addDays($days));
    }

    public function scopePast($query, $days = 30)
    {
        return $query->where('date', '<=', now())
                    ->where('date', '>=', now()->subDays($days));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    public function scopeThisYear($query)
    {
        return $query->whereBetween('date', [
            now()->startOfYear(),
            now()->endOfYear()
        ]);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    public function scopeByTitle($query, $title)
    {
        return $query->where('title', 'like', "%{$title}%");
    }

    public function scopeByDescription($query, $description)
    {
        return $query->where('description', 'like', "%{$description}%");
    }

    public function scopeByPriorityOrder($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('date', 'desc');
    }

    public function scopeByDateOrder($query)
    {
        return $query->orderBy('date', 'desc');
    }

    public function scopeByTimeOrder($query)
    {
        return $query->orderBy('time', 'desc');
    }

    public function scopeByTypeOrder($query)
    {
        return $query->orderBy('type')->orderBy('date', 'desc');
    }

    public function scopeByCategoryOrder($query)
    {
        return $query->orderBy('category')->orderBy('date', 'desc');
    }

    public function scopeByStatusOrder($query)
    {
        return $query->orderByRaw("FIELD(status, 'active', 'completed', 'cancelled', 'postponed')")
                    ->orderBy('date', 'desc');
    }

    public function scopeByImportanceOrder($query)
    {
        return $query->orderBy('is_important', 'desc')->orderBy('date', 'desc');
    }

    public function scopeChronological($query)
    {
        return $query->orderBy('date', 'asc')->orderBy('time', 'asc');
    }

    public function scopeReverseChronological($query)
    {
        return $query->orderBy('date', 'desc')->orderBy('time', 'desc');
    }

    public function getTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->type));
    }

    public function getCategoryDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->category));
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'completed' => 'primary',
            'cancelled' => 'danger',
            'postponed' => 'warning',
            'pending' => 'info',
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

    public function getDateDisplayAttribute(): string
    {
        return $this->date->format('M d, Y');
    }

    public function getTimeDisplayAttribute(): string
    {
        if (!$this->time) return 'No specific time';
        return $this->time->format('H:i');
    }

    public function getDateTimeDisplayAttribute(): string
    {
        $display = $this->date_display;
        
        if ($this->time) {
            $display .= ' at ' . $this->time_display;
        }
        
        return $display;
    }

    public function getLocationDisplayAttribute(): string
    {
        return $this->location ?: 'No location specified';
    }

    public function getAttachmentsDisplayAttribute(): string
    {
        if (!$this->attachments || empty($this->attachments)) {
            return 'No attachments';
        }
        
        $display = [];
        foreach ($this->attachments as $attachment) {
            $display[] = $attachment['name'] ?? 'Unknown file';
        }
        
        return implode(', ', $display);
    }

    public function getMetadataDisplayAttribute(): string
    {
        if (!$this->metadata || empty($this->metadata)) {
            return 'No additional data';
        }
        
        $display = [];
        foreach ($this->metadata as $key => $value) {
            $display[] = ucwords(str_replace('_', ' ', $key)) . ': ' . $value;
        }
        
        return implode(', ', $display);
    }

    public function getNotesDisplayAttribute(): string
    {
        return $this->notes ?: 'No additional notes';
    }

    public function getCreatedByDisplayAttribute(): string
    {
        if (!$this->created_by) return 'System';
        return $this->createdBy->name ?? 'Unknown';
    }

    public function getIsImportantAttribute(): bool
    {
        return $this->is_important;
    }

    public function getIsPublicAttribute(): bool
    {
        return $this->is_public;
    }

    public function getIsPrivateAttribute(): bool
    {
        return !$this->is_public;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getIsPostponedAttribute(): bool
    {
        return $this->status === 'postponed';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->date > now();
    }

    public function getIsPastAttribute(): bool
    {
        return $this->date < now();
    }

    public function getIsTodayAttribute(): bool
    {
        return $this->date->isToday();
    }

    public function getIsThisWeekAttribute(): bool
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        return $this->date->between($startOfWeek, $endOfWeek);
    }

    public function getIsThisMonthAttribute(): bool
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        return $this->date->between($startOfMonth, $endOfMonth);
    }

    public function getIsThisYearAttribute(): bool
    {
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();
        
        return $this->date->between($startOfYear, $endOfYear);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->date < now() && $this->status === 'active';
    }

    public function getIsDueSoonAttribute(): bool
    {
        if (!$this->is_upcoming) return false;
        
        $dueDate = now()->addDays(7);
        return $this->date <= $dueDate;
    }

    public function getDaysUntilEventAttribute(): int
    {
        if ($this->is_past) return 0;
        return now()->diffInDays($this->date, false);
    }

    public function getDaysSinceEventAttribute(): int
    {
        if ($this->is_upcoming) return 0;
        return now()->diffInDays($this->date);
    }

    public function getDaysSinceCreationAttribute(): int
    {
        return now()->diffInDays($this->created_at);
    }

    public function getDaysSinceLastUpdateAttribute(): int
    {
        return now()->diffInDays($this->updated_at);
    }

    public function getTimeAgoAttribute(): string
    {
        $diff = now()->diff($this->date);
        
        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        } elseif ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        } elseif ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'Just now';
        }
    }

    public function getTimeUntilAttribute(): string
    {
        if ($this->is_past) return 'Already happened';
        
        $diff = now()->diff($this->date);
        
        if ($diff->y > 0) {
            return 'In ' . $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
        } elseif ($diff->m > 0) {
            return 'In ' . $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
        } elseif ($diff->d > 0) {
            return 'In ' . $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
        } elseif ($diff->h > 0) {
            return 'In ' . $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
        } elseif ($diff->i > 0) {
            return 'In ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        } else {
            return 'Very soon';
        }
    }

    public function getTimelineSummaryAttribute(): string
    {
        $summary = $this->title;
        
        if ($this->type) {
            $summary .= ' (' . $this->type_display . ')';
        }
        
        if ($this->category) {
            $summary .= ' - ' . $this->category_display;
        }
        
        $summary .= ' - ' . $this->date_time_display;
        
        if ($this->location) {
            $summary .= ' at ' . $this->location;
        }
        
        $summary .= ' - ' . $this->status_display;
        
        if ($this->is_important) {
            $summary .= ' - IMPORTANT';
        }
        
        return $summary;
    }

    public function getEventSummaryAttribute(): string
    {
        $summary = [];
        
        if ($this->description) {
            $summary[] = $this->description;
        }
        
        if ($this->location) {
            $summary[] = 'Location: ' . $this->location;
        }
        
        if ($this->priority) {
            $summary[] = 'Priority: ' . $this->priority_display;
        }
        
        if ($this->is_important) {
            $summary[] = 'Important Event';
        }
        
        if ($this->is_public) {
            $summary[] = 'Public Event';
        } else {
            $summary[] = 'Private Event';
        }
        
        return empty($summary) ? 'No additional details' : implode(' | ', $summary);
    }

    public function getTimelineStatisticsAttribute(): string
    {
        $stats = [];
        
        if ($this->is_upcoming) {
            $stats[] = 'Upcoming in ' . $this->days_until_event . ' days';
        } elseif ($this->is_past) {
            $stats[] = 'Happened ' . $this->days_since_event . ' days ago';
        }
        
        if ($this->is_overdue) {
            $stats[] = 'OVERDUE';
        }
        
        if ($this->is_due_soon) {
            $stats[] = 'Due Soon';
        }
        
        if ($this->is_today) {
            $stats[] = 'Today';
        }
        
        if ($this->is_this_week) {
            $stats[] = 'This Week';
        }
        
        if ($this->is_this_month) {
            $stats[] = 'This Month';
        }
        
        return empty($stats) ? 'No timeline statistics' : implode(' | ', $stats);
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['active', 'pending']);
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'active';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['active', 'pending']);
    }

    public function canBePostponed(): bool
    {
        return in_array($this->status, ['active', 'pending']);
    }

    public function canBeReactivated(): bool
    {
        return in_array($this->status, ['cancelled', 'postponed']);
    }

    public function canBeDeleted(): bool
    {
        return in_array($this->status, ['cancelled', 'completed']);
    }

    public function canBeMarkedAsImportant(): bool
    {
        return !$this->is_important;
    }

    public function canBeMarkedAsNormal(): bool
    {
        return $this->is_important;
    }

    public function canBeMadePublic(): bool
    {
        return !$this->is_public;
    }

    public function canBeMadePrivate(): bool
    {
        return $this->is_public;
    }

    public function complete(string $notes = null): void
    {
        if ($this->can_be_completed) {
            $this->status = 'completed';
            
            if ($notes) {
                $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Completed: ' . $notes;
            }
            
            $this->save();
        }
    }

    public function cancel(string $reason = null): void
    {
        if ($this->can_be_cancelled) {
            $this->status = 'cancelled';
            
            if ($reason) {
                $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Cancelled: ' . $reason;
            }
            
            $this->save();
        }
    }

    public function postpone(string $newDate, string $reason = null): void
    {
        if ($this->can_be_postponed) {
            $this->status = 'postponed';
            $this->date = $newDate;
            
            if ($reason) {
                $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Postponed: ' . $reason;
            }
            
            $this->save();
        }
    }

    public function reactivate(string $notes = null): void
    {
        if ($this->can_be_reactivated) {
            $this->status = 'active';
            
            if ($notes) {
                $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Reactivated: ' . $notes;
            }
            
            $this->save();
        }
    }

    public function markAsImportant(): void
    {
        if ($this->can_be_marked_as_important) {
            $this->is_important = true;
            $this->save();
        }
    }

    public function markAsNormal(): void
    {
        if ($this->can_be_marked_as_normal) {
            $this->is_important = false;
            $this->save();
        }
    }

    public function makePublic(): void
    {
        if ($this->can_be_made_public) {
            $this->is_public = true;
            $this->save();
        }
    }

    public function makePrivate(): void
    {
        if ($this->can_be_made_private) {
            $this->is_public = false;
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

    public function updateDate(string $newDate): void
    {
        $this->date = $newDate;
        $this->save();
    }

    public function updateTime(string $newTime): void
    {
        $this->time = $newTime;
        $this->save();
    }

    public function addAttachment(string $name, string $path, string $type = null): void
    {
        $attachments = $this->attachments ?? [];
        $attachments[] = [
            'name' => $name,
            'path' => $path,
            'type' => $type,
            'added_at' => now()->toISOString()
        ];
        
        $this->attachments = $attachments;
        $this->save();
    }

    public function removeAttachment(int $index): void
    {
        $attachments = $this->attachments ?? [];
        
        if (isset($attachments[$index])) {
            unset($attachments[$index]);
            $this->attachments = array_values($attachments);
            $this->save();
        }
    }

    public function addMetadata(string $key, $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
        $this->save();
    }

    public function removeMetadata(string $key): void
    {
        $metadata = $this->metadata ?? [];
        unset($metadata[$key]);
        $this->metadata = $metadata;
        $this->save();
    }

    public function getMetadata(string $key, $default = null)
    {
        $metadata = $this->metadata ?? [];
        return $metadata[$key] ?? $default;
    }

    public function hasMetadata(string $key): bool
    {
        $metadata = $this->metadata ?? [];
        return isset($metadata[$key]);
    }

    public function addNote(string $note): void
    {
        $this->notes = ($this->notes ? $this->notes . "\n" : '') . $note;
        $this->save();
    }

    public function getStudentTimelineStatistics(): array
    {
        return [
            'type' => $this->type,
            'category' => $this->category,
            'status' => $this->status,
            'priority' => $this->priority,
            'date' => $this->date,
            'time' => $this->time,
            'location' => $this->location,
            'is_important' => $this->is_important,
            'is_public' => $this->is_public,
            'is_private' => $this->is_private,
            'is_active' => $this->is_active,
            'is_completed' => $this->is_completed,
            'is_cancelled' => $this->is_cancelled,
            'is_postponed' => $this->is_postponed,
            'is_pending' => $this->is_pending,
            'is_upcoming' => $this->is_upcoming,
            'is_past' => $this->is_past,
            'is_today' => $this->is_today,
            'is_this_week' => $this->is_this_week,
            'is_this_month' => $this->is_this_month,
            'is_this_year' => $this->is_this_year,
            'is_overdue' => $this->is_overdue,
            'is_due_soon' => $this->is_due_soon,
            'days_until_event' => $this->days_until_event,
            'days_since_event' => $this->days_since_event,
            'days_since_creation' => $this->days_since_creation,
            'days_since_last_update' => $this->days_since_last_update,
            'can_be_edited' => $this->can_be_edited,
            'can_be_completed' => $this->can_be_completed,
            'can_be_cancelled' => $this->can_be_cancelled,
            'can_be_postponed' => $this->can_be_postponed,
            'can_be_reactivated' => $this->can_be_reactivated,
            'can_be_deleted' => $this->can_be_deleted,
            'can_be_marked_as_important' => $this->can_be_marked_as_important,
            'can_be_marked_as_normal' => $this->can_be_marked_as_normal,
            'can_be_made_public' => $this->can_be_made_public,
            'can_be_made_private' => $this->can_be_made_private
        ];
    }
}
