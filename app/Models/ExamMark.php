<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ExamMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_schedule_id',
        'subject_id',
        'class_id',
        'academic_year',
        'marks_obtained',
        'total_marks',
        'percentage',
        'grade',
        'grade_point',
        'remarks',
        'teacher_comments',
        'parent_comments',
        'is_absent',
        'is_late',
        'submission_time',
        'status',
        'marked_by',
        'marked_at',
        'verified_by',
        'verified_at',
        'teacher_id',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'is_absent' => 'boolean',
        'is_late' => 'boolean',
        'submission_time' => 'datetime',
        'marked_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function examSchedule(): BelongsTo
    {
        return $this->belongsTo(ExamSchedule::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Teacher::class);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByExamSchedule($query, $examScheduleId)
    {
        return $query->where('exam_schedule_id', $examScheduleId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAbsent($query)
    {
        return $query->where('is_absent', true);
    }

    public function scopePresent($query)
    {
        return $query->where('is_absent', false);
    }

    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    public function scopePassed($query)
    {
        return $query->where('marks_obtained', '>=', 'passing_marks');
    }

    public function scopeFailed($query)
    {
        return $query->where('marks_obtained', '<', 'passing_marks');
    }

    public function getIsPassedAttribute(): bool
    {
        if ($this->is_absent) return false;
        return $this->marks_obtained >= $this->subject->passing_marks;
    }

    public function getIsFailedAttribute(): bool
    {
        if ($this->is_absent) return false;
        return $this->marks_obtained < $this->subject->passing_marks;
    }

    public function getGradeDisplayAttribute(): string
    {
        if ($this->is_absent) return 'ABS';
        if ($this->is_late) return 'LATE';
        return $this->grade ?? 'N/A';
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getMarksDisplayAttribute(): string
    {
        if ($this->is_absent) return 'ABS';
        return $this->marks_obtained . '/' . $this->total_marks;
    }

    public function getPercentageDisplayAttribute(): string
    {
        if ($this->is_absent) return 'N/A';
        return number_format($this->percentage, 1) . '%';
    }

    public function getTimeStatusAttribute(): string
    {
        if ($this->is_absent) return 'Absent';
        if ($this->is_late) return 'Late';
        return 'On Time';
    }
}
