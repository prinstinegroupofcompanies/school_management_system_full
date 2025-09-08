<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ScholarshipApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'scholarship_id', 'student_id', 'application_number', 'application_date',
        'status', 'submitted_at', 'reviewed_at', 'reviewed_by', 'approved_at',
        'approved_by', 'rejected_at', 'rejected_by', 'rejection_reason',
        'documents_submitted', 'documents_verified', 'documents_verified_at',
        'documents_verified_by', 'interview_scheduled', 'interview_date',
        'interview_time', 'interview_location', 'interview_notes',
        'interview_score', 'final_score', 'final_decision', 'notes',
        'is_active'
    ];

    protected $casts = [
        'application_date' => 'date', 'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime', 'approved_at' => 'datetime',
        'rejected_at' => 'datetime', 'documents_verified_at' => 'datetime',
        'interview_scheduled' => 'boolean', 'interview_date' => 'date',
        'interview_time' => 'datetime', 'interview_score' => 'decimal:2',
        'final_score' => 'decimal:2', 'is_active' => 'boolean',
        'documents_submitted' => 'array', 'documents_verified' => 'array'
    ];

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function documentsVerifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'documents_verified_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByScholarship($query, $scholarshipId)
    {
        return $query->where('scholarship_id', $scholarshipId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByApplicationNumber($query, $applicationNumber)
    {
        return $query->where('application_number', $applicationNumber);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('application_date', [$startDate, $endDate]);
    }

    public function scopeBySubmittedDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('submitted_at', [$startDate, $endDate]);
    }

    public function scopeByReviewDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('reviewed_at', [$startDate, $endDate]);
    }

    public function scopeByApprovalDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('approved_at', [$startDate, $endDate]);
    }

    public function scopeByRejectionDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('rejected_at', [$startDate, $endDate]);
    }

    public function scopeByInterviewDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('interview_date', [$startDate, $endDate]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeInterviewScheduled($query)
    {
        return $query->where('interview_scheduled', true);
    }

    public function scopeInterviewCompleted($query)
    {
        return $query->where('status', 'interview_completed');
    }

    public function scopeDocumentsVerified($query)
    {
        return $query->where('documents_verified', true);
    }

    public function scopeDocumentsPending($query)
    {
        return $query->where('documents_verified', false);
    }

    public function scopeByScoreRange($query, $minScore, $maxScore)
    {
        return $query->whereBetween('final_score', [$minScore, $maxScore]);
    }

    public function scopeByInterviewScoreRange($query, $minScore, $maxScore)
    {
        return $query->whereBetween('interview_score', [$minScore, $maxScore]);
    }

    public function scopeByFinalDecision($query, $decision)
    {
        return $query->where('final_decision', $decision);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('application_date', 'desc');
    }

    public function scopeChronological($query)
    {
        return $query->orderBy('application_date', 'asc');
    }

    public function scopeByScore($query)
    {
        return $query->orderBy('final_score', 'desc');
    }

    public function scopeByInterviewScore($query)
    {
        return $query->orderBy('interview_score', 'desc');
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'under_review' => 'info',
            'documents_pending' => 'warning',
            'documents_verified' => 'success',
            'interview_scheduled' => 'primary',
            'interview_completed' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'waitlisted' => 'secondary',
            'withdrawn' => 'dark',
            default => 'secondary'
        };
    }

    public function getApplicationDateDisplayAttribute(): string
    {
        return $this->application_date->format('M d, Y');
    }

    public function getSubmittedAtDisplayAttribute(): string
    {
        if (!$this->submitted_at) return 'Not submitted yet';
        return $this->submitted_at->format('M d, Y \a\t H:i');
    }

    public function getReviewedAtDisplayAttribute(): string
    {
        if (!$this->reviewed_at) return 'Not reviewed yet';
        return $this->reviewed_at->format('M d, Y \a\t H:i');
    }

    public function getApprovedAtDisplayAttribute(): string
    {
        if (!$this->approved_at) return 'Not approved yet';
        return $this->approved_at->format('M d, Y \a\t H:i');
    }

    public function getRejectedAtDisplayAttribute(): string
    {
        if (!$this->rejected_at) return 'Not rejected';
        return $this->rejected_at->format('M d, Y \a\t H:i');
    }

    public function getDocumentsVerifiedAtDisplayAttribute(): string
    {
        if (!$this->documents_verified_at) return 'Not verified yet';
        return $this->documents_verified_at->format('M d, Y \a\t H:i');
    }

    public function getInterviewDateDisplayAttribute(): string
    {
        if (!$this->interview_date) return 'No interview scheduled';
        return $this->interview_date->format('M d, Y');
    }

    public function getInterviewTimeDisplayAttribute(): string
    {
        if (!$this->interview_time) return 'No interview scheduled';
        return $this->interview_time->format('H:i');
    }

    public function getInterviewDateTimeDisplayAttribute(): string
    {
        if (!$this->interview_date) return 'No interview scheduled';
        
        $display = $this->interview_date->format('M d, Y');
        
        if ($this->interview_time) {
            $display .= ' at ' . $this->interview_time->format('H:i');
        }
        
        return $display;
    }

    public function getInterviewScoreDisplayAttribute(): string
    {
        if (!$this->interview_score) return 'Not scored yet';
        return number_format($this->interview_score, 1) . '/10';
    }

    public function getFinalScoreDisplayAttribute(): string
    {
        if (!$this->final_score) return 'Not calculated yet';
        return number_format($this->final_score, 1) . '/100';
    }

    public function getFinalDecisionDisplayAttribute(): string
    {
        if (!$this->final_decision) return 'Not decided yet';
        return ucwords(str_replace('_', ' ', $this->final_decision));
    }

    public function getFinalDecisionColorAttribute(): string
    {
        return match($this->final_decision) {
            'approved' => 'success',
            'rejected' => 'danger',
            'waitlisted' => 'warning',
            'pending' => 'info',
            default => 'secondary'
        };
    }

    public function getDocumentsSubmittedDisplayAttribute(): string
    {
        if (!$this->documents_submitted || empty($this->documents_submitted)) {
            return 'No documents submitted';
        }
        return implode(', ', $this->documents_submitted);
    }

    public function getDocumentsVerifiedDisplayAttribute(): string
    {
        if (!$this->documents_verified || empty($this->documents_verified)) {
            return 'No documents verified';
        }
        return implode(', ', $this->documents_verified);
    }

    public function getRejectionReasonDisplayAttribute(): string
    {
        return $this->rejection_reason ?: 'No reason provided';
    }

    public function getInterviewNotesDisplayAttribute(): string
    {
        return $this->interview_notes ?: 'No interview notes';
    }

    public function getNotesDisplayAttribute(): string
    {
        return $this->notes ?: 'No additional notes';
    }

    public function getIsSubmittedAttribute(): bool
    {
        return $this->submitted_at !== null;
    }

    public function getIsUnderReviewAttribute(): bool
    {
        return $this->status === 'under_review';
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    public function getIsWaitlistedAttribute(): bool
    {
        return $this->status === 'waitlisted';
    }

    public function getIsWithdrawnAttribute(): bool
    {
        return $this->status === 'withdrawn';
    }

    public function getIsInterviewScheduledAttribute(): bool
    {
        return $this->interview_scheduled && $this->interview_date;
    }

    public function getIsInterviewCompletedAttribute(): bool
    {
        return $this->status === 'interview_completed';
    }

    public function getIsDocumentsVerifiedAttribute(): bool
    {
        return $this->documents_verified && !empty($this->documents_verified);
    }

    public function getIsDocumentsPendingAttribute(): bool
    {
        return !$this->is_documents_verified;
    }

    public function getDaysSinceApplicationAttribute(): int
    {
        return now()->diffInDays($this->application_date);
    }

    public function getDaysSinceSubmissionAttribute(): int
    {
        if (!$this->submitted_at) return 0;
        return now()->diffInDays($this->submitted_at);
    }

    public function getDaysSinceReviewAttribute(): int
    {
        if (!$this->reviewed_at) return 0;
        return now()->diffInDays($this->reviewed_at);
    }

    public function getDaysUntilInterviewAttribute(): int
    {
        if (!$this->interview_date) return 0;
        
        if ($this->interview_date < now()) return 0;
        
        return now()->diffInDays($this->interview_date, false);
    }

    public function getIsInterviewUpcomingAttribute(): bool
    {
        return $this->interview_date && $this->interview_date > now();
    }

    public function getIsInterviewOverdueAttribute(): bool
    {
        return $this->interview_date && $this->interview_date < now() && !$this->is_interview_completed;
    }

    public function getApplicationSummaryAttribute(): string
    {
        $summary = $this->application_number . ' - ' . $this->student->name;
        
        $summary .= ' (' . $this->scholarship->name . ')';
        
        $summary .= ' - ' . $this->status_display;
        
        if ($this->is_interview_scheduled) {
            $summary .= ' - Interview: ' . $this->interview_date_display;
        }
        
        if ($this->final_score) {
            $summary .= ' - Score: ' . $this->final_score_display;
        }
        
        return $summary;
    }

    public function getReviewerDisplayAttribute(): string
    {
        if (!$this->reviewed_by) return 'Not reviewed yet';
        return $this->reviewedBy->name ?? 'Unknown';
    }

    public function getApproverDisplayAttribute(): string
    {
        if (!$this->approved_by) return 'Not approved yet';
        return $this->approvedBy->name ?? 'Unknown';
    }

    public function getRejectorDisplayAttribute(): string
    {
        if (!$this->rejected_by) return 'Not rejected';
        return $this->rejectedBy->name ?? 'Unknown';
    }

    public function getDocumentsVerifierDisplayAttribute(): string
    {
        if (!$this->documents_verified_by) return 'Not verified yet';
        return $this->documentsVerifiedBy->name ?? 'Unknown';
    }

    public function getInterviewLocationDisplayAttribute(): string
    {
        return $this->interview_location ?: 'Location not specified';
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === 'pending' && !$this->is_submitted;
    }

    public function canBeReviewed(): bool
    {
        return $this->is_submitted && $this->status === 'pending';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'under_review' && $this->is_documents_verified;
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, ['pending', 'under_review']);
    }

    public function canBeWaitlisted(): bool
    {
        return in_array($this->status, ['pending', 'under_review']);
    }

    public function canBeWithdrawn(): bool
    {
        return !in_array($this->status, ['approved', 'rejected', 'withdrawn']);
    }

    public function canScheduleInterview(): bool
    {
        return $this->status === 'under_review' && !$this->is_interview_scheduled;
    }

    public function canCompleteInterview(): bool
    {
        return $this->is_interview_scheduled && $this->interview_date < now();
    }

    public function canVerifyDocuments(): bool
    {
        return $this->is_submitted && !$this->is_documents_verified;
    }

    public function submit(): void
    {
        $this->status = 'pending';
        $this->submitted_at = now();
        $this->save();
    }

    public function markAsUnderReview(User $reviewer): void
    {
        $this->status = 'under_review';
        $this->reviewed_by = $reviewer->id;
        $this->reviewed_at = now();
        $this->save();
    }

    public function approve(User $approver, string $notes = null): void
    {
        $this->status = 'approved';
        $this->approved_by = $approver->id;
        $this->approved_at = now();
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Approved: ' . $notes;
        }
        
        $this->save();
    }

    public function reject(User $rejector, string $reason, string $notes = null): void
    {
        $this->status = 'rejected';
        $this->rejected_by = $rejector->id;
        $this->rejected_at = now();
        $this->rejection_reason = $reason;
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Rejected: ' . $notes;
        }
        
        $this->save();
    }

    public function waitlist(User $reviewer, string $notes = null): void
    {
        $this->status = 'waitlisted';
        $this->reviewed_by = $reviewer->id;
        $this->reviewed_at = now();
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Waitlisted: ' . $notes;
        }
        
        $this->save();
    }

    public function withdraw(string $notes = null): void
    {
        $this->status = 'withdrawn';
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'Withdrawn: ' . $notes;
        }
        
        $this->save();
    }

    public function scheduleInterview(string $date, string $time, string $location, string $notes = null): void
    {
        $this->interview_scheduled = true;
        $this->interview_date = $date;
        $this->interview_time = $time;
        $this->interview_location = $location;
        
        if ($notes) {
            $this->interview_notes = ($this->interview_notes ? $this->interview_notes . "\n" : '') . 'Scheduled: ' . $notes;
        }
        
        $this->save();
    }

    public function completeInterview(float $score, string $notes = null): void
    {
        $this->status = 'interview_completed';
        $this->interview_score = $score;
        
        if ($notes) {
            $this->interview_notes = ($this->interview_notes ? $this->interview_notes . "\n" : '') . 'Completed: ' . $notes;
        }
        
        $this->save();
    }

    public function verifyDocuments(User $verifier, array $verifiedDocuments): void
    {
        $this->documents_verified = $verifiedDocuments;
        $this->documents_verified_by = $verifier->id;
        $this->documents_verified_at = now();
        $this->save();
    }

    public function calculateFinalScore(): float
    {
        $score = 0;
        
        // Academic performance (40%)
        $studentGpa = $this->student->getGpa() ?? 0;
        $score += ($studentGpa / 4.0) * 40;
        
        // Attendance (20%)
        $studentAttendance = $this->student->getAttendanceRate() ?? 0;
        $score += ($studentAttendance / 100) * 20;
        
        // Interview score (30%)
        if ($this->interview_score) {
            $score += ($this->interview_score / 10) * 30;
        }
        
        // Documents completeness (10%)
        if ($this->is_documents_verified) {
            $score += 10;
        }
        
        $this->final_score = round($score, 2);
        $this->save();
        
        return $this->final_score;
    }

    public function getApplicationStatistics(): array
    {
        return [
            'application_date' => $this->application_date,
            'submitted_at' => $this->submitted_at,
            'reviewed_at' => $this->reviewed_at,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at,
            'documents_verified_at' => $this->documents_verified_at,
            'interview_date' => $this->interview_date,
            'interview_time' => $this->interview_time,
            'interview_score' => $this->interview_score,
            'final_score' => $this->final_score,
            'final_decision' => $this->final_decision,
            'is_submitted' => $this->is_submitted,
            'is_under_review' => $this->is_under_review,
            'is_approved' => $this->is_approved,
            'is_rejected' => $this->is_rejected,
            'is_waitlisted' => $this->is_waitlisted,
            'is_withdrawn' => $this->is_withdrawn,
            'is_interview_scheduled' => $this->is_interview_scheduled,
            'is_interview_completed' => $this->is_interview_completed,
            'is_documents_verified' => $this->is_documents_verified,
            'is_documents_pending' => $this->is_documents_pending,
            'days_since_application' => $this->days_since_application,
            'days_since_submission' => $this->days_since_submission,
            'days_since_review' => $this->days_since_review,
            'days_until_interview' => $this->days_until_interview,
            'is_interview_upcoming' => $this->is_interview_upcoming,
            'is_interview_overdue' => $this->is_interview_overdue
        ];
    }
}
