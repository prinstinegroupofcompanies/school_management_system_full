<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamPaper extends Model
{
    protected $fillable = [
        'teacher_id',
        'class_id', 
        'subject_id',
        'title',
        'description',
        'instructions',
        'total_marks',
        'duration_minutes',
        'passing_marks',
        'exam_type',
        'start_time',
        'end_time',
        'questions',
        'status',
        'exam_date',
        'is_published',
        'randomize_questions',
        'show_results_immediately',
        'allow_review'
    ];

    protected $casts = [
        'questions' => 'array',
        'exam_date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_published' => 'boolean',
        'randomize_questions' => 'boolean',
        'show_results_immediately' => 'boolean',
        'allow_review' => 'boolean',
    ];

    protected $attributes = [
        'questions' => '[]', // Default to empty array
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    // Questions are stored as JSON in the questions column, not as separate records
    // public function questions(): HasMany
    // {
    //     return $this->hasMany(ExamQuestion::class, 'exam_schedule_id');
    // }

    public function attempts(): HasMany
    {
        return $this->hasMany(StudentExamAttempt::class, 'exam_paper_id');
    }
}
