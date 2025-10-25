<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'class_id',
        'section_id',
        'academic_year',
        'admission_date',
        'gender',
        'date_of_birth',
        'level',
        'phone',
        'address',
        'status',
        // legacy/extended fields kept for compatibility
        'admission_no', 'roll_no', 'first_name', 'last_name', 'middle_name',
        'blood_group', 'religion', 'caste', 'mother_tongue', 'nationality',
        'student_category_id', 'student_group_id', 'student_house_id',
        'height', 'weight', 'as_on_date', 'is_active', 'is_transport', 'is_hostel',
        'previous_school', 'previous_class', 'admission_query_id', 'sibling_ids',
        'emergency_contact', 'medical_conditions', 'allergies', 'special_needs',
        'transport_route_id', 'hostel_room_id', 'guardian_id', 'father_id', 'mother_id',
        'local_guardian_id', 'fee_structure_id', 'scholarship_id', 'discount_id',
        'wallet_balance', 'last_payment_date', 'payment_status', 'restricted_access',
        'restriction_reason', 'restriction_date', 'restriction_expires_at',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'date_of_birth' => 'date',
        'as_on_date' => 'date',
        'is_active' => 'boolean',
        'is_transport' => 'boolean',
        'is_hostel' => 'boolean',
        'sibling_ids' => 'array',
        'wallet_balance' => 'decimal:2',
        'last_payment_date' => 'date',
        'restricted_access' => 'boolean',
        'restriction_date' => 'date',
        'restriction_expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class that the student belongs to.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * Get the class room that the student belongs to (alias for class).
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * Get the section that the student belongs to.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the student category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(StudentCategory::class, 'student_category_id');
    }

    /**
     * Get the student group.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(StudentGroup::class, 'student_group_id');
    }

    /**
     * Get the student house.
     */
    public function house(): BelongsTo
    {
        return $this->belongsTo(StudentHouse::class, 'student_house_id');
    }

    /**
     * Get the guardian.
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * Get the father.
     */
    public function father(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'father_id');
    }

    /**
     * Get the mother.
     */
    public function mother(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'mother_id');
    }

    /**
     * Get the local guardian.
     */
    public function localGuardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'local_guardian_id');
    }

    /**
     * Get the transport route.
     */
    public function transportRoute(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class);
    }

    /**
     * Get the hostel room.
     */
    public function hostelRoom(): BelongsTo
    {
        return $this->belongsTo(HostelRoom::class);
    }

    /**
     * Get the fee structure.
     */
    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    /**
     * Get the scholarship.
     */
    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    /**
     * Get the discount.
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Get the attendance records.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    /**
     * Get the exam marks.
     */
    public function examMarks(): HasMany
    {
        return $this->hasMany(ExamMark::class);
    }

    /**
     * Get the fee payments.
     */
    public function feePayments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    /**
     * Get the student fees.
     */
    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }

    /**
     * Get the homework submissions.
     */
    public function homeworkSubmissions(): HasMany
    {
        return $this->hasMany(HomeworkSubmission::class);
    }

    /**
     * Get the online exam attempts.
     */
    public function onlineExamAttempts(): HasMany
    {
        return $this->hasMany(OnlineExamAttempt::class);
    }

    /**
     * Get the exam attempts.
     */
    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * Get the library book issues.
     */
    public function bookIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class);
    }

    /**
     * Get the student timeline.
     */
    public function timeline(): HasMany
    {
        return $this->hasMany(StudentTimeline::class);
    }

    /**
     * Get the siblings.
     */
    public function siblings()
    {
        if (!$this->sibling_ids) {
            return collect();
        }
        return Student::whereIn('id', $this->sibling_ids)->get();
    }

    /**
     * Get the full name.
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }

    /**
     * Get the age.
     */
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : 0;
    }

    /**
     * Check if student has fee restrictions.
     */
    public function hasFeeRestrictions(): bool
    {
        return $this->restricted_access && 
               $this->restriction_expires_at && 
               $this->restriction_expires_at->isFuture();
    }

    /**
     * Scope for active students.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for students in a specific class.
     */
    public function scopeInClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope for students in a specific section.
     */
    public function scopeInSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    /**
     * Scope for students in a specific academic year.
     */
    public function scopeInAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'student_subject')->withTimestamps();
    }

    // New relationships for enhanced functionality
    public function internationalGrades(): HasMany
    {
        return $this->hasMany(InternationalGrade::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(StudentActivityLog::class);
    }

    public function classFeeStructure(): BelongsTo
    {
        return $this->belongsTo(ClassFeeStructure::class, 'class_id', 'class_id')
                    ->where('is_active', true)
                    ->where('academic_year', $this->academic_year ?? date('Y'));
    }

    // Auto-generation methods
    public static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            // Generate unique admission number (use existing admission_no column)
            if (!$student->admission_no) {
                $student->admission_no = self::generateAdmissionNumber();
            }
            
            // Generate unique student ID (use existing student_id column)
            if (!$student->student_id) {
                $student->student_id = self::generateStudentNumber();
            }
            
            // Set default academic year if not provided
            if (!$student->academic_year) {
                $student->academic_year = date('Y');
            }
        });

        static::created(function ($student) {
            // Log enrollment activity
            if (auth()->check()) {
                StudentActivityLog::logEnrollment($student, $student->classRoom, auth()->user());
            }
        });
    }

    // Auto-generation helper methods
    public static function generateAdmissionNumber(): string
    {
        $year = date('Y');
        $lastStudent = self::where('admission_no', 'like', "ADM{$year}%")
                          ->orderBy('admission_no', 'desc')
                          ->first();
        
        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->admission_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return "ADM{$year}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function generateStudentNumber(): string
    {
        $lastStudent = self::orderBy('student_id', 'desc')->first();
        
        if ($lastStudent && $lastStudent->student_id) {
            $lastNumber = (int) substr($lastStudent->student_id, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return 'STU' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    public static function generateInternationalStudentId(): string
    {
        $country = 'LR'; // Liberia country code
        $year = date('y');
        $lastStudent = self::where('international_student_id', 'like', "{$country}{$year}%")
                          ->orderBy('international_student_id', 'desc')
                          ->first();
        
        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->international_student_id, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return "{$country}{$year}" . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    // Auto-assignment methods (disabled - columns don't exist in database)
    /*
    public function autoAssignSubjectsAndTeachers()
    {
        if (!$this->classRoom) return;

        // Get all subjects assigned to this class
        $classSubjects = $this->classRoom->subjects()->with('teacher')->get();
        
        $assignedSubjects = [];
        $assignedTeachers = [];
        
        foreach ($classSubjects as $subject) {
            $assignedSubjects[] = [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
                'credits' => $subject->credits ?? 1,
            ];
            
            if ($subject->teacher) {
                $assignedTeachers[] = [
                    'subject_id' => $subject->id,
                    'teacher_id' => $subject->teacher->id,
                    'teacher_name' => $subject->teacher->user->name,
                    'employee_id' => $subject->teacher->employee_id,
                ];
            }
        }
        
        $this->update([
            'assigned_subjects' => $assignedSubjects,
            'assigned_teachers' => $assignedTeachers,
        ]);
    }

    public function autoAssignFeeStructure()
    {
        if (!$this->classRoom) return;

        $feeStructure = ClassFeeStructure::where('class_id', $this->class_id)
                                       ->where('academic_year', $this->academic_year)
                                       ->where('is_active', true)
                                       ->first();
        
        if ($feeStructure) {
            $this->update([
                'total_fees' => $feeStructure->total_fees,
                'paid_fees' => 0,
                'balance_fees' => $feeStructure->total_fees,
            ]);
        }
    }
    */

    // Fee management methods (disabled - columns don't exist in database)
    /*
    public function recordPayment($amount, $paymentType = 'tuition', $approvedBy = null)
    {
        $newPaidAmount = $this->paid_fees + $amount;
        $newBalance = $this->total_fees - $newPaidAmount;
        
        $this->update([
            'paid_fees' => $newPaidAmount,
            'balance_fees' => max(0, $newBalance),
            'last_payment_date' => now(),
            'payment_status' => $newBalance <= 0 ? 'paid' : 'partial',
        ]);

        // Log the payment
        if (auth()->check()) {
            StudentActivityLog::logFeePayment($this, $amount, $paymentType, auth()->user());
        }

        return $this;
    }

    public function hasPendingFees(): bool
    {
        return $this->balance_fees > 0;
    }
    */

    // Display helpers
    public function getDisplayName(): string
    {
        return $this->user->name ?? ($this->first_name . ' ' . $this->last_name);
    }

    public function getFormattedAdmissionNumber(): string
    {
        return $this->admission_no ?? 'Not Assigned';
    }

    public function getFormattedStudentNumber(): string
    {
        return $this->student_id ?? 'Not Assigned';
    }
} 