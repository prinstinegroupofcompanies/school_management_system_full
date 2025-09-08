<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name', 'description', 'group_avatar', 'created_by', 'admin_id',
        'group_type', 'class_id', 'subject_id', 'max_members', 'current_members',
        'is_public', 'require_invitation', 'allow_member_invite', 'allow_file_sharing',
        'max_file_size', 'allowed_file_types', 'group_rules', 'status', 'is_active'
    ];

    protected $casts = [
        'allowed_file_types' => 'array', 'max_members' => 'integer',
        'current_members' => 'integer', 'is_public' => 'boolean',
        'require_invitation' => 'boolean', 'allow_member_invite' => 'boolean',
        'allow_file_sharing' => 'boolean', 'max_file_size' => 'integer',
        'is_active' => 'boolean'
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ChatGroupMember::class, 'group_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'group_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('group_type', $type);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeByAdmin($query, $userId)
    {
        return $query->where('admin_id', $userId);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeByMember($query, $userId)
    {
        return $query->whereHas('members', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeByMemberRole($query, $role)
    {
        return $query->whereHas('members', function($q) use ($role) {
            $q->where('role', $role);
        });
    }

    public function scopeByMemberStatus($query, $status)
    {
        return $query->whereHas('members', function($q) use ($status) {
            $q->where('status', $status);
        });
    }

    public function scopePopular($query)
    {
        return $query->orderBy('current_members', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
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
            'suspended' => 'danger',
            'pending' => 'warning',
            'archived' => 'dark',
            default => 'secondary'
        };
    }

    public function getGroupTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->group_type));
    }

    public function getIsFullAttribute(): bool
    {
        return $this->current_members >= $this->max_members;
    }

    public function getAvailableSlotsAttribute(): int
    {
        return max(0, $this->max_members - $this->current_members);
    }

    public function getOccupancyPercentageAttribute(): float
    {
        if ($this->max_members == 0) return 0;
        return round(($this->current_members / $this->max_members) * 100, 2);
    }

    public function getOccupancyColorAttribute(): string
    {
        $percentage = $this->occupancy_percentage;
        
        if ($percentage >= 90) return 'danger';
        if ($percentage >= 75) return 'warning';
        if ($percentage >= 50) return 'info';
        return 'success';
    }

    public function getGroupAvatarUrlAttribute(): string
    {
        if ($this->group_avatar) {
            return asset('storage/' . $this->group_avatar);
        }
        
        return asset('images/default-group-avatar.png');
    }

    public function getAllowedFileTypesDisplayAttribute(): string
    {
        if (!$this->allowed_file_types || empty($this->allowed_file_types)) {
            return 'All file types allowed';
        }
        return implode(', ', $this->allowed_file_types);
    }

    public function getMaxFileSizeDisplayAttribute(): string
    {
        if (!$this->max_file_size) return 'No limit';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->max_file_size;
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getGroupRulesDisplayAttribute(): string
    {
        if (!$this->group_rules) return 'No specific rules';
        return $this->group_rules;
    }

    public function getIsPublicDisplayAttribute(): string
    {
        return $this->is_public ? 'Public' : 'Private';
    }

    public function getRequireInvitationDisplayAttribute(): string
    {
        return $this->require_invitation ? 'Yes' : 'No';
    }

    public function getAllowMemberInviteDisplayAttribute(): string
    {
        return $this->allow_member_invite ? 'Yes' : 'No';
    }

    public function getAllowFileSharingDisplayAttribute(): string
    {
        return $this->allow_file_sharing ? 'Yes' : 'No';
    }

    public function getTotalMessagesAttribute(): int
    {
        return $this->messages()->count();
    }

    public function getUnreadMessagesCountAttribute(): int
    {
        return $this->messages()->where('is_read', false)->count();
    }

    public function getPinnedMessagesCountAttribute(): int
    {
        return $this->messages()->where('is_pinned', true)->count();
    }

    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    public function getLastMessageTimeAttribute(): string
    {
        $lastMessage = $this->last_message;
        if (!$lastMessage) return 'No messages';
        
        return $lastMessage->time_ago;
    }

    public function getActiveMembersCountAttribute(): int
    {
        return $this->members()->where('status', 'active')->count();
    }

    public function getBannedMembersCountAttribute(): int
    {
        return $this->members()->where('status', 'banned')->count();
    }

    public function getMutedMembersCountAttribute(): int
    {
        return $this->members()->where('status', 'muted')->count();
    }

    public function getAdminMembersAttribute()
    {
        return $this->members()->where('role', 'admin')->get();
    }

    public function getModeratorMembersAttribute()
    {
        return $this->members()->where('role', 'moderator')->get();
    }

    public function getRegularMembersAttribute()
    {
        return $this->members()->where('role', 'member')->get();
    }

    public function canUserJoin(User $user): bool
    {
        if (!$this->is_active || $this->status !== 'active') {
            return false;
        }
        
        if ($this->is_full) {
            return false;
        }
        
        // Check if user is already a member
        if ($this->members()->where('user_id', $user->id)->exists()) {
            return false;
        }
        
        // Check if user is banned
        $bannedMember = $this->members()->where('user_id', $user->id)->where('status', 'banned')->first();
        if ($bannedMember) {
            return false;
        }
        
        return true;
    }

    public function canUserInvite(User $user): bool
    {
        if (!$this->allow_member_invite) {
            return false;
        }
        
        $member = $this->members()->where('user_id', $user->id)->first();
        if (!$member) {
            return false;
        }
        
        return in_array($member->role, ['admin', 'moderator']);
    }

    public function canUserShareFiles(User $user): bool
    {
        if (!$this->allow_file_sharing) {
            return false;
        }
        
        $member = $this->members()->where('user_id', $user->id)->first();
        if (!$member) {
            return false;
        }
        
        return $member->status === 'active';
    }

    public function isUserAdmin(User $user): bool
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        return $member && $member->role === 'admin';
    }

    public function isUserModerator(User $user): bool
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        return $member && in_array($member->role, ['admin', 'moderator']);
    }

    public function isUserMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function getUserRole(User $user): ?string
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        return $member ? $member->role : null;
    }

    public function getUserStatus(User $user): ?string
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        return $member ? $member->status : null;
    }

    public function addMember(User $user, string $role = 'member'): bool
    {
        if (!$this->canUserJoin($user)) {
            return false;
        }
        
        $this->members()->create([
            'user_id' => $user->id,
            'role' => $role,
            'status' => 'active',
            'joined_at' => now()
        ]);
        
        $this->increment('current_members');
        return true;
    }

    public function removeMember(User $user): bool
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        if (!$member) {
            return false;
        }
        
        $member->delete();
        $this->decrement('current_members');
        return true;
    }

    public function banMember(User $user, string $reason = null): bool
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        if (!$member) {
            return false;
        }
        
        $member->ban($reason);
        return true;
    }

    public function unbanMember(User $user): bool
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        if (!$member) {
            return false;
        }
        
        $member->unban();
        return true;
    }

    public function muteMember(User $user, int $minutes = 60, string $reason = null): bool
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        if (!$member) {
            return false;
        }
        
        $member->mute($minutes, $reason);
        return true;
    }

    public function unmuteMember(User $user): bool
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        if (!$member) {
            return false;
        }
        
        $member->unmute();
        return true;
    }

    public function changeMemberRole(User $user, string $newRole): bool
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        if (!$member) {
            return false;
        }
        
        $member->update(['role' => $newRole]);
        return true;
    }
}
