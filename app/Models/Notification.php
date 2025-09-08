<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'message', 'type', 'category', 'subcategory',
        'priority', 'status', 'read_at', 'action_url', 'action_text',
        'related_model', 'related_id', 'metadata', 'scheduled_at',
        'sent_at', 'delivery_method', 'delivery_status', 'error_message',
        'retry_count', 'max_retries', 'expires_at', 'is_active'
    ];

    protected $casts = [
        'read_at' => 'datetime', 'scheduled_at' => 'datetime', 'sent_at' => 'datetime',
        'expires_at' => 'datetime', 'metadata' => 'array', 'priority' => 'integer',
        'retry_count' => 'integer', 'max_retries' => 'integer', 'is_active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySubcategory($query, $subcategory)
    {
        return $query->where('subcategory', $subcategory);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDeliveryMethod($query, $method)
    {
        return $query->where('delivery_method', $method);
    }

    public function scopeByDeliveryStatus($query, $status)
    {
        return $query->where('delivery_status', $status);
    }

    public function scopeByRelatedModel($query, $model)
    {
        return $query->where('related_model', $model);
    }

    public function scopeByRelatedId($query, $id)
    {
        return $query->where('related_id', $id);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByScheduledDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('scheduled_at', [$startDate, $endDate]);
    }

    public function scopeBySentDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sent_at', [$startDate, $endDate]);
    }

    public function scopeByExpiryDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expires_at', [$startDate, $endDate]);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeDelivered($query)
    {
        return $query->where('delivery_status', 'delivered');
    }

    public function scopePendingDelivery($query)
    {
        return $query->where('delivery_status', 'pending');
    }

    public function scopeFailedDelivery($query)
    {
        return $query->where('delivery_status', 'failed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_at', '<', now())
                    ->where('status', 'scheduled');
    }

    public function scopeDueSoon($query, $minutes = 15)
    {
        return $query->whereBetween('scheduled_at', [
            now(),
            now()->addMinutes($minutes)
        ])->where('status', 'scheduled');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeExpiringSoon($query, $hours = 24)
    {
        return $query->whereBetween('expires_at', [
            now(),
            now()->addHours($hours)
        ]);
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

    public function scopeByRetryCount($query, $maxCount)
    {
        return $query->where('retry_count', '<=', $maxCount);
    }

    public function scopeCanRetry($query)
    {
        return $query->where('retry_count', '<', 'max_retries')
                    ->where('status', 'failed');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeChronological($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    public function scopeByPriorityOrder($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('created_at', 'desc');
    }

    public function scopeByStatusOrder($query)
    {
        return $query->orderByRaw("FIELD(status, 'pending', 'scheduled', 'sent', 'failed')")
                    ->orderBy('created_at', 'desc');
    }

    public function scopeByTypeOrder($query)
    {
        return $query->orderBy('type')->orderBy('created_at', 'desc');
    }

    public function scopeByCategoryOrder($query)
    {
        return $query->orderBy('category')->orderBy('created_at', 'desc');
    }

    public function scopeByScheduledTime($query)
    {
        return $query->orderBy('scheduled_at', 'asc');
    }

    public function scopeByExpiryTime($query)
    {
        return $query->orderBy('expires_at', 'asc');
    }

    public function getTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->type));
    }

    public function getCategoryDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->category));
    }

    public function getSubcategoryDisplayAttribute(): string
    {
        if (!$this->subcategory) return 'N/A';
        return ucwords(str_replace('_', ' ', $this->subcategory));
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'scheduled' => 'info',
            'sent' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            'expired' => 'dark',
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

    public function getDeliveryMethodDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->delivery_method));
    }

    public function getDeliveryStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->delivery_status));
    }

    public function getDeliveryStatusColorAttribute(): string
    {
        return match($this->delivery_status) {
            'pending' => 'warning',
            'sent' => 'info',
            'delivered' => 'success',
            'failed' => 'danger',
            'bounced' => 'secondary',
            'spam' => 'dark',
            default => 'secondary'
        };
    }

    public function getReadAtDisplayAttribute(): string
    {
        if (!$this->read_at) return 'Not read';
        return $this->read_at->format('M d, Y \a\t H:i');
    }

    public function getScheduledAtDisplayAttribute(): string
    {
        if (!$this->scheduled_at) return 'Immediate';
        return $this->scheduled_at->format('M d, Y \a\t H:i');
    }

    public function getSentAtDisplayAttribute(): string
    {
        if (!$this->sent_at) return 'Not sent yet';
        return $this->sent_at->format('M d, Y \a\t H:i');
    }

    public function getExpiresAtDisplayAttribute(): string
    {
        if (!$this->expires_at) return 'Never expires';
        return $this->expires_at->format('M d, Y \a\t H:i');
    }

    public function getCreatedAtDisplayAttribute(): string
    {
        return $this->created_at->format('M d, Y \a\t H:i');
    }

    public function getUpdatedAtDisplayAttribute(): string
    {
        return $this->updated_at->format('M d, Y \a\t H:i');
    }

    public function getMetadataDisplayAttribute(): string
    {
        if (!$this->metadata || empty($this->metadata)) return 'No additional data';
        
        $display = [];
        foreach ($this->metadata as $key => $value) {
            $display[] = ucwords(str_replace('_', ' ', $key)) . ': ' . $value;
        }
        
        return implode(', ', $display);
    }

    public function getActionUrlDisplayAttribute(): string
    {
        return $this->action_url ?: 'No action available';
    }

    public function getActionTextDisplayAttribute(): string
    {
        return $this->action_text ?: 'View Details';
    }

    public function getRelatedModelDisplayAttribute(): string
    {
        if (!$this->related_model) return 'N/A';
        return ucwords(str_replace('_', ' ', $this->related_model));
    }

    public function getRelatedIdDisplayAttribute(): string
    {
        return $this->related_id ?: 'N/A';
    }

    public function getErrorMessageDisplayAttribute(): string
    {
        return $this->error_message ?: 'No error details';
    }

    public function getRetryCountDisplayAttribute(): string
    {
        return $this->retry_count . '/' . $this->max_retries;
    }

    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }

    public function getIsUnreadAttribute(): bool
    {
        return $this->read_at === null;
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsScheduledAttribute(): bool
    {
        return $this->status === 'scheduled';
    }

    public function getIsSentAttribute(): bool
    {
        return $this->status === 'sent';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->scheduled_at && $this->scheduled_at < now() && $this->status === 'scheduled';
    }

    public function getIsDueSoonAttribute(): bool
    {
        if (!$this->scheduled_at || $this->status !== 'scheduled') return false;
        
        $dueTime = now()->addMinutes(15);
        return $this->scheduled_at <= $dueTime && $this->scheduled_at >= now();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expires_at) return false;
        
        $expiryTime = now()->addHours(24);
        return $this->expires_at <= $expiryTime && $this->expires_at >= now();
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

    public function getIsDeliveredAttribute(): bool
    {
        return $this->delivery_status === 'delivered';
    }

    public function getIsDeliveryPendingAttribute(): bool
    {
        return $this->delivery_status === 'pending';
    }

    public function getIsDeliveryFailedAttribute(): bool
    {
        return $this->delivery_status === 'failed';
    }

    public function getCanRetryAttribute(): bool
    {
        return $this->status === 'failed' && $this->retry_count < $this->max_retries;
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        if (!$this->expires_at || $this->is_expired) return 0;
        return now()->diffInDays($this->expires_at, false);
    }

    public function getDaysUntilScheduledAttribute(): int
    {
        if (!$this->scheduled_at || $this->is_overdue) return 0;
        return now()->diffInDays($this->scheduled_at, false);
    }

    public function getMinutesUntilScheduledAttribute(): int
    {
        if (!$this->scheduled_at || $this->is_overdue) return 0;
        return now()->diffInMinutes($this->scheduled_at, false);
    }

    public function getHoursUntilExpiryAttribute(): int
    {
        if (!$this->expires_at || $this->is_expired) return 0;
        return now()->diffInHours($this->expires_at, false);
    }

    public function getNotificationSummaryAttribute(): string
    {
        $summary = $this->title;
        
        if ($this->category) {
            $summary .= ' (' . $this->category_display . ')';
        }
        
        $summary .= ' - ' . $this->status_display;
        
        if ($this->is_high_priority) {
            $summary .= ' - HIGH PRIORITY';
        }
        
        if ($this->is_scheduled) {
            $summary .= ' - Scheduled: ' . $this->scheduled_at_display;
        }
        
        return $summary;
    }

    public function getTimeAgoAttribute(): string
    {
        $diff = now()->diff($this->created_at);
        
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

    public function getScheduledTimeAgoAttribute(): string
    {
        if (!$this->scheduled_at) return 'Immediate';
        
        $diff = now()->diff($this->scheduled_at);
        
        if ($diff->invert) {
            // Future
            if ($diff->d > 0) {
                return 'In ' . $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
            } elseif ($diff->h > 0) {
                return 'In ' . $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
            } elseif ($diff->i > 0) {
                return 'In ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
            } else {
                return 'Very soon';
            }
        } else {
            // Past
            if ($diff->d > 0) {
                return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
            } elseif ($diff->h > 0) {
                return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
            } elseif ($diff->i > 0) {
                return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
            } else {
                return 'Just now';
            }
        }
    }

    public function canBeRead(): bool
    {
        return $this->is_unread;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'scheduled']);
    }

    public function canBeRescheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function canBeRetried(): bool
    {
        return $this->can_retry;
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['pending', 'scheduled']);
    }

    public function canBeDeleted(): bool
    {
        return in_array($this->status, ['sent', 'failed', 'cancelled', 'expired']);
    }

    public function markAsRead(): void
    {
        if ($this->can_be_read) {
            $this->read_at = now();
            $this->save();
        }
    }

    public function markAsUnread(): void
    {
        $this->read_at = null;
        $this->save();
    }

    public function markAsSent(): void
    {
        $this->status = 'sent';
        $this->sent_at = now();
        $this->save();
    }

    public function markAsFailed(string $errorMessage = null): void
    {
        $this->status = 'failed';
        
        if ($errorMessage) {
            $this->error_message = $errorMessage;
        }
        
        $this->save();
    }

    public function cancel(string $reason = null): void
    {
        if ($this->can_be_cancelled) {
            $this->status = 'cancelled';
            
            if ($reason) {
                $this->metadata = array_merge($this->metadata ?? [], ['cancellation_reason' => $reason]);
            }
            
            $this->save();
        }
    }

    public function reschedule(string $newScheduledAt): void
    {
        if ($this->can_be_rescheduled) {
            $this->scheduled_at = $newScheduledAt;
            $this->save();
        }
    }

    public function retry(): void
    {
        if ($this->can_be_retried) {
            $this->retry_count++;
            $this->status = 'pending';
            $this->delivery_status = 'pending';
            $this->error_message = null;
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

    public function extendExpiry(string $newExpiryAt): void
    {
        $this->expires_at = $newExpiryAt;
        $this->save();
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

    public function getNotificationStatistics(): array
    {
        return [
            'type' => $this->type,
            'category' => $this->category,
            'subcategory' => $this->subcategory,
            'status' => $this->status,
            'priority' => $this->priority,
            'delivery_method' => $this->delivery_method,
            'delivery_status' => $this->delivery_status,
            'scheduled_at' => $this->scheduled_at,
            'sent_at' => $this->sent_at,
            'expires_at' => $this->expires_at,
            'is_read' => $this->is_read,
            'is_unread' => $this->is_unread,
            'is_pending' => $this->is_pending,
            'is_scheduled' => $this->is_scheduled,
            'is_sent' => $this->is_sent,
            'is_failed' => $this->is_failed,
            'is_cancelled' => $this->is_cancelled,
            'is_expired' => $this->is_expired,
            'is_overdue' => $this->is_overdue,
            'is_due_soon' => $this->is_due_soon,
            'is_expiring_soon' => $this->is_expiring_soon,
            'is_high_priority' => $this->is_high_priority,
            'is_medium_priority' => $this->is_medium_priority,
            'is_low_priority' => $this->is_low_priority,
            'is_delivered' => $this->is_delivered,
            'is_delivery_pending' => $this->is_delivery_pending,
            'is_delivery_failed' => $this->is_delivery_failed,
            'can_retry' => $this->can_retry,
            'days_until_expiry' => $this->days_until_expiry,
            'days_until_scheduled' => $this->days_until_scheduled,
            'minutes_until_scheduled' => $this->minutes_until_scheduled,
            'hours_until_expiry' => $this->hours_until_expiry,
            'time_ago' => $this->time_ago,
            'scheduled_time_ago' => $this->scheduled_time_ago,
            'can_be_read' => $this->can_be_read,
            'can_be_cancelled' => $this->can_be_cancelled,
            'can_be_rescheduled' => $this->can_be_rescheduled,
            'can_be_retried' => $this->can_be_retried,
            'can_be_edited' => $this->can_be_edited,
            'can_be_deleted' => $this->can_be_deleted
        ];
    }
}
