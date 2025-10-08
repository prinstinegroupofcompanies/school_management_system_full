<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmergencyContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'organization', 'contact_type', 'phone_primary', 'phone_secondary',
        'email', 'address', 'city', 'state', 'country', 'postal_code',
        'services_provided', 'specialization', 'availability', 'notes',
        'is_active', 'priority', 'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'metadata' => 'array'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('contact_type', $type);
    }

    public function scopeByOrganization($query, $organization)
    {
        return $query->where('organization', 'like', "%{$organization}%");
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 4);
    }

    public function scopeMedical($query)
    {
        return $query->where('contact_type', 'medical');
    }

    public function scopePolice($query)
    {
        return $query->where('contact_type', 'police');
    }

    public function scopeFire($query)
    {
        return $query->where('contact_type', 'fire');
    }

    public function scopeAmbulance($query)
    {
        return $query->where('contact_type', 'ambulance');
    }

    // Accessors
    public function getTypeColorAttribute(): string
    {
        return match($this->contact_type) {
            'medical' => 'success',
            'police' => 'primary',
            'fire' => 'danger',
            'ambulance' => 'warning',
            'emergency' => 'dark',
            default => 'secondary'
        };
    }

    public function getTypeTextAttribute(): string
    {
        return match($this->contact_type) {
            'medical' => 'Medical',
            'police' => 'Police',
            'fire' => 'Fire Department',
            'ambulance' => 'Ambulance',
            'emergency' => 'Emergency',
            default => ucfirst($this->contact_type)
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            5 => 'danger',
            4 => 'warning',
            3 => 'info',
            2 => 'primary',
            1 => 'secondary',
            default => 'secondary'
        };
    }

    public function getPriorityTextAttribute(): string
    {
        return match($this->priority) {
            5 => 'Critical',
            4 => 'High',
            3 => 'Medium',
            2 => 'Low',
            1 => 'Minimal',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    public function getFormattedPhonePrimaryAttribute(): string
    {
        return $this->formatPhoneNumber($this->phone_primary);
    }

    public function getFormattedPhoneSecondaryAttribute(): string
    {
        return $this->phone_secondary ? $this->formatPhoneNumber($this->phone_secondary) : 'N/A';
    }

    public function getIsHighPriorityAttribute(): bool
    {
        return $this->priority >= 4;
    }

    public function getIsCriticalAttribute(): bool
    {
        return $this->priority === 5;
    }

    // Methods
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function setPriority($priority): void
    {
        $this->update(['priority' => max(1, min(5, $priority))]);
    }

    public function canBeDeleted(): bool
    {
        return !$this->is_active;
    }

    public function getContactInfo(): array
    {
        return [
            'name' => $this->name,
            'organization' => $this->organization,
            'type' => $this->type_text,
            'phone_primary' => $this->formatted_phone_primary,
            'phone_secondary' => $this->formatted_phone_secondary,
            'email' => $this->email,
            'address' => $this->full_address,
            'priority' => $this->priority_text,
            'is_active' => $this->is_active
        ];
    }

    private function formatPhoneNumber($phone): string
    {
        if (!$phone) {
            return 'N/A';
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Format based on length
        if (strlen($phone) === 10) {
            return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        } elseif (strlen($phone) === 11 && substr($phone, 0, 1) === '1') {
            return '+1 (' . substr($phone, 1, 3) . ') ' . substr($phone, 4, 3) . '-' . substr($phone, 7);
        }

        return $phone;
    }
}
