<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_number', 'visitor_id', 'student_id', 'staff_id', 'checked_in_by',
        'checked_out_by', 'purpose', 'purpose_details', 'destination',
        'escort_name', 'escort_phone', 'status', 'check_in_time',
        'expected_check_out_time', 'actual_check_out_time', 'check_in_notes',
        'check_out_notes', 'attachments', 'vehicle_parked', 'vehicle_plate',
        'vehicle_make', 'vehicle_model', 'vehicle_color', 'special_instructions',
        'emergency_contact_notified', 'emergency_contact_notes', 'metadata'
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'expected_check_out_time' => 'datetime',
        'actual_check_out_time' => 'datetime',
        'attachments' => 'array',
        'vehicle_parked' => 'boolean',
        'emergency_contact_notified' => 'boolean',
        'metadata' => 'array'
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class, 'visitor_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('status', 'checked_in');
    }

    public function scopeCheckedOut($query)
    {
        return $query->where('status', 'checked_out');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByVisitor($query, $visitorId)
    {
        return $query->where('visitor_id', $visitorId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('check_in_time', [$startDate, $endDate]);
    }

    public function scopeByPurpose($query, $purpose)
    {
        return $query->where('purpose', 'like', "%{$purpose}%");
    }

    public function scopeByDestination($query, $destination)
    {
        return $query->where('destination', 'like', "%{$destination}%");
    }

    public function scopeToday($query)
    {
        return $query->whereDate('check_in_time', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('check_in_time', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('check_in_time', now()->month)
            ->whereYear('check_in_time', now()->year);
    }

    public function scopeWithVehicle($query)
    {
        return $query->where('vehicle_parked', true);
    }

    public function scopeRequiresEscort($query)
    {
        return $query->whereNotNull('escort_name');
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'checked_in' => 'success',
            'checked_out' => 'info',
            'overdue' => 'warning',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'checked_in' => 'Checked In',
            'checked_out' => 'Checked Out',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status)
        };
    }

    public function getIsCheckedInAttribute(): bool
    {
        return $this->status === 'checked_in';
    }

    public function getIsCheckedOutAttribute(): bool
    {
        return $this->status === 'checked_out';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'overdue';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getFormattedCheckInTimeAttribute(): string
    {
        return $this->check_in_time->format('M d, Y H:i');
    }

    public function getFormattedCheckOutTimeAttribute(): string
    {
        return $this->actual_check_out_time ? $this->actual_check_out_time->format('M d, Y H:i') : 'N/A';
    }

    public function getFormattedExpectedCheckOutTimeAttribute(): string
    {
        return $this->expected_check_out_time ? $this->expected_check_out_time->format('M d, Y H:i') : 'N/A';
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->actual_check_out_time) {
            return null;
        }

        $duration = $this->check_in_time->diffInMinutes($this->actual_check_out_time);
        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    public function getExpectedDurationAttribute(): ?string
    {
        if (!$this->expected_check_out_time) {
            return null;
        }

        $duration = $this->check_in_time->diffInMinutes($this->expected_check_out_time);
        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }


    public function getVisitorNameAttribute(): string
    {
        return $this->visitor ? $this->visitor->full_name : 'Unknown Visitor';
    }

    public function getVisitedPersonNameAttribute(): string
    {
        if ($this->student) {
            return $this->student->first_name . ' ' . $this->student->last_name;
        } elseif ($this->staff) {
            return $this->staff->first_name . ' ' . $this->staff->last_name;
        }
        return 'N/A';
    }

    public function getVehicleInfoAttribute(): string
    {
        if (!$this->vehicle_parked) {
            return 'No Vehicle';
        }

        $info = [];
        if ($this->vehicle_make) $info[] = $this->vehicle_make;
        if ($this->vehicle_model) $info[] = $this->vehicle_model;
        if ($this->vehicle_color) $info[] = $this->vehicle_color;
        if ($this->vehicle_plate) $info[] = "({$this->vehicle_plate})";

        return implode(' ', $info) ?: 'Vehicle Parked';
    }

    // Methods
    public function generateLogNumber(): string
    {
        $prefix = 'VL';
        $count = static::count() + 1;
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    public function checkOut($checkedOutBy, $notes = null): void
    {
        $this->update([
            'status' => 'checked_out',
            'checked_out_by' => $checkedOutBy,
            'actual_check_out_time' => now(),
            'check_out_notes' => $notes
        ]);
    }

    public function markAsOverdue(): void
    {
        $this->update(['status' => 'overdue']);
    }

    public function cancel($notes = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'check_out_notes' => $notes
        ]);
    }

    public function notifyEmergencyContact(): void
    {
        $this->update(['emergency_contact_notified' => true]);
    }

    public function canBeCheckedOut(): bool
    {
        return $this->status === 'checked_in';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['checked_in', 'overdue']);
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'checked_in';
    }

    public function canBeDeleted(): bool
    {
        return in_array($this->status, ['checked_out', 'cancelled']);
    }

    public function getVisitSummary(): array
    {
        return [
            'log_number' => $this->log_number,
            'visitor_name' => $this->visitor_name,
            'visited_person' => $this->visited_person_name,
            'purpose' => $this->purpose,
            'destination' => $this->destination,
            'check_in_time' => $this->formatted_check_in_time,
            'check_out_time' => $this->formatted_check_out_time,
            'duration' => $this->duration,
            'status' => $this->status_text,
            'vehicle_info' => $this->vehicle_info
        ];
    }
}
