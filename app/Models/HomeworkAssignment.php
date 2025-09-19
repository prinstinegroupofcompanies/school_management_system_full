<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomeworkAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'class_id',
        'teacher_id',
        'assignment_type',
        'instructions',
        'attachments',
        'rubric',
        'assigned_at',
        'due_date',
        'allow_late_submission',
        'late_penalty_percentage',
        'total_points',
        'is_published',
        'is_active',
    ];

    protected $casts = [
        'instructions' => 'array',
        'attachments' => 'array',
        'rubric' => 'array',
        'assigned_at' => 'datetime',
        'due_date' => 'datetime',
        'allow_late_submission' => 'boolean',
        'late_penalty_percentage' => 'decimal:2',
        'is_published' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(HomeworkSubmission::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today());
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('due_date', '>', now());
    }

    // Helper methods
    public function isOverdue(): bool
    {
        return now()->gt($this->due_date);
    }

    public function isDueToday(): bool
    {
        return $this->due_date->isToday();
    }

    public function getDaysUntilDue(): int
    {
        return now()->diffInDays($this->due_date, false);
    }

    public function getFormattedDueDate(): string
    {
        return $this->due_date->format('M d, Y \a\t g:i A');
    }

    public function getStatusBadgeColor(): string
    {
        if (!$this->is_published) return 'gray';
        if ($this->isOverdue()) return 'red';
        if ($this->isDueToday()) return 'orange';
        return 'green';
    }

    public function getStatusText(): string
    {
        if (!$this->is_published) return 'Draft';
        if ($this->isOverdue()) return 'Overdue';
        if ($this->isDueToday()) return 'Due Today';
        return 'Active';
    }

    public function getSubmissionStats(): array
    {
        $totalStudents = Student::where('class_id', $this->class_id)->count();
        $submittedCount = $this->submissions()->count();
        $gradedCount = $this->submissions()->where('status', 'graded')->count();
        $pendingCount = $this->submissions()->where('status', 'submitted')->count();

        return [
            'total_students' => $totalStudents,
            'submitted_count' => $submittedCount,
            'graded_count' => $gradedCount,
            'pending_count' => $pendingCount,
            'submission_rate' => $totalStudents > 0 ? ($submittedCount / $totalStudents) * 100 : 0,
            'grading_progress' => $submittedCount > 0 ? ($gradedCount / $submittedCount) * 100 : 0,
        ];
    }

    public function getAverageScore(): float
    {
        return $this->submissions()
                   ->whereNotNull('score')
                   ->avg('score') ?? 0;
    }

    public function getAveragePercentage(): float
    {
        return $this->submissions()
                   ->whereNotNull('percentage')
                   ->avg('percentage') ?? 0;
    }

    public function hasRubric(): bool
    {
        return !empty($this->rubric);
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    public function getAttachmentCount(): int
    {
        return count($this->attachments ?? []);
    }

    public function canAcceptSubmissions(): bool
    {
        if (!$this->is_published || !$this->is_active) {
            return false;
        }

        if ($this->isOverdue() && !$this->allow_late_submission) {
            return false;
        }

        return true;
    }

    public function getLatePenalty($daysLate): float
    {
        if ($daysLate <= 0 || !$this->allow_late_submission) {
            return 0;
        }

        return min(100, $this->late_penalty_percentage * $daysLate);
    }

    public function publish()
    {
        $this->update([
            'is_published' => true,
            'assigned_at' => now(),
        ]);
    }

    public function unpublish()
    {
        // Only allow if no submissions exist
        if ($this->submissions()->exists()) {
            throw new \Exception('Cannot unpublish assignment with existing submissions.');
        }

        $this->update(['is_published' => false]);
    }
}