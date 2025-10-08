<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number', 'message', 'type', 'status', 'provider',
        'provider_message_id', 'provider_response', 'cost',
        'scheduled_at', 'sent_at', 'delivered_at', 'expires_at',
        'metadata', 'user_id', 'student_id', 'parent_id'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'expires_at' => 'datetime',
        'cost' => 'decimal:4',
        'metadata' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'parent_id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at');
    }

    public function scopeReadyToSend($query)
    {
        return $query->where('status', 'pending')
                    ->where(function($q) {
                        $q->whereNull('scheduled_at')
                          ->orWhere('scheduled_at', '<=', now());
                    });
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'sent' => 'blue',
            'delivered' => 'green',
            'failed' => 'red',
            'expired' => 'gray',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'sent' => 'Sent',
            'delivered' => 'Delivered',
            'failed' => 'Failed',
            'expired' => 'Expired',
            default => 'Unknown'
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'attendance' => 'blue',
            'grades' => 'green',
            'urgent' => 'red',
            'general' => 'gray',
            'payment' => 'yellow',
            'exam' => 'purple',
            'event' => 'orange',
            default => 'gray'
        };
    }

    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'attendance' => 'Attendance',
            'grades' => 'Grades',
            'urgent' => 'Urgent',
            'general' => 'General',
            'payment' => 'Payment',
            'exam' => 'Exam',
            'event' => 'Event',
            default => 'Unknown'
        };
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSent()
    {
        return $this->status === 'sent';
    }

    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    public function canBeSent()
    {
        return $this->status === 'pending' && 
               (is_null($this->scheduled_at) || $this->scheduled_at <= now());
    }

    public function markAsSent($providerMessageId = null, $cost = null)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'provider_message_id' => $providerMessageId,
            'cost' => $cost
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);
    }

    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'provider_response' => $reason
        ]);
    }

    public function markAsExpired()
    {
        $this->update([
            'status' => 'expired'
        ]);
    }

    public static function createForStudent($student, $message, $type = 'general', $metadata = [])
    {
        $phoneNumbers = [];
        
        // Get student's phone if available
        if ($student->user && $student->user->phone) {
            $phoneNumbers[] = $student->user->phone;
        }
        
        // Get parent/guardian phone numbers
        if ($student->guardian && $student->guardian->phone) {
            $phoneNumbers[] = $student->guardian->phone;
        }

        $notifications = [];
        foreach ($phoneNumbers as $phoneNumber) {
            $notifications[] = self::create([
                'phone_number' => $phoneNumber,
                'message' => $message,
                'type' => $type,
                'student_id' => $student->id,
                'user_id' => $student->user_id,
                'parent_id' => $student->guardian_id,
                'metadata' => $metadata
            ]);
        }

        return $notifications;
    }

    public static function createForParent($parent, $message, $type = 'general', $metadata = [])
    {
        return self::create([
            'phone_number' => $parent->phone,
            'message' => $message,
            'type' => $type,
            'parent_id' => $parent->id,
            'metadata' => $metadata
        ]);
    }

    public static function createForUser($user, $message, $type = 'general', $metadata = [])
    {
        return self::create([
            'phone_number' => $user->phone,
            'message' => $message,
            'type' => $type,
            'user_id' => $user->id,
            'metadata' => $metadata
        ]);
    }
}
