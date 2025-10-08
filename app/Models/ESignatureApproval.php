<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ESignatureApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'signature_id', 'approver_id', 'approval_level', 'status', 'approval_notes',
        'rejection_reason', 'approved_at', 'rejected_at', 'delegated_to', 'delegated_at',
        'delegation_notes', 'ip_address', 'user_agent', 'metadata'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'delegated_at' => 'datetime',
        'metadata' => 'array'
    ];

    // Relationships
    public function signature(): BelongsTo
    {
        return $this->belongsTo(ESignature::class, 'signature_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function delegatedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delegated_to');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByApprover($query, $approverId)
    {
        return $query->where('approver_id', $approverId);
    }

    public function scopeByApprovalLevel($query, $level)
    {
        return $query->where('approval_level', $level);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeDelegated($query)
    {
        return $query->where('status', 'delegated');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                    ->where('created_at', '<', now()->subDays(3));
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'delegated' => 'info',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'delegated' => 'Delegated',
            default => 'Unknown'
        };
    }

    public function getApprovalLevelTextAttribute(): string
    {
        return match ($this->approval_level) {
            'first_level' => 'First Level',
            'second_level' => 'Second Level',
            'final_approval' => 'Final Approval',
            default => ucfirst(str_replace('_', ' ', $this->approval_level))
        };
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    public function getIsDelegatedAttribute(): bool
    {
        return $this->status === 'delegated';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->is_pending && $this->created_at->diffInDays(now()) > 3;
    }

    public function getFormattedApprovedAtAttribute(): string
    {
        return $this->approved_at ? $this->approved_at->format('M d, Y H:i') : 'Not approved';
    }

    public function getFormattedRejectedAtAttribute(): string
    {
        return $this->rejected_at ? $this->rejected_at->format('M d, Y H:i') : 'Not rejected';
    }

    public function getFormattedDelegatedAtAttribute(): string
    {
        return $this->delegated_at ? $this->delegated_at->format('M d, Y H:i') : 'Not delegated';
    }

    public function getApproverNameAttribute(): string
    {
        return $this->approver ? $this->approver->name : 'Unknown';
    }

    public function getDelegatedToNameAttribute(): string
    {
        return $this->delegatedTo ? $this->delegatedTo->name : 'Not delegated';
    }

    public function getDaysPendingAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getCanBeApprovedAttribute(): bool
    {
        return $this->status === 'pending' && !$this->is_overdue;
    }

    public function getCanBeRejectedAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getCanBeDelegatedAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getCanBeEditedAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getCanBeDeletedAttribute(): bool
    {
        return $this->status === 'pending';
    }

    // Methods
    public function approve(string $notes = null, string $ipAddress = null, string $userAgent = null): bool
    {
        $this->update([
            'status' => 'approved',
            'approval_notes' => $notes,
            'approved_at' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);

        return true;
    }

    public function reject(string $reason, string $ipAddress = null, string $userAgent = null): bool
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);

        return true;
    }

    public function delegate(int $delegatedToId, string $notes = null, string $ipAddress = null, string $userAgent = null): bool
    {
        $this->update([
            'status' => 'delegated',
            'delegated_to' => $delegatedToId,
            'delegation_notes' => $notes,
            'delegated_at' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);

        return true;
    }

    public function getApprovalSummary(): array
    {
        return [
            'id' => $this->id,
            'approver' => $this->approver_name,
            'level' => $this->approval_level_text,
            'status' => $this->status_text,
            'status_color' => $this->status_color,
            'created_at' => $this->created_at->format('M d, Y H:i'),
            'days_pending' => $this->days_pending,
            'is_overdue' => $this->is_overdue,
            'notes' => $this->approval_notes,
            'rejection_reason' => $this->rejection_reason,
            'delegated_to' => $this->delegated_to_name,
            'delegation_notes' => $this->delegation_notes
        ];
    }

    public function getTimelineEntry(): array
    {
        $entry = [
            'id' => $this->id,
            'type' => 'approval',
            'title' => "Approval Request - {$this->approval_level_text}",
            'description' => "Approval requested from {$this->approver_name}",
            'status' => $this->status,
            'status_color' => $this->status_color,
            'created_at' => $this->created_at,
            'user' => $this->approver_name
        ];

        if ($this->is_approved) {
            $entry['description'] = "Approved by {$this->approver_name}";
            $entry['timestamp'] = $this->approved_at;
        } elseif ($this->is_rejected) {
            $entry['description'] = "Rejected by {$this->approver_name}";
            $entry['timestamp'] = $this->rejected_at;
        } elseif ($this->is_delegated) {
            $entry['description'] = "Delegated to {$this->delegated_to_name}";
            $entry['timestamp'] = $this->delegated_at;
        }

        return $entry;
    }

    public function getNotificationData(): array
    {
        return [
            'approval_id' => $this->id,
            'signature_id' => $this->signature_id,
            'approver_id' => $this->approver_id,
            'approval_level' => $this->approval_level,
            'status' => $this->status,
            'signature_title' => $this->signature->document_title ?? 'Unknown Document',
            'approver_name' => $this->approver_name,
            'created_at' => $this->created_at,
            'is_overdue' => $this->is_overdue,
            'days_pending' => $this->days_pending
        ];
    }

    public function getAuditData(): array
    {
        return [
            'approval_id' => $this->id,
            'signature_id' => $this->signature_id,
            'approver_id' => $this->approver_id,
            'approval_level' => $this->approval_level,
            'status' => $this->status,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at,
            'delegated_at' => $this->delegated_at,
            'delegated_to' => $this->delegated_to,
            'approval_notes' => $this->approval_notes,
            'rejection_reason' => $this->rejection_reason,
            'delegation_notes' => $this->delegation_notes
        ];
    }
}