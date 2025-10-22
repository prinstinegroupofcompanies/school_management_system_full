<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostelStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'room_id',
        'hostel_id',
        'allocation_date',
        'check_in_date',
        'check_out_date',
        'monthly_fee',
        'security_deposit',
        'status',
        'is_active',
        'notes',
        'emergency_contact',
        'emergency_phone',
        'guardian_name',
        'guardian_phone',
    ];

    protected $casts = [
        'allocation_date' => 'date',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'monthly_fee' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the student that owns this hostel assignment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the room assigned to this student.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(HostelRoom::class);
    }

    /**
     * Get the hostel that this student is assigned to.
     */
    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    /**
     * Get the payments for this hostel assignment.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(HostelPayment::class);
    }

    /**
     * Scope to get only active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

    /**
     * Scope to get assignments by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get assignments for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get assignments for a specific room.
     */
    public function scopeForRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    /**
     * Scope to get assignments for a specific hostel.
     */
    public function scopeForHostel($query, $hostelId)
    {
        return $query->where('hostel_id', $hostelId);
    }

    /**
     * Get the status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'red',
            'suspended' => 'yellow',
            'pending' => 'blue',
            'checked_out' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get the formatted monthly fee.
     */
    public function getFormattedFeeAttribute(): string
    {
        return '$' . number_format($this->monthly_fee, 2);
    }

    /**
     * Get the formatted security deposit.
     */
    public function getFormattedDepositAttribute(): string
    {
        return '$' . number_format($this->security_deposit, 2);
    }

    /**
     * Check if the assignment is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->is_active;
    }

    /**
     * Check if the student has checked in.
     */
    public function hasCheckedIn(): bool
    {
        return $this->check_in_date !== null;
    }

    /**
     * Check if the student has checked out.
     */
    public function hasCheckedOut(): bool
    {
        return $this->check_out_date !== null;
    }

    /**
     * Get the duration of stay in days.
     */
    public function getStayDurationAttribute(): int
    {
        if (!$this->check_in_date) {
            return 0;
        }

        $endDate = $this->check_out_date ?? now();
        return $this->check_in_date->diffInDays($endDate);
    }

    /**
     * Check if the assignment is overdue (not paid for current month).
     */
    public function isOverdue(): bool
    {
        // This would need to be implemented based on your payment tracking logic
        return false;
    }
}
