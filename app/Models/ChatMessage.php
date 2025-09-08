<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id', 'receiver_id', 'group_id', 'message', 'message_type',
        'file_path', 'file_name', 'file_type', 'file_size', 'thumbnail',
        'metadata', 'is_edited', 'edited_at', 'is_deleted', 'deleted_at',
        'is_read', 'read_at', 'is_pinned', 'pinned_at', 'pinned_by',
        'reply_to_message', 'reply_to_id', 'status'
    ];

    protected $casts = [
        'metadata' => 'array', 'is_edited' => 'boolean', 'edited_at' => 'datetime',
        'is_deleted' => 'boolean', 'deleted_at' => 'datetime', 'is_read' => 'boolean',
        'read_at' => 'datetime', 'is_pinned' => 'boolean', 'pinned_at' => 'datetime',
        'file_size' => 'integer'
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

    public function pinnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pinned_by');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id');
    }

    public function scopeBySender($query, $senderId)
    {
        return $query->where('sender_id', $senderId);
    }

    public function scopeByReceiver($query, $receiverId)
    {
        return $query->where('receiver_id', $receiverId);
    }

    public function scopeByGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('message_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getMessageTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->message_type));
    }

    public function getIsDirectMessageAttribute(): bool
    {
        return !empty($this->receiver_id) && empty($this->group_id);
    }

    public function getIsGroupMessageAttribute(): bool
    {
        return !empty($this->group_id);
    }

    public function getIsFileMessageAttribute(): bool
    {
        return $this->message_type === 'file';
    }

    public function getIsImageMessageAttribute(): bool
    {
        return $this->message_type === 'image';
    }

    public function getIsVideoMessageAttribute(): bool
    {
        return $this->message_type === 'video';
    }

    public function getIsAudioMessageAttribute(): bool
    {
        return $this->message_type === 'audio';
    }

    public function getIsTextMessageAttribute(): bool
    {
        return $this->message_type === 'text';
    }

    public function getFileSizeDisplayAttribute(): string
    {
        if (!$this->file_size) return 'N/A';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getFileTypeIconAttribute(): string
    {
        if (!$this->file_type) return 'fas fa-file';
        
        return match(strtolower($this->file_type)) {
            'pdf' => 'fas fa-file-pdf',
            'doc', 'docx' => 'fas fa-file-word',
            'xls', 'xlsx' => 'fas fa-file-excel',
            'ppt', 'pptx' => 'fas fa-file-powerpoint',
            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image',
            'mp4', 'avi', 'mov' => 'fas fa-file-video',
            'mp3', 'wav' => 'fas fa-file-audio',
            'zip', 'rar' => 'fas fa-file-archive',
            'txt' => 'fas fa-file-alt',
            default => 'fas fa-file'
        };
    }

    public function getFileTypeColorAttribute(): string
    {
        if (!$this->file_type) return 'secondary';
        
        return match(strtolower($this->file_type)) {
            'pdf' => 'danger',
            'doc', 'docx' => 'primary',
            'xls', 'xlsx' => 'success',
            'ppt', 'pptx' => 'warning',
            'jpg', 'jpeg', 'png', 'gif' => 'info',
            'mp4', 'avi', 'mov' => 'dark',
            'mp3', 'wav' => 'secondary',
            'zip', 'rar' => 'warning',
            'txt' => 'secondary',
            default => 'secondary'
        };
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        
        if ($this->is_image) {
            return asset('storage/' . $this->file_path);
        }
        
        return asset('images/file-types/' . strtolower($this->file_type) . '.png');
    }

    public function getFileUrlAttribute(): string
    {
        if ($this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        
        return '#';
    }

    public function getDownloadUrlAttribute(): string
    {
        if ($this->file_path) {
            return route('chat.download', $this->id);
        }
        
        return '#';
    }

    public function getMessagePreviewAttribute(): string
    {
        if ($this->is_text_message) {
            $text = $this->message;
            if (strlen($text) > 100) {
                return substr($text, 0, 100) . '...';
            }
            return $text;
        }
        
        if ($this->is_file_message) {
            return 'File: ' . $this->file_name;
        }
        
        if ($this->is_image_message) {
            return 'Image';
        }
        
        if ($this->is_video_message) {
            return 'Video';
        }
        
        if ($this->is_audio_message) {
            return 'Audio';
        }
        
        return 'Message';
    }

    public function getTimeAgoAttribute(): string
    {
        $now = now();
        $diff = $now->diff($this->created_at);
        
        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y != 1 ? 's' : '') . ' ago';
        }
        
        if ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m != 1 ? 's' : '') . ' ago';
        }
        
        if ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d != 1 ? 's' : '') . ' ago';
        }
        
        if ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h != 1 ? 's' : '') . ' ago';
        }
        
        if ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i != 1 ? 's' : '') . ' ago';
        }
        
        return 'Just now';
    }

    public function getFormattedTimeAttribute(): string
    {
        $now = now();
        $today = $now->toDateString();
        $yesterday = $now->subDay()->toDateString();
        
        if ($this->created_at->toDateString() === $today) {
            return $this->created_at->format('H:i');
        }
        
        if ($this->created_at->toDateString() === $yesterday) {
            return 'Yesterday at ' . $this->created_at->format('H:i');
        }
        
        if ($this->created_at->year === $now->year) {
            return $this->created_at->format('M d at H:i');
        }
        
        return $this->created_at->format('M d, Y at H:i');
    }

    public function getIsEditedDisplayAttribute(): string
    {
        return $this->is_edited ? '(edited)' : '';
    }

    public function getIsPinnedDisplayAttribute(): string
    {
        return $this->is_pinned ? '📌 ' : '';
    }

    public function getReplyPreviewAttribute(): string
    {
        if (!$this->reply_to_message) return '';
        
        $reply = $this->replyTo;
        if (!$reply) return '';
        
        $preview = $reply->message_preview;
        if (strlen($preview) > 50) {
            $preview = substr($preview, 0, 50) . '...';
        }
        
        return '↩ ' . $preview;
    }

    public function getChatTypeAttribute(): string
    {
        if ($this->is_direct_message) return 'Direct Message';
        if ($this->is_group_message) return 'Group Chat';
        return 'Unknown';
    }

    public function getRecipientAttribute(): string
    {
        if ($this->is_direct_message) {
            return $this->receiver->name ?? 'Unknown User';
        }
        
        if ($this->is_group_message) {
            return $this->group->group_name ?? 'Unknown Group';
        }
        
        return 'Unknown';
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->is_read = true;
            $this->read_at = now();
            $this->save();
        }
    }

    public function markAsUnread(): void
    {
        $this->is_read = false;
        $this->read_at = null;
        $this->save();
    }

    public function pin(User $user): void
    {
        $this->is_pinned = true;
        $this->pinned_at = now();
        $this->pinned_by = $user->id;
        $this->save();
    }

    public function unpin(): void
    {
        $this->is_pinned = false;
        $this->pinned_at = null;
        $this->pinned_by = null;
        $this->save();
    }

    public function edit(string $newMessage): void
    {
        $this->message = $newMessage;
        $this->is_edited = true;
        $this->edited_at = now();
        $this->save();
    }

    public function softDelete(): void
    {
        $this->is_deleted = true;
        $this->deleted_at = now();
        $this->save();
    }

    public function restore(): void
    {
        $this->is_deleted = false;
        $this->deleted_at = null;
        $this->save();
    }

    public function canEdit(User $user): bool
    {
        return $user->id === $this->sender_id && !$this->is_deleted;
    }

    public function canDelete(User $user): bool
    {
        return $user->id === $this->sender_id || 
               $user->user_type === 'admin' ||
               ($this->is_group_message && $user->id === $this->group->admin_id);
    }

    public function canPin(User $user): bool
    {
        if ($this->is_direct_message) {
            return $user->id === $this->receiver_id;
        }
        
        if ($this->is_group_message) {
            return $user->id === $this->group->admin_id || 
                   $user->user_type === 'admin';
        }
        
        return false;
    }
}
