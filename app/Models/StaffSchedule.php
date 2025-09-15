<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'schedule_date',
        'start_time',
        'end_time',
        'shift_type',
        'work_location',
        'duties',
        'status',
        'notes',
        'assigned_by',
        'confirmed_at',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'confirmed_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('schedule_date', $date);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByShiftType($query, $shiftType)
    {
        return $query->where('shift_type', $shiftType);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('schedule_date', '>=', now()->toDateString())
                    ->whereIn('status', ['scheduled', 'confirmed']);
    }

    public function getDurationAttribute(): int
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        return $start->diffInMinutes($end);
    }

    public function getDurationHoursAttribute(): float
    {
        return round($this->duration / 60, 2);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'blue',
            'confirmed' => 'green',
            'in_progress' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
            'no_show' => 'red',
            default => 'gray'
        };
    }

    public function isOverdue(): bool
    {
        return $this->schedule_date < now()->toDateString() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }
}
