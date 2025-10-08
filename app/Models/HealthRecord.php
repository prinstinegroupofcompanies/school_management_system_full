<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'recorded_by', 'record_type', 'title', 'description',
        'record_date', 'expiry_date', 'health_provider', 'provider_contact',
        'medical_notes', 'medications', 'allergies', 'chronic_conditions',
        'emergency_instructions', 'vital_signs', 'attachments', 'is_confidential',
        'requires_follow_up', 'follow_up_date', 'follow_up_notes', 'status',
        'notes', 'metadata'
    ];

    protected $casts = [
        'record_date' => 'date',
        'expiry_date' => 'date',
        'follow_up_date' => 'date',
        'vital_signs' => 'array',
        'attachments' => 'array',
        'is_confidential' => 'boolean',
        'requires_follow_up' => 'boolean',
        'metadata' => 'array'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('record_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeConfidential($query)
    {
        return $query->where('is_confidential', true);
    }

    public function scopeRequiresFollowUp($query)
    {
        return $query->where('requires_follow_up', true);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days))
            ->where('status', 'active');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('record_date', [$startDate, $endDate]);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'expired' => 'warning',
            'inactive' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->record_type) {
            'medical_checkup' => 'primary',
            'vaccination' => 'success',
            'allergy' => 'warning',
            'medication' => 'info',
            'emergency' => 'danger',
            default => 'secondary'
        };
    }

    public function getTypeTextAttribute(): string
    {
        return match($this->record_type) {
            'medical_checkup' => 'Medical Checkup',
            'vaccination' => 'Vaccination',
            'allergy' => 'Allergy',
            'medication' => 'Medication',
            'emergency' => 'Emergency',
            default => ucfirst($this->record_type)
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date <= now()->addDays(30);
    }

    public function getNeedsFollowUpAttribute(): bool
    {
        return $this->requires_follow_up && $this->follow_up_date && $this->follow_up_date <= now();
    }

    public function getFormattedRecordDateAttribute(): string
    {
        return $this->record_date->format('M d, Y');
    }

    public function getFormattedExpiryDateAttribute(): string
    {
        return $this->expiry_date ? $this->expiry_date->format('M d, Y') : 'N/A';
    }

    public function getFormattedFollowUpDateAttribute(): string
    {
        return $this->follow_up_date ? $this->follow_up_date->format('M d, Y') : 'N/A';
    }

    // Methods
    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    public function markAsInactive(): void
    {
        $this->update(['status' => 'inactive']);
    }

    public function markAsActive(): void
    {
        $this->update(['status' => 'active']);
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['active', 'inactive']);
    }

    public function canBeDeleted(): bool
    {
        return $this->status === 'inactive';
    }

    public function isConfidential(): bool
    {
        return $this->is_confidential;
    }

    public function requiresFollowUp(): bool
    {
        return $this->requires_follow_up;
    }

    public function getVitalSignsFormatted(): array
    {
        if (!$this->vital_signs) {
            return [];
        }

        $formatted = [];
        foreach ($this->vital_signs as $key => $value) {
            $formatted[ucfirst(str_replace('_', ' ', $key))] = $value;
        }

        return $formatted;
    }
}
