<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class OnlineExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'online_exam_id', 'attempt_number', 'started_at',
        'submitted_at', 'time_expired_at', 'time_taken_minutes',
        'questions_attempted', 'questions_answered', 'questions_skipped',
        'marks_obtained', 'total_marks', 'percentage', 'grade',
        'grade_point', 'status', 'is_passed', 'student_answers',
        'correct_answers', 'feedback', 'teacher_comments', 'is_reviewed',
        'reviewed_by', 'reviewed_at'
    ];

    protected $casts = [
        'started_at' => 'datetime', 'submitted_at' => 'datetime',
        'time_expired_at' => 'datetime', 'time_taken_minutes' => 'integer',
        'questions_attempted' => 'integer', 'questions_answered' => 'integer',
        'questions_skipped' => 'integer', 'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2', 'percentage' => 'decimal:2',
        'grade_point' => 'decimal:2', 'student_answers' => 'array',
        'correct_answers' => 'array', 'is_passed' => 'boolean',
        'is_reviewed' => 'boolean', 'reviewed_at' => 'datetime'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function onlineExam(): BelongsTo
    {
        return $this->belongsTo(OnlineExam::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByExam($query, $examId)
    {
        return $query->where('online_exam_id', $examId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('is_passed', false);
    }

    public function scopeReviewed($query)
    {
        return $query->where('is_reviewed', true);
    }

    public function scopeUnreviewed($query)
    {
        return $query->where('is_reviewed', false);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('started_at', [$startDate, $endDate]);
    }

    public function scopeByAttemptNumber($query, $attemptNumber)
    {
        return $query->where('attempt_number', $attemptNumber);
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'in_progress' => 'warning',
            'submitted' => 'info',
            'completed' => 'success',
            'time_expired' => 'danger',
            'abandoned' => 'secondary',
            default => 'secondary'
        };
    }

    public function getGradeDisplayAttribute(): string
    {
        if (!$this->grade) return 'N/A';
        return $this->grade;
    }

    public function getIsInProgressAttribute(): bool
    {
        return $this->status === 'in_progress';
    }

    public function getIsSubmittedAttribute(): bool
    {
        return $this->status === 'submitted';
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsTimeExpiredAttribute(): bool
    {
        return $this->status === 'time_expired';
    }

    public function getTimeTakenDisplayAttribute(): string
    {
        if (!$this->time_taken_minutes) return 'N/A';
        
        $hours = intval($this->time_taken_minutes / 60);
        $minutes = $this->time_taken_minutes % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    public function getPercentageDisplayAttribute(): string
    {
        if (!$this->percentage) return 'N/A';
        return number_format($this->percentage, 1) . '%';
    }

    public function getMarksDisplayAttribute(): string
    {
        return $this->marks_obtained . '/' . $this->total_marks;
    }

    public function getCompletionRateAttribute(): float
    {
        if ($this->questions_attempted == 0) return 0;
        return round(($this->questions_answered / $this->questions_attempted) * 100, 2);
    }

    public function getAccuracyRateAttribute(): float
    {
        if ($this->questions_answered == 0) return 0;
        
        $correctAnswers = 0;
        foreach ($this->student_answers as $questionId => $answer) {
            if (isset($this->correct_answers[$questionId]) && 
                $this->correct_answers[$questionId] == $answer) {
                $correctAnswers++;
            }
        }
        
        return round(($correctAnswers / $this->questions_answered) * 100, 2);
    }

    public function getTimeEfficiencyAttribute(): float
    {
        if (!$this->time_taken_minutes || !$this->onlineExam->duration_minutes) return 0;
        
        $efficiency = ($this->time_taken_minutes / $this->onlineExam->duration_minutes) * 100;
        return round($efficiency, 2);
    }

    public function getTimeEfficiencyColorAttribute(): string
    {
        $efficiency = $this->time_efficiency;
        if ($efficiency <= 50) return 'success';
        if ($efficiency <= 75) return 'info';
        if ($efficiency <= 90) return 'warning';
        return 'danger';
    }

    public function getPerformanceLevelAttribute(): string
    {
        if (!$this->percentage) return 'N/A';
        
        if ($this->percentage >= 90) return 'Excellent';
        if ($this->percentage >= 80) return 'Very Good';
        if ($this->percentage >= 70) return 'Good';
        if ($this->percentage >= 60) return 'Satisfactory';
        if ($this->percentage >= 50) return 'Pass';
        return 'Fail';
    }

    public function getPerformanceColorAttribute(): string
    {
        return match($this->performance_level) {
            'Excellent' => 'success',
            'Very Good' => 'info',
            'Good' => 'primary',
            'Satisfactory' => 'warning',
            'Pass' => 'secondary',
            'Fail' => 'danger',
            default => 'secondary'
        };
    }

    public function getAttemptSummaryAttribute(): string
    {
        return "Attempt {$this->attempt_number} - " . 
               $this->marks_display . " ({$this->percentage_display}) - " .
               $this->performance_level;
    }

    public function canRetake(): bool
    {
        return $this->onlineExam->allow_retake && 
               $this->attempt_number < $this->onlineExam->max_attempts;
    }

    public function getNextAttemptNumberAttribute(): int
    {
        return $this->attempt_number + 1;
    }

    public function getRemainingAttemptsAttribute(): int
    {
        return max(0, $this->onlineExam->max_attempts - $this->attempt_number);
    }

    public function markAsReviewed(User $reviewer, string $comments = null): void
    {
        $this->is_reviewed = true;
        $this->reviewed_by = $reviewer->id;
        $this->reviewed_at = now();
        $this->teacher_comments = $comments;
        $this->save();
    }

    public function calculateGrade(): void
    {
        if ($this->percentage >= 90) {
            $this->grade = 'A+';
            $this->grade_point = 4.0;
        } elseif ($this->percentage >= 80) {
            $this->grade = 'A';
            $this->grade_point = 3.7;
        } elseif ($this->percentage >= 70) {
            $this->grade = 'B+';
            $this->grade_point = 3.3;
        } elseif ($this->percentage >= 60) {
            $this->grade = 'B';
            $this->grade_point = 3.0;
        } elseif ($this->percentage >= 50) {
            $this->grade = 'C';
            $this->grade_point = 2.0;
        } else {
            $this->grade = 'F';
            $this->grade_point = 0.0;
        }
        
        $this->is_passed = $this->percentage >= $this->onlineExam->passing_marks;
        $this->save();
    }
}
