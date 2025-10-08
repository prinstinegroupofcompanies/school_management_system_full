<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HealthIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_number', 'student_id', 'staff_id', 'reported_by', 'incident_type',
        'severity', 'location', 'description', 'symptoms', 'actions_taken',
        'medical_treatment', 'follow_up_required', 'status', 'incident_date',
        'reported_date', 'resolved_date', 'investigation_notes', 'prevention_measures',
        'witnesses', 'attachments', 'parent_notified', 'authorities_notified',
        'notes', 'metadata'
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'reported_date' => 'datetime',
        'resolved_date' => 'datetime',
        'witnesses' => 'array',
        'attachments' => 'array',
        'parent_notified' => 'boolean',
        'authorities_notified' => 'boolean',
        'metadata' => 'array'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('incident_type', $type);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('incident_date', [$startDate, $endDate]);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeMajor($query)
    {
        return $query->where('severity', 'major');
    }

    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['reported', 'investigating']);
    }

    // Accessors
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'minor' => 'success',
            'moderate' => 'warning',
            'major' => 'danger',
            'critical' => 'dark',
            default => 'secondary'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'reported' => 'warning',
            'investigating' => 'info',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getSeverityTextAttribute(): string
    {
        return ucfirst($this->severity);
    }

    public function getIncidentPersonAttribute(): string
    {
        if ($this->student) {
            return $this->student->first_name . ' ' . $this->student->last_name;
        } elseif ($this->staff) {
            return $this->staff->first_name . ' ' . $this->staff->last_name;
        }
        return 'Unknown';
    }

    public function getIsResolvedAttribute(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function getIsCriticalAttribute(): bool
    {
        return $this->severity === 'critical';
    }

    public function getIsMajorAttribute(): bool
    {
        return $this->severity === 'major';
    }

    public function getRequiresImmediateAttentionAttribute(): bool
    {
        return in_array($this->severity, ['major', 'critical']) && !$this->is_resolved;
    }

    // Methods
    public function generateIncidentNumber(): string
    {
        $prefix = 'HIN';
        $count = static::count() + 1;
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    public function markAsInvestigating(): void
    {
        $this->update(['status' => 'investigating']);
    }

    public function markAsResolved(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_date' => now()
        ]);
    }

    public function markAsClosed(): void
    {
        $this->update(['status' => 'closed']);
    }

    public function notifyParent(): void
    {
        $this->update(['parent_notified' => true]);
    }

    public function notifyAuthorities(): void
    {
        $this->update(['authorities_notified' => true]);
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['reported', 'investigating']);
    }

    public function canBeResolved(): bool
    {
        return in_array($this->status, ['reported', 'investigating']);
    }

    public function canBeClosed(): bool
    {
        return $this->status === 'resolved';
    }
}
