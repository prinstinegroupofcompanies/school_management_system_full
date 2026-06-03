<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'live_exam_id', 'student_id', 'started_at', 'submitted_at',
        'time_spent_minutes', 'answers', 'score', 'percentage',
        'status', 'teacher_remarks',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'time_spent_minutes' => 'integer',
        'answers' => 'array',
        'score' => 'integer',
        'percentage' => 'decimal:2',
    ];

    /**
     * Get the exam.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(LiveExam::class, 'live_exam_id');
    }

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Check if attempt is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if attempt is submitted.
     */
    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted', 'auto_submitted', 'graded']);
    }

    /**
     * Calculate remaining time in minutes.
     */
    public function getRemainingTimeMinutes(): ?int
    {
        if (!$this->exam || !$this->isInProgress()) {
            return null;
        }

        $examEndTime = $this->exam->end_time;
        $now = now();
        
        if ($now > $examEndTime) {
            return 0;
        }

        return $now->diffInMinutes($examEndTime);
    }
}
