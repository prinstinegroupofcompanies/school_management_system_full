<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'class_id', 'subject_id', 'title', 'description',
        'meeting_url', 'meeting_id', 'meeting_password', 'platform',
        'scheduled_at', 'duration_minutes', 'status', 'attendance_data',
        'recording_urls', 'is_recorded', 'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'attendance_data' => 'array',
        'recording_urls' => 'array',
        'is_recorded' => 'boolean',
    ];

    /**
     * Get the teacher.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the class.
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * Get the subject.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Scope for upcoming classes.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
                     ->where('status', 'scheduled');
    }

    /**
     * Scope for live classes.
     */
    public function scopeLive($query)
    {
        return $query->where('status', 'live')
                     ->where('scheduled_at', '<=', now())
                     ->whereRaw('scheduled_at + INTERVAL duration_minutes MINUTE >= NOW()');
    }

    /**
     * Check if class is currently live.
     */
    public function isLive(): bool
    {
        return $this->status === 'live' &&
               $this->scheduled_at <= now() &&
               $this->scheduled_at->addMinutes($this->duration_minutes) >= now();
    }

    /**
     * Get end time.
     */
    public function getEndTimeAttribute()
    {
        return $this->scheduled_at->copy()->addMinutes($this->duration_minutes);
    }
}
