<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_text', 'question_image', 'question_type', 'subject_id',
        'class_id', 'teacher_id', 'academic_year', 'difficulty_level',
        'marks', 'time_limit_seconds', 'options', 'correct_answer',
        'explanation', 'hints', 'tags', 'status', 'is_active',
        'usage_count', 'success_rate'
    ];

    protected $casts = [
        'options' => 'array', 'correct_answer' => 'array', 'tags' => 'array',
        'marks' => 'integer', 'time_limit_seconds' => 'integer',
        'usage_count' => 'integer', 'success_rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('question_type', $type);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTags($query, $tags)
    {
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        } else {
            $query->whereJsonContains('tags', $tags);
        }
        return $query;
    }

    public function scopePopular($query)
    {
        return $query->orderBy('usage_count', 'desc');
    }

    public function scopeHighSuccessRate($query)
    {
        return $query->where('success_rate', '>=', 70);
    }

    public function getQuestionTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->question_type));
    }

    public function getDifficultyLevelDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->difficulty_level));
    }

    public function getDifficultyColorAttribute(): string
    {
        return match($this->difficulty_level) {
            'easy' => 'success',
            'medium' => 'warning',
            'hard' => 'danger',
            'expert' => 'dark',
            default => 'secondary'
        };
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getOptionsDisplayAttribute(): string
    {
        if (!$this->options || empty($this->options)) return 'No options';
        return implode(', ', $this->options);
    }

    public function getCorrectAnswerDisplayAttribute(): string
    {
        if (!$this->correct_answer || empty($this->correct_answer)) return 'N/A';
        return implode(', ', $this->correct_answer);
    }

    public function getTagsDisplayAttribute(): string
    {
        if (!$this->tags || empty($this->tags)) return 'No tags';
        return implode(', ', $this->tags);
    }

    public function getTimeLimitDisplayAttribute(): string
    {
        if (!$this->time_limit_seconds) return 'No time limit';
        $minutes = intval($this->time_limit_seconds / 60);
        $seconds = $this->time_limit_seconds % 60;
        if ($minutes > 0 && $seconds > 0) {
            return "{$minutes}m {$seconds}s";
        } elseif ($minutes > 0) {
            return "{$minutes}m";
        } else {
            return "{$seconds}s";
        }
    }

    public function getSuccessRateDisplayAttribute(): string
    {
        if (!$this->success_rate) return 'N/A';
        return number_format($this->success_rate, 1) . '%';
    }

    public function getSuccessRateColorAttribute(): string
    {
        if (!$this->success_rate) return 'secondary';
        if ($this->success_rate >= 80) return 'success';
        if ($this->success_rate >= 60) return 'info';
        if ($this->success_rate >= 40) return 'warning';
        return 'danger';
    }

    public function isCorrectAnswer($answer): bool
    {
        if (!$this->correct_answer || empty($this->correct_answer)) return false;
        return in_array($answer, $this->correct_answer);
    }

    public function getQuestionPreviewAttribute(): string
    {
        $text = $this->question_text;
        if (strlen($text) > 100) {
            return substr($text, 0, 100) . '...';
        }
        return $text;
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function updateSuccessRate(float $newRate): void
    {
        if ($this->success_rate) {
            $this->success_rate = (($this->success_rate + $newRate) / 2);
        } else {
            $this->success_rate = $newRate;
        }
        $this->save();
    }
}
