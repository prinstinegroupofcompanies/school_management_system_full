<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'teacher_id',
        'class_id',
        'level',
        'type',
        'hours_per_week',
        'book_name',
        'book_author',
        'book_publisher',
        'book_isbn',
        'passing_marks',
        'full_marks',
        'status',
        'is_active',
    ];

    protected $casts = [
        'hours_per_week' => 'integer',
        'passing_marks' => 'decimal:2',
        'full_marks' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(ClassRoom::class, 'subject_classes', 'subject_id', 'class_id');
    }

    // Keep the old method for backward compatibility but make it return the first class
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id')->withDefault();
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    // Note: exam_marks table doesn't have subject_id column
    // public function examMarks(): HasMany
    // {
    //     return $this->hasMany(ExamMark::class);
    // }

    // Note: student_attendances table doesn't have subject_id column
    // public function studentAttendances(): HasMany
    // {
    //     return $this->hasMany(StudentAttendance::class);
    // }

    public function homework(): HasMany
    {
        return $this->hasMany(Homework::class);
    }

    public function studyMaterials(): HasMany
    {
        return $this->hasMany(StudyMaterial::class);
    }

    public function onlineExams(): HasMany
    {
        return $this->hasMany(OnlineExam::class);
    }

    public function questionBanks(): HasMany
    {
        return $this->hasMany(QuestionBank::class);
    }

    public function chatGroups(): HasMany
    {
        return $this->hasMany(ChatGroup::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->whereHas('classes', function($q) use ($classId) {
            $q->where('class_id', $classId);
        });
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->class->students()->count();
    }

    // Note: These methods are commented out because exam_marks table doesn't have subject_id
    // public function getAverageMarksAttribute(): float
    // {
    //     $marks = $this->examMarks()->whereNotNull('marks_obtained')->pluck('marks_obtained');
    //     return $marks->count() > 0 ? $marks->avg() : 0;
    // }

    // public function getPassPercentageAttribute(): float
    // {
    //     $totalMarks = $this->examMarks()->count();
    //     if ($totalMarks === 0) return 0;
        
    //     $passedMarks = $this->examMarks()
    //         ->where('marks_obtained', '>=', $this->passing_marks)
    //         ->count();
        
    //     return round(($passedMarks / $totalMarks) * 100, 2);
    // }
}
