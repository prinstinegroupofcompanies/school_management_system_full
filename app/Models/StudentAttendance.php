<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'section_id',
        'subject_id',
        'academic_year',
        'attendance_date',
        'status',
        'check_in_time',
        'check_out_time',
        'remarks',
        'teacher_remarks',
        'parent_remarks',
        'is_excused',
        'excuse_reason',
        'excuse_document',
        'marked_by',
        'marked_at',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'is_excused' => 'boolean',
        'marked_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeExcused($query)
    {
        return $query->where('is_excused', true);
    }

    public function scopeByMarkedBy($query, $userId)
    {
        return $query->where('marked_by', $userId);
    }

    public function getStatusDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'present' => 'success',
            'absent' => 'danger',
            'late' => 'warning',
            'half_day' => 'info',
            'sick_leave' => 'secondary',
            'other_leave' => 'secondary',
            default => 'primary'
        };
    }

    public function getIsPresentAttribute(): bool
    {
        return in_array($this->status, ['present', 'late', 'half_day']);
    }

    public function getIsAbsentAttribute(): bool
    {
        return in_array($this->status, ['absent', 'sick_leave', 'other_leave']);
    }

    public function getTimeSpentAttribute(): string
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return 'N/A';
        }

        $duration = $this->check_out_time->diffInMinutes($this->check_in_time);
        $hours = intval($duration / 60);
        $minutes = $duration % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    public function getAttendancePercentageAttribute(): float
    {
        $totalDays = $this->student->attendances()
            ->where('academic_year', $this->academic_year)
            ->count();

        if ($totalDays == 0) return 0;

        $presentDays = $this->student->attendances()
            ->where('academic_year', $this->academic_year)
            ->whereIn('status', ['present', 'late', 'half_day'])
            ->count();

        return round(($presentDays / $totalDays) * 100, 2);
    }
}
