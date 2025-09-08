<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class BookIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_no',
        'book_id',
        'member_id',
        'issued_by',
        'returned_to',
        'issue_date',
        'due_date',
        'return_date',
        'issue_time',
        'return_time',
        'status',
        'issue_notes',
        'return_notes',
        'fine_amount',
        'fine_reason',
        'fine_paid',
        'fine_paid_date',
        'fine_paid_to',
        'remarks',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'issue_time' => 'datetime',
        'return_time' => 'datetime',
        'fine_amount' => 'decimal:2',
        'fine_paid' => 'boolean',
        'fine_paid_date' => 'date',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(LibraryMember::class, 'member_id');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function returnedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    public function finePaidTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fine_paid_to');
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeByBook($query, $bookId)
    {
        return $query->where('book_id', $bookId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'issued')
                    ->where('due_date', '<', now());
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('issue_date', [$startDate, $endDate]);
    }

    public function scopeByIssuedBy($query, $userId)
    {
        return $query->where('issued_by', $userId);
    }

    public function scopeWithFines($query)
    {
        return $query->where('fine_amount', '>', 0);
    }

    public function scopeUnpaidFines($query)
    {
        return $query->where('fine_amount', '>', 0)
                    ->where('fine_paid', false);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'issued' && $this->due_date < now();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) return 0;
        return now()->diffInDays($this->due_date);
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'issued' => $this->is_overdue ? 'danger' : 'warning',
            'returned' => 'success',
            'lost' => 'danger',
            'damaged' => 'warning',
            'reserved' => 'info',
            default => 'secondary'
        };
    }

    public function getFineStatusAttribute(): string
    {
        if ($this->fine_amount == 0) return 'No Fine';
        return $this->fine_paid ? 'Paid' : 'Unpaid';
    }

    public function getFineStatusColorAttribute(): string
    {
        return match($this->fine_status) {
            'No Fine' => 'success',
            'Paid' => 'success',
            'Unpaid' => 'danger',
            default => 'secondary'
        };
    }

    public function getDurationAttribute(): string
    {
        if ($this->status === 'issued') {
            $days = now()->diffInDays($this->issue_date);
            return $days . ' day' . ($days != 1 ? 's' : '');
        }

        if ($this->return_date) {
            $days = $this->return_date->diffInDays($this->issue_date);
            return $days . ' day' . ($days != 1 ? 's' : '');
        }

        return 'N/A';
    }

    public function getIsFineApplicableAttribute(): bool
    {
        return $this->status === 'issued' && $this->is_overdue;
    }

    public function calculateFine(): float
    {
        if (!$this->is_fine_applicable) return 0;

        // Default fine calculation: LRD 1 per day after due date
        $daysOverdue = $this->days_overdue;
        $dailyFine = 1.00; // LRD 1 per day
        
        return $daysOverdue * $dailyFine;
    }

    public function markAsReturned(User $returnedTo, string $notes = null): void
    {
        $this->status = 'returned';
        $this->return_date = now();
        $this->return_time = now();
        $this->returned_to = $returnedTo->id;
        $this->return_notes = $notes;
        
        // Calculate fine if overdue
        if ($this->is_overdue) {
            $this->fine_amount = $this->calculateFine();
            $this->fine_reason = 'Late return - ' . $this->days_overdue . ' day(s) overdue';
        }
        
        $this->save();
        
        // Update book availability
        $this->book->increment('available_copies');
        $this->book->decrement('borrowed_copies');
    }
}
