<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class OnlineExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'class_id', 'subject_id', 'teacher_id',
        'academic_year', 'exam_date', 'start_time', 'end_time',
        'duration_minutes', 'total_questions', 'total_marks', 'passing_marks',
        'question_type', 'shuffle_questions', 'shuffle_options',
        'show_results_immediately', 'allow_review', 'allow_retake',
        'max_attempts', 'instructions', 'important_notes', 'status', 'is_active'
    ];

    protected $casts = [
        'exam_date' => 'date', 'start_time' => 'datetime', 'end_time' => 'datetime',
        'duration_minutes' => 'integer', 'total_questions' => 'integer',
        'total_marks' => 'integer', 'passing_marks' => 'integer',
        'shuffle_questions' => 'boolean', 'shuffle_options' => 'boolean',
        'show_results_immediately' => 'boolean', 'allow_review' => 'boolean',
        'allow_retake' => 'boolean', 'max_attempts' => 'integer', 'is_active' => 'boolean'
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(OnlineExamAttempt::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>=', now()->toDateString());
    }

    public function scopeOngoing($query)
    {
        $now = now();
        return $query->where('exam_date', $now->toDateString())
                    ->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now);
    }

    public function scopeCompleted($query)
    {
        return $query->where('end_time', '<', now());
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->exam_date > now()->toDateString();
    }

    public function getIsOngoingAttribute(): bool
    {
        $now = now();
        return $this->exam_date == $now->toDateString() &&
               $this->start_time <= $now && $this->end_time >= $now;
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->end_time < now();
    }

    public function getDurationDisplayAttribute(): string
    {
        if (!$this->duration_minutes) return 'No time limit';
        $hours = intval($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    public function getPassPercentageAttribute(): float
    {
        if ($this->total_marks == 0) return 0;
        return round(($this->passing_marks / $this->total_marks) * 100, 2);
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->class->students()->count();
    }

    public function getTotalAttemptsAttribute(): int
    {
        return $this->attempts()->count();
    }

    public function getUniqueAttemptsAttribute(): int
    {
        return $this->attempts()->distinct('student_id')->count();
    }

    public function getParticipationRateAttribute(): float
    {
        if ($this->total_students == 0) return 0;
        return round(($this->unique_attempts / $this->total_students) * 100, 2);
    }

    public function canStudentTakeExam(int $studentId): bool
    {
        if (!$this->is_active || $this->status !== 'active' || $this->is_completed) {
            return false;
        }
        $attemptsCount = $this->attempts()->where('student_id', $studentId)->count();
        return $attemptsCount < $this->max_attempts;
    }
}
