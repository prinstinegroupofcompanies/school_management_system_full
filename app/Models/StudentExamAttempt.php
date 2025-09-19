<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_paper_id',
        'student_id',
        'attempt_number',
        'started_at',
        'submitted_at',
        'auto_submit_at',
        'status',
        'time_spent_minutes',
        'answers',
        'question_order',
        'raw_score',
        'percentage_score',
        'letter_grade',
        'is_passed',
        'teacher_feedback',
        'question_feedback',
        'reviewed_by_teacher',
        'reviewed_at',
        'reviewed_by',
        'ip_address',
        'user_agent',
        'security_flags',
        'flagged_for_review',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'auto_submit_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'answers' => 'array',
        'question_order' => 'array',
        'question_feedback' => 'array',
        'security_flags' => 'array',
        'raw_score' => 'decimal:2',
        'percentage_score' => 'decimal:2',
        'reviewed_by_teacher' => 'boolean',
        'is_passed' => 'boolean',
        'flagged_for_review' => 'boolean',
    ];

    // Relationships
    public function examPaper(): BelongsTo
    {
        return $this->belongsTo(ExamPaper::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeSubmitted($query)
    {
        return $query->whereIn('status', ['submitted', 'auto_submitted']);
    }

    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForExam($query, $examId)
    {
        return $query->where('exam_paper_id', $examId);
    }

    // Helper methods
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted', 'auto_submitted']);
    }

    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    public function getTimeRemainingSeconds(): int
    {
        if (!$this->isInProgress()) {
            return 0;
        }

        return max(0, now()->diffInSeconds($this->auto_submit_at, false));
    }

    public function getFormattedTimeRemaining(): string
    {
        $seconds = $this->getTimeRemainingSeconds();
        
        if ($seconds <= 0) {
            return 'Time expired';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
        } else {
            return sprintf('%02d:%02d', $minutes, $remainingSeconds);
        }
    }

    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'in_progress' => 'blue',
            'submitted' => 'green',
            'auto_submitted' => 'yellow',
            'under_review' => 'orange',
            'graded' => 'purple',
            'abandoned' => 'red',
            default => 'gray'
        };
    }

    public function getGradeBadgeColor(): string
    {
        if (!$this->letter_grade) return 'gray';

        return match($this->letter_grade) {
            'A+', 'A', 'A-' => 'green',
            'B+', 'B', 'B-' => 'blue',
            'C+', 'C', 'C-' => 'yellow',
            'D+', 'D' => 'orange',
            'F' => 'red',
            default => 'gray'
        };
    }

    public function hasSecurityFlags(): bool
    {
        return !empty($this->security_flags);
    }

    public function getSecurityFlagsSummary(): string
    {
        if (!$this->hasSecurityFlags()) {
            return 'No security issues detected';
        }

        $flags = $this->security_flags;
        $summary = [];

        if (isset($flags['tab_switches']) && $flags['tab_switches'] > 0) {
            $summary[] = "{$flags['tab_switches']} tab switches";
        }

        if (isset($flags['copy_attempts']) && $flags['copy_attempts'] > 0) {
            $summary[] = "{$flags['copy_attempts']} copy attempts";
        }

        if (isset($flags['paste_attempts']) && $flags['paste_attempts'] > 0) {
            $summary[] = "{$flags['paste_attempts']} paste attempts";
        }

        return implode(', ', $summary);
    }

    public function calculateScore()
    {
        $exam = $this->examPaper;
        $questions = $exam->questions;
        
        if (!$this->answers || $questions->isEmpty()) {
            return;
        }

        $totalScore = 0;
        $maxScore = 0;

        foreach ($questions as $question) {
            $maxScore += $question->points;
            $studentAnswer = $this->answers[$question->id] ?? null;
            
            if ($this->checkAnswer($question, $studentAnswer)) {
                $totalScore += $question->points;
            }
        }

        $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
        $letterGrade = $this->calculateLetterGrade($percentage);
        $isPassed = $totalScore >= $exam->passing_marks;

        $this->update([
            'raw_score' => $totalScore,
            'percentage_score' => round($percentage, 2),
            'letter_grade' => $letterGrade,
            'is_passed' => $isPassed,
        ]);
    }

    private function checkAnswer($question, $studentAnswer): bool
    {
        if (!$studentAnswer || !$question->correct_answers) {
            return false;
        }

        $correctAnswers = $question->correct_answers;
        
        switch ($question->question_type) {
            case 'multiple_choice':
                return in_array($studentAnswer, $correctAnswers);
                
            case 'true_false':
                return strtolower($studentAnswer) === strtolower($correctAnswers[0] ?? '');
                
            case 'fill_blank':
                foreach ($correctAnswers as $correct) {
                    if (strtolower(trim($studentAnswer)) === strtolower(trim($correct))) {
                        return true;
                    }
                }
                return false;
                
            default:
                return false; // Requires manual review
        }
    }

    private function calculateLetterGrade($percentage): string
    {
        if ($percentage >= 97) return 'A+';
        if ($percentage >= 93) return 'A';
        if ($percentage >= 90) return 'A-';
        if ($percentage >= 87) return 'B+';
        if ($percentage >= 83) return 'B';
        if ($percentage >= 80) return 'B-';
        if ($percentage >= 77) return 'C+';
        if ($percentage >= 73) return 'C';
        if ($percentage >= 70) return 'C-';
        if ($percentage >= 67) return 'D+';
        if ($percentage >= 65) return 'D';
        return 'F';
    }
}