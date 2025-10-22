<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'vehicle_id',
        'route_id',
        'pickup_location',
        'drop_location',
        'pickup_time',
        'drop_time',
        'monthly_fee',
        'assigned_date',
        'status',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'monthly_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the student that owns this transport assignment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the vehicle assigned to this student.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(TransportVehicle::class);
    }

    /**
     * Get the route assigned to this student.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class);
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
     * Scope to get assignments for a specific vehicle.
     */
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Scope to get assignments for a specific route.
     */
    public function scopeForRoute($query, $routeId)
    {
        return $query->where('route_id', $routeId);
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
     * Check if the assignment is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->is_active;
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
