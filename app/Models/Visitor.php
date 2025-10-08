<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id', 'first_name', 'last_name', 'email', 'phone', 'id_number',
        'id_type', 'organization', 'position', 'address', 'city', 'state',
        'country', 'postal_code', 'emergency_contact_name', 'emergency_contact_phone',
        'emergency_contact_relationship', 'visitor_type', 'category_id',
        'is_blacklisted', 'blacklist_reason', 'blacklist_date', 'requires_escort',
        'special_instructions', 'attachments', 'metadata'
    ];

    protected $casts = [
        'is_blacklisted' => 'boolean',
        'blacklist_date' => 'date',
        'requires_escort' => 'boolean',
        'attachments' => 'array',
        'metadata' => 'array'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(VisitorCategory::class, 'category_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(VisitorLog::class, 'visitor_id');
    }

    public function recentLogs(): HasMany
    {
        return $this->hasMany(VisitorLog::class, 'visitor_id')
            ->orderBy('check_in_time', 'desc')
            ->limit(5);
    }

    public function activeLogs(): HasMany
    {
        return $this->hasMany(VisitorLog::class, 'visitor_id')
            ->where('status', 'checked_in');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('visitor_type', $type);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBlacklisted($query)
    {
        return $query->where('is_blacklisted', true);
    }

    public function scopeNotBlacklisted($query)
    {
        return $query->where('is_blacklisted', false);
    }

    public function scopeRequiresEscort($query)
    {
        return $query->where('requires_escort', true);
    }

    public function scopeByOrganization($query, $organization)
    {
        return $query->where('organization', 'like', "%{$organization}%");
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email', 'like', "%{$email}%");
    }

    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone', 'like', "%{$phone}%");
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->visitor_type) {
            'parent' => 'primary',
            'guardian' => 'info',
            'vendor' => 'warning',
            'contractor' => 'success',
            'official' => 'danger',
            'guest' => 'secondary',
            'other' => 'dark',
            default => 'secondary'
        };
    }

    public function getTypeTextAttribute(): string
    {
        return match($this->visitor_type) {
            'parent' => 'Parent',
            'guardian' => 'Guardian',
            'vendor' => 'Vendor',
            'contractor' => 'Contractor',
            'official' => 'Official',
            'guest' => 'Guest',
            'other' => 'Other',
            default => ucfirst($this->visitor_type)
        };
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_blacklisted ? 'danger' : 'success';
    }

    public function getStatusTextAttribute(): string
    {
        return $this->is_blacklisted ? 'Blacklisted' : 'Active';
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

    public function getFormattedPhoneAttribute(): string
    {
        if (!$this->phone) {
            return 'N/A';
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $this->phone);

        // Format based on length
        if (strlen($phone) === 10) {
            return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        } elseif (strlen($phone) === 11 && substr($phone, 0, 1) === '1') {
            return '+1 (' . substr($phone, 1, 3) . ') ' . substr($phone, 4, 3) . '-' . substr($phone, 7);
        }

        return $this->phone;
    }

    public function getFormattedEmergencyPhoneAttribute(): string
    {
        if (!$this->emergency_contact_phone) {
            return 'N/A';
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $this->emergency_contact_phone);

        // Format based on length
        if (strlen($phone) === 10) {
            return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        } elseif (strlen($phone) === 11 && substr($phone, 0, 1) === '1') {
            return '+1 (' . substr($phone, 1, 3) . ') ' . substr($phone, 4, 3) . '-' . substr($phone, 7);
        }

        return $this->emergency_contact_phone;
    }

    public function getIsCurrentlyVisitingAttribute(): bool
    {
        return $this->activeLogs()->exists();
    }

    public function getLastVisitDateAttribute(): ?string
    {
        $lastLog = $this->logs()->orderBy('check_in_time', 'desc')->first();
        return $lastLog ? $lastLog->check_in_time->format('M d, Y H:i') : null;
    }

    public function getTotalVisitsAttribute(): int
    {
        return $this->logs()->count();
    }

    public function getTotalVisitsThisMonthAttribute(): int
    {
        return $this->logs()
            ->whereMonth('check_in_time', now()->month)
            ->whereYear('check_in_time', now()->year)
            ->count();
    }

    // Methods
    public function generateVisitorId(): string
    {
        $prefix = 'V';
        $count = static::count() + 1;
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    public function blacklist(string $reason): void
    {
        $this->update([
            'is_blacklisted' => true,
            'blacklist_reason' => $reason,
            'blacklist_date' => now()
        ]);
    }

    public function removeFromBlacklist(): void
    {
        $this->update([
            'is_blacklisted' => false,
            'blacklist_reason' => null,
            'blacklist_date' => null
        ]);
    }

    public function canBeDeleted(): bool
    {
        return $this->logs()->count() === 0;
    }

    public function canBeBlacklisted(): bool
    {
        return !$this->is_blacklisted;
    }

    public function canBeRemovedFromBlacklist(): bool
    {
        return $this->is_blacklisted;
    }

    public function getContactInfo(): array
    {
        return [
            'name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->formatted_phone,
            'organization' => $this->organization,
            'position' => $this->position,
            'address' => $this->full_address,
            'type' => $this->type_text,
            'status' => $this->status_text
        ];
    }

    public function getEmergencyContactInfo(): array
    {
        return [
            'name' => $this->emergency_contact_name,
            'phone' => $this->formatted_emergency_phone,
            'relationship' => $this->emergency_contact_relationship
        ];
    }
}
