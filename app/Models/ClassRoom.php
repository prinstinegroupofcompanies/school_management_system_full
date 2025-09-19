<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'capacity',
        'class_teacher_id',
        'room_number',
        'building',
        'floor',
        'status',
        'is_active',
        'session',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function classTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'class_teacher_id');
    }

    /**
     * Get all teachers assigned to this class.
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_class')
                    ->withPivot(['is_class_teacher', 'assigned_at', 'unassigned_at'])
                    ->withTimestamps();
    }

    /**
     * Get the class teacher for this class.
     */
    public function classTeachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_class')
                    ->wherePivot('is_class_teacher', true)
                    ->withPivot(['is_class_teacher', 'assigned_at', 'unassigned_at'])
                    ->withTimestamps();
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_classes', 'class_id', 'subject_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class, 'class_id');
    }

    public function examSchedules(): HasMany
    {
        return $this->hasMany(ExamSchedule::class, 'class_id');
    }

    public function onlineExams(): HasMany
    {
        return $this->hasMany(OnlineExam::class, 'class_id');
    }

    public function homework(): HasMany
    {
        return $this->hasMany(Homework::class, 'class_id');
    }

    public function studyMaterials(): HasMany
    {
        return $this->hasMany(StudyMaterial::class, 'class_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('class_teacher_id', $teacherId);
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->students()->count();
    }

    public function getAvailableCapacityAttribute(): int
    {
        return $this->capacity - $this->total_students;
    }

    /**
     * Get fee structures for this class
     */
    public function feeStructures(): HasMany
    {
        return $this->hasMany(ClassFeeStructure::class, 'class_id');
    }

    /**
     * Get active fee structure for current academic year
     */
    public function currentFeeStructure()
    {
        return $this->hasOne(ClassFeeStructure::class, 'class_id')
                    ->where('is_active', true)
                    ->where('academic_year', date('Y'))
                    ->where('effective_from', '<=', now())
                    ->where(function($query) {
                        $query->whereNull('effective_to')
                              ->orWhere('effective_to', '>=', now());
                    });
    }

    /**
     * Get fee structure for specific academic year
     */
    public function feeStructureForYear($year)
    {
        return $this->hasOne(ClassFeeStructure::class, 'class_id')
                    ->where('is_active', true)
                    ->where('academic_year', $year);
    }
}
