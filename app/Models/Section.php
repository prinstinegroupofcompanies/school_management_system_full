<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'class_id',
        'section_teacher_id',
        'start_time',
        'end_time',
        'capacity',
        'status',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function sectionTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'section_teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'section_id');
    }

    public function studentGroups(): HasMany
    {
        return $this->hasMany(StudentGroup::class, 'section_id');
    }

    public function homework(): HasMany
    {
        return $this->hasMany(Homework::class, 'section_id');
    }

    public function studyMaterials(): HasMany
    {
        return $this->hasMany(StudyMaterial::class, 'section_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('section_teacher_id', $teacherId);
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->students()->count();
    }

    public function getAvailableCapacityAttribute(): int
    {
        return $this->capacity - $this->total_students;
    }

    public function getTimeRangeAttribute(): string
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
        }
        return '';
    }
}
