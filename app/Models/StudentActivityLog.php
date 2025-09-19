<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'user_id',
        'activity_type',
        'activity_category',
        'activity_title',
        'activity_description',
        'related_model',
        'related_id',
        'related_data',
        'academic_year',
        'semester',
        'class_id',
        'subject_id',
        'old_values',
        'new_values',
        'metadata',
        'impact_level',
        'requires_parent_notification',
        'requires_admin_review',
        'status',
        'is_visible_to_student',
        'is_visible_to_parent',
        'activity_timestamp',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'related_data' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'requires_parent_notification' => 'boolean',
        'requires_admin_review' => 'boolean',
        'is_visible_to_student' => 'boolean',
        'is_visible_to_parent' => 'boolean',
        'activity_timestamp' => 'datetime',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    // Dynamic relationship to related model
    public function relatedModel()
    {
        if ($this->related_model && $this->related_id) {
            return $this->belongsTo($this->related_model, 'related_id');
        }
        return null;
    }

    // Scopes
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('activity_category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeForAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeHighImpact($query)
    {
        return $query->whereIn('impact_level', ['high', 'critical']);
    }

    public function scopeRequiringNotification($query)
    {
        return $query->where('requires_parent_notification', true);
    }

    public function scopeRequiringReview($query)
    {
        return $query->where('requires_admin_review', true);
    }

    public function scopeVisibleToStudent($query)
    {
        return $query->where('is_visible_to_student', true);
    }

    public function scopeVisibleToParent($query)
    {
        return $query->where('is_visible_to_parent', true);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('activity_timestamp', '>=', now()->subDays($days));
    }

    // Static methods for creating common activity logs
    public static function logEnrollment(Student $student, ClassRoom $class, User $user)
    {
        return self::create([
            'student_id' => $student->id,
            'user_id' => $user->id,
            'activity_type' => 'enrollment',
            'activity_category' => 'administrative',
            'activity_title' => 'Student Enrolled in Class',
            'activity_description' => "Student {$student->user->name} enrolled in class {$class->name}",
            'related_model' => ClassRoom::class,
            'related_id' => $class->id,
            'class_id' => $class->id,
            'academic_year' => $class->academic_year ?? date('Y'),
            'impact_level' => 'medium',
            'requires_parent_notification' => true,
            'activity_timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logGradeUpdate(Student $student, InternationalGrade $grade, User $user)
    {
        return self::create([
            'student_id' => $student->id,
            'user_id' => $user->id,
            'activity_type' => 'grade_update',
            'activity_category' => 'academic',
            'activity_title' => 'Grade Updated',
            'activity_description' => "Grade updated for {$grade->subject->name}: {$grade->letter_grade}",
            'related_model' => InternationalGrade::class,
            'related_id' => $grade->id,
            'subject_id' => $grade->subject_id,
            'class_id' => $grade->class_id,
            'academic_year' => $grade->academic_year,
            'semester' => $grade->semester,
            'new_values' => [
                'letter_grade' => $grade->letter_grade,
                'percentage' => $grade->percentage,
                'gpa_points' => $grade->gpa_points,
            ],
            'impact_level' => 'medium',
            'requires_parent_notification' => true,
            'is_visible_to_student' => true,
            'is_visible_to_parent' => true,
            'activity_timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logFeePayment(Student $student, $amount, $paymentType, User $user)
    {
        return self::create([
            'student_id' => $student->id,
            'user_id' => $user->id,
            'activity_type' => 'payment',
            'activity_category' => 'financial',
            'activity_title' => 'Fee Payment Recorded',
            'activity_description' => "Payment of ${amount} recorded for {$paymentType}",
            'academic_year' => date('Y'),
            'new_values' => [
                'amount' => $amount,
                'payment_type' => $paymentType,
                'payment_date' => now()->toDateString(),
            ],
            'impact_level' => 'medium',
            'requires_parent_notification' => true,
            'is_visible_to_student' => true,
            'is_visible_to_parent' => true,
            'activity_timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logAttendance(Student $student, $status, Subject $subject, User $user)
    {
        return self::create([
            'student_id' => $student->id,
            'user_id' => $user->id,
            'activity_type' => 'attendance',
            'activity_category' => 'academic',
            'activity_title' => 'Attendance Recorded',
            'activity_description' => "Attendance marked as {$status} for {$subject->name}",
            'related_model' => Subject::class,
            'related_id' => $subject->id,
            'subject_id' => $subject->id,
            'class_id' => $student->class_id,
            'academic_year' => date('Y'),
            'new_values' => [
                'status' => $status,
                'date' => now()->toDateString(),
                'subject' => $subject->name,
            ],
            'impact_level' => $status === 'absent' ? 'medium' : 'low',
            'requires_parent_notification' => $status === 'absent',
            'activity_timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logPromotion(Student $student, ClassRoom $fromClass, ClassRoom $toClass, User $user)
    {
        return self::create([
            'student_id' => $student->id,
            'user_id' => $user->id,
            'activity_type' => 'promotion',
            'activity_category' => 'administrative',
            'activity_title' => 'Student Promoted',
            'activity_description' => "Student promoted from {$fromClass->name} to {$toClass->name}",
            'related_model' => ClassRoom::class,
            'related_id' => $toClass->id,
            'class_id' => $toClass->id,
            'academic_year' => date('Y'),
            'old_values' => ['class' => $fromClass->name, 'class_id' => $fromClass->id],
            'new_values' => ['class' => $toClass->name, 'class_id' => $toClass->id],
            'impact_level' => 'high',
            'requires_parent_notification' => true,
            'requires_admin_review' => true,
            'is_visible_to_student' => true,
            'is_visible_to_parent' => true,
            'activity_timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logDisciplinaryAction(Student $student, $action, $reason, User $user)
    {
        return self::create([
            'student_id' => $student->id,
            'user_id' => $user->id,
            'activity_type' => 'discipline',
            'activity_category' => 'disciplinary',
            'activity_title' => 'Disciplinary Action',
            'activity_description' => "Disciplinary action: {$action}. Reason: {$reason}",
            'class_id' => $student->class_id,
            'academic_year' => date('Y'),
            'new_values' => [
                'action' => $action,
                'reason' => $reason,
                'date' => now()->toDateString(),
            ],
            'impact_level' => 'high',
            'requires_parent_notification' => true,
            'requires_admin_review' => true,
            'is_visible_to_parent' => true,
            'activity_timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Helper methods
    public function getImpactBadgeColor(): string
    {
        return match($this->impact_level) {
            'low' => 'gray',
            'medium' => 'blue',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray'
        };
    }

    public function getCategoryIcon(): string
    {
        return match($this->activity_category) {
            'academic' => 'fas fa-graduation-cap',
            'financial' => 'fas fa-dollar-sign',
            'administrative' => 'fas fa-cog',
            'disciplinary' => 'fas fa-gavel',
            'extracurricular' => 'fas fa-trophy',
            default => 'fas fa-info-circle'
        };
    }

    public function getFormattedDescription(): string
    {
        $description = $this->activity_description;
        
        // Add context from metadata if available
        if ($this->metadata && is_array($this->metadata)) {
            foreach ($this->metadata as $key => $value) {
                $description .= " [{$key}: {$value}]";
            }
        }
        
        return $description;
    }
}