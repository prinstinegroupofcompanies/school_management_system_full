<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'class_id', 'subject_id', 'title', 'description',
        'instructions', 'start_time', 'end_time', 'duration_minutes',
        'total_marks', 'passing_marks', 'status', 'allow_late_submission',
        'late_submission_penalty', 'randomize_questions', 'show_results_immediately',
        'questions', 'settings', 'attempts_allowed',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_minutes' => 'integer',
        'total_marks' => 'integer',
        'passing_marks' => 'integer',
        'allow_late_submission' => 'boolean',
        'late_submission_penalty' => 'integer',
        'randomize_questions' => 'boolean',
        'show_results_immediately' => 'boolean',
        'questions' => 'array',
        'settings' => 'array',
        'attempts_allowed' => 'integer',
    ];

    /**
     * Get the teacher.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the class.
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * Get the subject.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get exam attempts.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(LiveExamAttempt::class);
    }

    /**
     * Scope for upcoming exams.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
                     ->where('status', 'scheduled');
    }

    /**
     * Scope for active exams.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('start_time', '<=', now())
                     ->where('end_time', '>=', now());
    }

    /**
     * Check if exam is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' &&
               $this->start_time <= now() &&
               $this->end_time >= now();
    }

    /**
     * Check if student has attempted.
     */
    public function hasStudentAttempted(int $studentId): bool
    {
        return $this->attempts()->where('student_id', $studentId)->exists();
    }

    /**
     * Get student attempt.
     */
    public function getStudentAttempt(int $studentId): ?LiveExamAttempt
    {
        return $this->attempts()->where('student_id', $studentId)->first();
    }
}
