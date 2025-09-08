<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ChatGroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id', 'user_id', 'role', 'status', 'joined_at', 'left_at',
        'banned_at', 'banned_by', 'ban_reason', 'muted_at', 'muted_by',
        'mute_reason', 'mute_duration', 'invited_by', 'invited_at',
        'last_activity', 'message_count', 'file_share_count', 'notes'
    ];

    protected $casts = [
        'joined_at' => 'datetime', 'left_at' => 'datetime', 'banned_at' => 'datetime',
        'muted_at' => 'datetime', 'invited_at' => 'datetime', 'last_activity' => 'datetime',
        'message_count' => 'integer', 'file_share_count' => 'integer'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    public function mutedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'muted_by');
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByJoinedDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('joined_at', [$startDate, $endDate]);
    }

    public function scopeByLastActivityRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('last_activity', [$startDate, $endDate]);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeModerators($query)
    {
        return $query->where('role', 'moderator');
    }

    public function scopeRegularMembers($query)
    {
        return $query->where('role', 'member');
    }

    public function scopeBanned($query)
    {
        return $query->where('status', 'banned');
    }

    public function scopeMuted($query)
    {
        return $query->where('status', 'muted');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeLeft($query)
    {
        return $query->where('status', 'left');
    }

    public function scopeByMessageCount($query, $minCount)
    {
        return $query->where('message_count', '>=', $minCount);
    }

    public function scopeByFileShareCount($query, $minCount)
    {
        return $query->where('file_share_count', '>=', $minCount);
    }

    public function scopeByLastActivity($query, $days)
    {
        return $query->where('last_activity', '>=', now()->subDays($days));
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('joined_at', 'desc');
    }

    public function scopeByRoleOrder($query)
    {
        return $query->orderByRaw("FIELD(role, 'admin', 'moderator', 'member')");
    }

    public function scopeByActivity($query)
    {
        return $query->orderBy('last_activity', 'desc');
    }

    public function scopeByMessageCountOrder($query)
    {
        return $query->orderBy('message_count', 'desc');
    }

    public function getRoleDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->role));
    }

    public function getRoleColorAttribute(): string
    {
        return match($this->role) {
            'admin' => 'danger',
            'moderator' => 'warning',
            'member' => 'info',
            default => 'secondary'
        };
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'banned' => 'danger',
            'muted' => 'warning',
            'left' => 'dark',
            'pending' => 'info',
            default => 'secondary'
        };
    }

    public function getJoinedAtDisplayAttribute(): string
    {
        return $this->joined_at->format('M d, Y \a\t H:i');
    }

    public function getLeftAtDisplayAttribute(): string
    {
        if (!$this->left_at) return 'Still a member';
        return $this->left_at->format('M d, Y \a\t H:i');
    }

    public function getBannedAtDisplayAttribute(): string
    {
        if (!$this->banned_at) return 'Not banned';
        return $this->banned_at->format('M d, Y \a\t H:i');
    }

    public function getMutedAtDisplayAttribute(): string
    {
        if (!$this->muted_at) return 'Not muted';
        return $this->muted_at->format('M d, Y \a\t H:i');
    }

    public function getInvitedAtDisplayAttribute(): string
    {
        if (!$this->invited_at) return 'Direct join';
        return $this->invited_at->format('M d, Y \a\t H:i');
    }

    public function getLastActivityDisplayAttribute(): string
    {
        if (!$this->last_activity) return 'Never';
        return $this->last_activity->format('M d, Y \a\t H:i');
    }

    public function getBanReasonDisplayAttribute(): string
    {
        return $this->ban_reason ?: 'No reason provided';
    }

    public function getMuteReasonDisplayAttribute(): string
    {
        return $this->mute_reason ?: 'No reason provided';
    }

    public function getMuteDurationDisplayAttribute(): string
    {
        if (!$this->mute_duration) return 'Indefinite';
        return $this->mute_duration . ' minutes';
    }

    public function getNotesDisplayAttribute(): string
    {
        return $this->notes ?: 'No notes';
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    public function getIsModeratorAttribute(): bool
    {
        return in_array($this->role, ['admin', 'moderator']);
    }

    public function getIsRegularMemberAttribute(): bool
    {
        return $this->role === 'member';
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getIsBannedAttribute(): bool
    {
        return $this->status === 'banned';
    }

    public function getIsMutedAttribute(): bool
    {
        return $this->status === 'muted';
    }

    public function getIsInactiveAttribute(): bool
    {
        return $this->status === 'inactive';
    }

    public function getHasLeftAttribute(): bool
    {
        return $this->status === 'left';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getDaysSinceJoinedAttribute(): int
    {
        return now()->diffInDays($this->joined_at);
    }

    public function getDaysSinceLeftAttribute(): int
    {
        if (!$this->left_at) return 0;
        return now()->diffInDays($this->left_at);
    }

    public function getDaysSinceBannedAttribute(): int
    {
        if (!$this->banned_at) return 0;
        return now()->diffInDays($this->banned_at);
    }

    public function getDaysSinceMutedAttribute(): int
    {
        if (!$this->muted_at) return 0;
        return now()->diffInDays($this->muted_at);
    }

    public function getDaysSinceLastActivityAttribute(): int
    {
        if (!$this->last_activity) return 0;
        return now()->diffInDays($this->last_activity);
    }

    public function getIsMuteExpiredAttribute(): bool
    {
        if (!$this->muted_at || !$this->mute_duration) return false;
        
        $muteEndTime = $this->muted_at->addMinutes($this->mute_duration);
        return now() > $muteEndTime;
    }

    public function getMuteEndTimeAttribute()
    {
        if (!$this->muted_at || !$this->mute_duration) return null;
        return $this->muted_at->addMinutes($this->mute_duration);
    }

    public function getMuteEndTimeDisplayAttribute(): string
    {
        $endTime = $this->mute_end_time;
        if (!$endTime) return 'Indefinite';
        return $endTime->format('M d, Y \a\t H:i');
    }

    public function getDaysUntilMuteExpiresAttribute(): int
    {
        if (!$this->is_muted || $this->is_mute_expired) return 0;
        
        $endTime = $this->mute_end_time;
        if (!$endTime) return 0;
        
        return now()->diffInDays($endTime, false);
    }

    public function getMemberSummaryAttribute(): string
    {
        $summary = $this->user->name . ' (' . $this->role_display . ')';
        
        $summary .= ' - ' . $this->status_display;
        
        if ($this->is_muted) {
            $summary .= ' - Muted until ' . $this->mute_end_time_display;
        }
        
        if ($this->message_count > 0) {
            $summary .= ' - ' . $this->message_count . ' messages';
        }
        
        return $summary;
    }

    public function getBannerDisplayAttribute(): string
    {
        if (!$this->banned_by) return 'Not banned';
        return $this->bannedBy->name ?? 'Unknown';
    }

    public function getMuterDisplayAttribute(): string
    {
        if (!$this->muted_by) return 'Not muted';
        return $this->mutedBy->name ?? 'Unknown';
    }

    public function getInviterDisplayAttribute(): string
    {
        if (!$this->invited_by) return 'Direct join';
        return $this->invitedBy->name ?? 'Unknown';
    }

    public function getActivityStatusAttribute(): string
    {
        if (!$this->last_activity) return 'Never Active';
        
        $days = $this->days_since_last_activity;
        
        if ($days == 0) return 'Active Today';
        if ($days == 1) return 'Active Yesterday';
        if ($days <= 7) return 'Active This Week';
        if ($days <= 30) return 'Active This Month';
        if ($days <= 90) return 'Active This Quarter';
        return 'Inactive';
    }

    public function getActivityStatusColorAttribute(): string
    {
        $status = $this->activity_status;
        
        return match($status) {
            'Active Today' => 'success',
            'Active Yesterday' => 'success',
            'Active This Week' => 'info',
            'Active This Month' => 'warning',
            'Active This Quarter' => 'secondary',
            'Inactive' => 'danger',
            'Never Active' => 'dark',
            default => 'secondary'
        };
    }

    public function canPromote(): bool
    {
        return $this->role === 'member';
    }

    public function canDemote(): bool
    {
        return in_array($this->role, ['admin', 'moderator']);
    }

    public function canBan(): bool
    {
        return $this->status === 'active' && !$this->is_admin;
    }

    public function canUnban(): bool
    {
        return $this->status === 'banned';
    }

    public function canMute(): bool
    {
        return $this->status === 'active' && !$this->is_admin;
    }

    public function canUnmute(): bool
    {
        return $this->status === 'muted';
    }

    public function canRemove(): bool
    {
        return !$this->is_admin || $this->group->admin_id !== $this->user_id;
    }

    public function promote(string $notes = null): void
    {
        if ($this->role === 'member') {
            $this->role = 'moderator';
            
            if ($notes) {
                $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Promoted: ' . $notes;
            }
            
            $this->save();
        }
    }

    public function demote(string $notes = null): void
    {
        if (in_array($this->role, ['admin', 'moderator'])) {
            $this->role = 'member';
            
            if ($notes) {
                $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Demoted: ' . $notes;
            }
            
            $this->save();
        }
    }

    public function ban(User $banner, string $reason, string $notes = null): void
    {
        $this->status = 'banned';
        $this->banned_by = $banner->id;
        $this->banned_at = now();
        $this->ban_reason = $reason;
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Banned: ' . $notes;
        }
        
        $this->save();
    }

    public function unban(string $notes = null): void
    {
        $this->status = 'active';
        $this->banned_by = null;
        $this->banned_at = null;
        $this->ban_reason = null;
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Unbanned: ' . $notes;
        }
        
        $this->save();
    }

    public function mute(User $muter, string $reason, int $duration = null, string $notes = null): void
    {
        $this->status = 'muted';
        $this->muted_by = $muter->id;
        $this->muted_at = now();
        $this->mute_reason = $reason;
        $this->mute_duration = $duration;
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Muted: ' . $notes;
        }
        
        $this->save();
    }

    public function unmute(string $notes = null): void
    {
        $this->status = 'active';
        $this->muted_by = null;
        $this->muted_at = null;
        $this->mute_reason = null;
        $this->mute_duration = null;
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Unmuted: ' . $notes;
        }
        
        $this->save();
    }

    public function leave(string $notes = null): void
    {
        $this->status = 'left';
        $this->left_at = now();
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Left: ' . $notes;
        }
        
        $this->save();
    }

    public function deactivate(string $notes = null): void
    {
        $this->status = 'inactive';
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Deactivated: ' . $notes;
        }
        
        $this->save();
    }

    public function reactivate(string $notes = null): void
    {
        $this->status = 'active';
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Reactivated: ' . $notes;
        }
        
        $this->save();
    }

    public function updateLastActivity(): void
    {
        $this->last_activity = now();
        $this->save();
    }

    public function incrementMessageCount(): void
    {
        $this->increment('message_count');
    }

    public function incrementFileShareCount(): void
    {
        $this->increment('file_share_count');
    }

    public function getMemberStatistics(): array
    {
        return [
            'role' => $this->role,
            'status' => $this->status,
            'joined_at' => $this->joined_at,
            'left_at' => $this->left_at,
            'banned_at' => $this->banned_at,
            'muted_at' => $this->muted_at,
            'invited_at' => $this->invited_at,
            'last_activity' => $this->last_activity,
            'message_count' => $this->message_count,
            'file_share_count' => $this->file_share_count,
            'is_admin' => $this->is_admin,
            'is_moderator' => $this->is_moderator,
            'is_regular_member' => $this->is_regular_member,
            'is_active' => $this->is_active,
            'is_banned' => $this->is_banned,
            'is_muted' => $this->is_muted,
            'is_inactive' => $this->is_inactive,
            'has_left' => $this->has_left,
            'is_pending' => $this->is_pending,
            'is_mute_expired' => $this->is_mute_expired,
            'days_since_joined' => $this->days_since_joined,
            'days_since_left' => $this->days_since_left,
            'days_since_banned' => $this->days_since_banned,
            'days_since_muted' => $this->days_since_muted,
            'days_since_last_activity' => $this->days_since_last_activity,
            'days_until_mute_expires' => $this->days_until_mute_expires,
            'activity_status' => $this->activity_status,
            'mute_end_time' => $this->mute_end_time
        ];
    }
}
