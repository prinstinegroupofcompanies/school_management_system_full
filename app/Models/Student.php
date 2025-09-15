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
} 