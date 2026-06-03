<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'exam_type_id',
        'class_id',
        'subject_id',
        'academic_year',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'venue',
        'instructions',
        'important_notes',
        'status',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'exam_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function examMarks(): HasMany
    {
        return $this->hasMany(ExamMark::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByExamType($query, $examTypeId)
    {
        return $query->where('exam_type_id', $examTypeId);
    }

    public function scopeByAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    /** Optional: teacher not on table; view uses @if($schedule->teacher) */
    public function getTeacherAttribute()
    {
        return null;
    }

    public function getExamDateAttribute($value)
    {
        if ($value) {
            return \Carbon\Carbon::parse($value);
        }
        return $this->start_date;
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString());
    }

    public function scopeOngoing($query)
    {
        $today = now()->toDateString();
        return $query->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
    }

    public function scopeCompleted($query)
    {
        return $query->where('end_date', '<', now()->toDateString());
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date > now()->toDateString();
    }

    public function getIsOngoingAttribute(): bool
    {
        $today = now()->toDateString();
        return $this->start_date <= $today && $this->end_date >= $today;
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->end_date < now()->toDateString();
    }

    public function getDurationAttribute(): string
    {
        if ($this->start_date == $this->end_date) {
            return $this->start_date->format('M d, Y');
        }
        return $this->start_date->format('M d') . ' - ' . $this->end_date->format('M d, Y');
    }

    public function getTimeRangeAttribute(): string
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
        }
        return '';
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->class->students()->count();
    }

    public function getTotalMarksSubmittedAttribute(): int
    {
        return $this->examMarks()->whereNotNull('marks_obtained')->count();
    }

    public function getSubmissionPercentageAttribute(): float
    {
        if ($this->total_students == 0) return 0;
        return round(($this->total_marks_submitted / $this->total_students) * 100, 2);
    }
}
