<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class LibraryMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'user_id',
        'member_type',
        'card_number',
        'issue_date',
        'expiry_date',
        'max_books_allowed',
        'current_books_borrowed',
        'fine_balance',
        'status',
        'suspension_reason',
        'suspension_start_date',
        'suspension_end_date',
        'remarks',
        'is_active',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'max_books_allowed' => 'integer',
        'current_books_borrowed' => 'integer',
        'fine_balance' => 'decimal:2',
        'suspension_start_date' => 'date',
        'suspension_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'member_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByMemberType($query, $memberType)
    {
        return $query->where('member_type', $memberType);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now());
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeWithFines($query)
    {
        return $query->where('fine_balance', '>', 0);
    }

    public function scopeByCardNumber($query, $cardNumber)
    {
        return $query->where('card_number', $cardNumber);
    }

    public function getMemberTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->member_type));
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'danger',
            'expired' => 'warning',
            'pending' => 'info',
            default => 'secondary'
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date < now();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_date <= now()->addDays(30) && $this->expiry_date > now();
    }

    public function getIsSuspendedAttribute(): bool
    {
        return $this->status === 'suspended';
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        if ($this->is_expired) return 0;
        return now()->diffInDays($this->expiry_date, false);
    }

    public function getCanBorrowAttribute(): bool
    {
        return $this->is_active && 
               !$this->is_expired && 
               !$this->is_suspended && 
               $this->current_books_borrowed < $this->max_books_allowed &&
               $this->fine_balance == 0;
    }

    public function getAvailableBooksAttribute(): int
    {
        return max(0, $this->max_books_allowed - $this->current_books_borrowed);
    }

    public function getMembershipDurationAttribute(): string
    {
        $days = $this->issue_date->diffInDays($this->expiry_date);
        $years = intval($days / 365);
        $remainingDays = $days % 365;
        
        if ($years > 0 && $remainingDays > 0) {
            return "{$years} year(s) {$remainingDays} day(s)";
        } elseif ($years > 0) {
            return "{$years} year(s)";
        } else {
            return "{$days} day(s)";
        }
    }

    public function getActiveIssuesAttribute(): int
    {
        return $this->bookIssues()
            ->where('status', 'issued')
            ->count();
    }

    public function getOverdueIssuesAttribute(): int
    {
        return $this->bookIssues()
            ->where('status', 'issued')
            ->where('due_date', '<', now())
            ->count();
    }

    public function getTotalFinesAttribute(): float
    {
        return $this->bookIssues()
            ->where('fine_amount', '>', 0)
            ->sum('fine_amount');
    }

    public function getUnpaidFinesAttribute(): float
    {
        return $this->bookIssues()
            ->where('fine_amount', '>', 0)
            ->where('fine_paid', false)
            ->sum('fine_amount');
    }

    public function canBorrowBook(): bool
    {
        return $this->can_borrow && $this->available_books > 0;
    }

    public function incrementBorrowedBooks(): void
    {
        $this->increment('current_books_borrowed');
    }

    public function decrementBorrowedBooks(): void
    {
        $this->decrement('current_books_borrowed');
    }

    public function addFine(float $amount): void
    {
        $this->increment('fine_balance', $amount);
    }

    public function payFine(float $amount): void
    {
        $this->decrement('fine_balance', $amount);
    }

    public function suspend(string $reason, int $days = 30): void
    {
        $this->status = 'suspended';
        $this->suspension_reason = $reason;
        $this->suspension_start_date = now();
        $this->suspension_end_date = now()->addDays($days);
        $this->save();
    }

    public function unsuspend(): void
    {
        $this->status = 'active';
        $this->suspension_reason = null;
        $this->suspension_start_date = null;
        $this->suspension_end_date = null;
        $this->save();
    }
}
