<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ESignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'signature_id', 'user_id', 'document_type', 'document_id', 'signature_type',
        'signature_data', 'signature_hash', 'ip_address', 'user_agent', 'device_fingerprint',
        'location', 'status', 'signed_at', 'expires_at', 'verified_at', 'verification_notes',
        'revocation_reason', 'revoked_at', 'metadata'
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'revoked_at' => 'datetime',
        'metadata' => 'array'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ESignatureApproval::class, 'signature_id');
    }

    public function pendingApprovals(): HasMany
    {
        return $this->hasMany(ESignatureApproval::class, 'signature_id')->where('status', 'pending');
    }

    public function approvedApprovals(): HasMany
    {
        return $this->hasMany(ESignatureApproval::class, 'signature_id')->where('status', 'approved');
    }

    // Scopes
    public function scopeByDocumentType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('signed_at', [$startDate, $endDate]);
    }

    public function scopeBySignatureType($query, $type)
    {
        return $query->where('signature_type', $type);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'signed' => 'info',
            'verified' => 'success',
            'expired' => 'danger',
            'revoked' => 'dark',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'signed' => 'Signed',
            'verified' => 'Verified',
            'expired' => 'Expired',
            'revoked' => 'Revoked',
            default => 'Unknown'
        };
    }

    public function getSignatureTypeTextAttribute(): string
    {
        return match ($this->signature_type) {
            'digital' => 'Digital Signature',
            'biometric' => 'Biometric Signature',
            'pin' => 'PIN Signature',
            'password' => 'Password Signature',
            default => 'Unknown'
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsSignedAttribute(): bool
    {
        return $this->status === 'signed';
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->status === 'verified';
    }

    public function getIsRevokedAttribute(): bool
    {
        return $this->status === 'revoked';
    }

    public function getFormattedSignedAtAttribute(): string
    {
        return $this->signed_at ? $this->signed_at->format('M d, Y H:i') : 'Not signed';
    }

    public function getFormattedExpiresAtAttribute(): string
    {
        return $this->expires_at ? $this->expires_at->format('M d, Y H:i') : 'No expiry';
    }

    public function getFormattedVerifiedAtAttribute(): string
    {
        return $this->verified_at ? $this->verified_at->format('M d, Y H:i') : 'Not verified';
    }

    public function getFormattedRevokedAtAttribute(): string
    {
        return $this->revoked_at ? $this->revoked_at->format('M d, Y H:i') : 'Not revoked';
    }

    public function getDocumentTitleAttribute(): string
    {
        return match ($this->document_type) {
            'lesson_plan' => 'Lesson Plan',
            'grade_submission' => 'Grade Submission',
            'monthly_report' => 'Monthly Report',
            'transcript' => 'Transcript',
            'admission_application' => 'Admission Application',
            default => ucfirst(str_replace('_', ' ', $this->document_type))
        };
    }

    public function getRequiresApprovalAttribute(): bool
    {
        return $this->approvals()->where('status', 'pending')->exists();
    }

    public function getApprovalStatusAttribute(): string
    {
        $pendingCount = $this->approvals()->where('status', 'pending')->count();
        $approvedCount = $this->approvals()->where('status', 'approved')->count();
        $rejectedCount = $this->approvals()->where('status', 'rejected')->count();

        if ($rejectedCount > 0) {
            return 'rejected';
        } elseif ($pendingCount > 0) {
            return 'pending';
        } elseif ($approvedCount > 0) {
            return 'approved';
        }

        return 'no_approval_required';
    }

    // Methods
    public function generateSignatureId(): string
    {
        return 'SIG-' . strtoupper(uniqid());
    }

    public function sign(array $signatureData, string $ipAddress = null, string $userAgent = null): bool
    {
        $this->update([
            'signature_data' => encrypt(json_encode($signatureData)),
            'signature_hash' => hash('sha256', json_encode($signatureData) . $this->id),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'status' => 'signed',
            'signed_at' => now()
        ]);

        return true;
    }

    public function verify(string $verificationNotes = null): bool
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verification_notes' => $verificationNotes
        ]);

        return true;
    }

    public function revoke(string $reason): bool
    {
        $this->update([
            'status' => 'revoked',
            'revocation_reason' => $reason,
            'revoked_at' => now()
        ]);

        return true;
    }

    public function expire(): bool
    {
        $this->update([
            'status' => 'expired'
        ]);

        return true;
    }

    public function canBeSigned(): bool
    {
        return $this->status === 'pending' && !$this->is_expired;
    }

    public function canBeVerified(): bool
    {
        return $this->status === 'signed' && !$this->is_expired;
    }

    public function canBeRevoked(): bool
    {
        return in_array($this->status, ['signed', 'verified']) && !$this->is_expired;
    }

    public function canBeDeleted(): bool
    {
        return $this->status === 'pending' || $this->is_expired;
    }

    public function getSignatureDataDecrypted(): array
    {
        if (!$this->signature_data) {
            return [];
        }

        try {
            return json_decode(decrypt($this->signature_data), true) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function validateSignature(): bool
    {
        if (!$this->signature_data || !$this->signature_hash) {
            return false;
        }

        $decryptedData = $this->getSignatureDataDecrypted();
        $expectedHash = hash('sha256', json_encode($decryptedData) . $this->id);

        return hash_equals($this->signature_hash, $expectedHash);
    }

    public function getDocumentUrl(): string
    {
        return match ($this->document_type) {
            'lesson_plan' => route('admin.lesson-plans.show', $this->document_id),
            'grade_submission' => route('admin.grades.show', $this->document_id),
            'monthly_report' => route('admin.monthly-reports.show', $this->document_id),
            'transcript' => route('admin.transcripts.show', $this->document_id),
            'admission_application' => route('admin.admissions.show', $this->document_id),
            default => '#'
        };
    }

    public function getApprovalProgress(): array
    {
        $totalApprovals = $this->approvals()->count();
        $approvedCount = $this->approvals()->where('status', 'approved')->count();
        $rejectedCount = $this->approvals()->where('status', 'rejected')->count();

        return [
            'total' => $totalApprovals,
            'approved' => $approvedCount,
            'rejected' => $rejectedCount,
            'pending' => $totalApprovals - $approvedCount - $rejectedCount,
            'percentage' => $totalApprovals > 0 ? round(($approvedCount / $totalApprovals) * 100, 2) : 0
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($signature) {
            if (!$signature->signature_id) {
                $signature->signature_id = $signature->generateSignatureId();
            }
        });
    }
}