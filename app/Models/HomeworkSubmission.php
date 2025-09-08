<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class HomeworkSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'homework_id', 'student_id', 'submission_text', 'attachments',
        'submitted_at', 'is_late', 'late_minutes', 'late_penalty',
        'marks_obtained', 'total_marks', 'percentage', 'grade',
        'teacher_feedback', 'student_comments', 'status', 'is_approved'
    ];

    protected $casts = [
        'attachments' => 'array', 'submitted_at' => 'datetime',
        'is_late' => 'boolean', 'late_minutes' => 'integer',
        'late_penalty' => 'decimal:2', 'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2', 'percentage' => 'decimal:2',
        'is_approved' => 'boolean'
    ];

    public function homework(): BelongsTo
    {
        return $this->belongsTo(Homework::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeByHomework($query, $homeworkId)
    {
        return $query->where('homework_id', $homeworkId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    public function scopeOnTime($query)
    {
        return $query->where('is_late', false);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeUnapproved($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeGraded($query)
    {
        return $query->whereNotNull('marks_obtained');
    }

    public function scopeUngraded($query)
    {
        return $query->whereNull('marks_obtained');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('submitted_at', [$startDate, $endDate]);
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'submitted' => 'info',
            'under_review' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'graded' => 'primary',
            'archived' => 'secondary',
            default => 'secondary'
        };
    }

    public function getGradeDisplayAttribute(): string
    {
        if (!$this->grade) return 'N/A';
        return $this->grade;
    }

    public function getIsGradedAttribute(): bool
    {
        return $this->marks_obtained !== null;
    }

    public function getIsPassedAttribute(): bool
    {
        if (!$this->is_graded) return false;
        return $this->marks_obtained >= ($this->total_marks * 0.5);
    }

    public function getIsFailedAttribute(): bool
    {
        if (!$this->is_graded) return false;
        return $this->marks_obtained < ($this->total_marks * 0.5);
    }

    public function getMarksDisplayAttribute(): string
    {
        if (!$this->is_graded) return 'Not graded';
        return $this->marks_obtained . '/' . $this->total_marks;
    }

    public function getPercentageDisplayAttribute(): string
    {
        if (!$this->percentage) return 'N/A';
        return number_format($this->percentage, 1) . '%';
    }

    public function getLatePenaltyDisplayAttribute(): string
    {
        if (!$this->late_penalty) return 'No penalty';
        return $this->late_penalty . ' marks';
    }

    public function getAttachmentsDisplayAttribute(): string
    {
        if (!$this->attachments || empty($this->attachments)) return 'No attachments';
        return implode(', ', $this->attachments);
    }

    public function getSubmissionTimeAttribute(): string
    {
        return $this->submitted_at->format('M d, Y \a\t H:i');
    }

    public function getLateTimeDisplayAttribute(): string
    {
        if (!$this->is_late) return 'On time';
        
        if ($this->late_minutes < 60) {
            return $this->late_minutes . ' minutes late';
        }
        
        $hours = intval($this->late_minutes / 60);
        $minutes = $this->late_minutes % 60;
        
        if ($minutes > 0) {
            return "{$hours}h {$minutes}m late";
        } else {
            return "{$hours}h late";
        }
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

    public function getFinalMarksAttribute(): float
    {
        if (!$this->is_graded) return 0;
        
        $finalMarks = $this->marks_obtained;
        
        // Apply late penalty if applicable
        if ($this->is_late && $this->late_penalty > 0) {
            $finalMarks -= $this->late_penalty;
        }
        
        return max(0, $finalMarks);
    }

    public function getFinalPercentageAttribute(): float
    {
        if ($this->total_marks == 0) return 0;
        return round(($this->final_marks / $this->total_marks) * 100, 2);
    }

    public function getFinalPercentageDisplayAttribute(): string
    {
        return number_format($this->final_percentage, 1) . '%';
    }

    public function getFinalGradeAttribute(): string
    {
        if (!$this->is_graded) return 'N/A';
        
        $percentage = $this->final_percentage;
        
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        return 'F';
    }

    public function getFinalGradeColorAttribute(): string
    {
        $grade = $this->final_grade;
        
        return match($grade) {
            'A+', 'A' => 'success',
            'B+', 'B' => 'info',
            'C' => 'warning',
            'F' => 'danger',
            default => 'secondary'
        };
    }

    public function getSubmissionSummaryAttribute(): string
    {
        $summary = $this->student->name . ' - ' . $this->homework->title;
        
        if ($this->is_graded) {
            $summary .= ' (' . $this->final_marks . '/' . $this->total_marks . ')';
        } else {
            $summary .= ' (Not graded)';
        }
        
        if ($this->is_late) {
            $summary .= ' - Late';
        }
        
        return $summary;
    }

    public function getTeacherFeedbackDisplayAttribute(): string
    {
        if (!$this->teacher_feedback) return 'No feedback provided';
        return $this->teacher_feedback;
    }

    public function getStudentCommentsDisplayAttribute(): string
    {
        if (!$this->student_comments) return 'No comments';
        return $this->student_comments;
    }

    public function markAsGraded(float $marks, string $feedback = null): void
    {
        $this->marks_obtained = $marks;
        $this->percentage = ($marks / $this->total_marks) * 100;
        $this->grade = $this->calculateGrade($this->percentage);
        $this->status = 'graded';
        
        if ($feedback) {
            $this->teacher_feedback = $feedback;
        }
        
        $this->save();
    }

    public function approve(): void
    {
        $this->is_approved = true;
        $this->status = 'approved';
        $this->save();
    }

    public function reject(string $reason = null): void
    {
        $this->is_approved = false;
        $this->status = 'rejected';
        
        if ($reason) {
            $this->teacher_feedback = $reason;
        }
        
        $this->save();
    }

    private function calculateGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        return 'F';
    }
}
