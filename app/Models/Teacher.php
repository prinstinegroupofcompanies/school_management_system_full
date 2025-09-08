<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'teacher_id',
        'employee_id',
        'department_id',
        'designation_id',
        'qualification',
        'experience',
        'joining_date',
        'salary',
        'basic_salary',
        'status',
        'employment_status',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'contract_end_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
        'is_class_teacher' => 'boolean',
        'restricted_mode' => 'boolean',
        'restriction_date' => 'date',
        'restriction_expires_at' => 'datetime',
        'social_media_links' => 'array',
        'languages_known' => 'array',
        'interests_hobbies' => 'array',
    ];

    /**
     * Get the user that owns the teacher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department that the teacher belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the designation of the teacher.
     */
    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    /**
     * Get the classes that the teacher teaches.
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(ClassRoom::class, 'teacher_class')
                    ->withPivot(['is_class_teacher', 'assigned_at', 'unassigned_at'])
                    ->withTimestamps();
    }

    /**
     * Get the classes where the teacher is the class teacher.
     */
    public function classTeacherClasses(): BelongsToMany
    {
        return $this->belongsToMany(ClassRoom::class, 'teacher_class')
                    ->wherePivot('is_class_teacher', true)
                    ->withPivot(['is_class_teacher', 'assigned_at', 'unassigned_at'])
                    ->withTimestamps();
    }

    /**
     * Get the subjects that the teacher teaches.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    /**
     * Get the class routines for the teacher.
     */
    public function classRoutines(): HasMany
    {
        return $this->hasMany(ClassRoutine::class);
    }

    /**
     * Get the exam duties for the teacher.
     */
    public function examDuties(): HasMany
    {
        return $this->hasMany(ExamDuty::class);
    }

    /**
     * Get the homework assignments created by the teacher.
     */
    public function homeworkAssignments(): HasMany
    {
        return $this->hasMany(Homework::class);
    }

    /**
     * Get the study materials uploaded by the teacher.
     */
    public function studyMaterials(): HasMany
    {
        return $this->hasMany(StudyMaterial::class);
    }

    /**
     * Get the assignments created by the teacher.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the syllabus created by the teacher.
     */
    public function syllabi(): HasMany
    {
        return $this->hasMany(Syllabus::class);
    }

    /**
     * Get the attendance records for the teacher.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class);
    }

    /**
     * Get the leave requests for the teacher.
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Get the payroll records for the teacher.
     */
    public function payrollRecords(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * Get the online exams created by the teacher.
     */
    public function onlineExams(): HasMany
    {
        return $this->hasMany(OnlineExam::class);
    }

    /**
     * Get the question banks created by the teacher.
     */
    public function questionBanks(): HasMany
    {
        return $this->hasMany(QuestionBank::class);
    }

    /**
     * Get the lesson plans created by the teacher.
     */
    public function lessonPlans(): HasMany
    {
        return $this->hasMany(LessonPlan::class);
    }

    /**
     * Get the chat messages sent by the teacher.
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    /**
     * Get the teacher's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    /**
     * Get the teacher's email.
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email ?? '';
    }

    /**
     * Get the teacher's phone.
     */
    public function getPhoneAttribute(): string
    {
        return $this->user->phone ?? '';
    }

    /**
     * Check if teacher is in restricted mode.
     */
    public function isInRestrictedMode(): bool
    {
        return $this->restricted_mode && 
               $this->restriction_expires_at && 
               $this->restriction_expires_at->isFuture();
    }

    /**
     * Check if teacher is class teacher.
     */
    public function isClassTeacher(): bool
    {
        return $this->is_class_teacher;
    }

    /**
     * Get the teacher's experience in years.
     */
    public function getExperienceAttribute(): int
    {
        if (!$this->joining_date) {
            return 0;
        }
        
        $startDate = $this->joining_date;
        $endDate = $this->contract_end_date ?? now();
        
        return $startDate->diffInYears($endDate);
    }

    /**
     * Scope for active teachers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for class teachers.
     */
    public function scopeClassTeachers($query)
    {
        return $query->where('is_class_teacher', true);
    }

    /**
     * Scope for teachers in a specific department.
     */
    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope for teachers with a specific designation.
     */
    public function scopeWithDesignation($query, $designationId)
    {
        return $query->where('designation_id', $designationId);
    }
} 