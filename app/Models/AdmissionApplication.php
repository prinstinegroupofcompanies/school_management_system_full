<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdmissionApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_number', 'first_name', 'last_name', 'middle_name',
        'date_of_birth', 'gender', 'nationality', 'place_of_birth', 'religion',
        'phone_number', 'email', 'address', 'city', 'state', 'postal_code',
        'applying_class_id', 'previous_school', 'previous_school_address',
        'previous_class', 'previous_gpa', 'academic_achievements',
        'parent_first_name', 'parent_last_name', 'parent_middle_name',
        'parent_phone', 'parent_email', 'parent_address', 'parent_occupation',
        'parent_employer', 'relationship_to_student',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
        'required_documents', 'submitted_documents', 'document_paths',
        'status', 'rejection_reason', 'notes', 'internal_notes',
        'application_date', 'review_deadline', 'decision_date', 'enrollment_deadline',
        'requires_entrance_exam', 'entrance_exam_date', 'entrance_exam_time',
        'entrance_exam_venue', 'entrance_exam_score', 'entrance_exam_notes',
        'requires_interview', 'interview_date', 'interview_time', 'interview_venue',
        'interview_notes', 'interview_score',
        'application_fee', 'application_fee_paid', 'application_fee_payment_date', 'payment_reference',
        'special_needs', 'medical_conditions', 'allergies', 'medications',
        'extracurricular_activities', 'hobbies', 'why_choose_school',
        'created_by', 'reviewed_by', 'approved_by',
        'submitted_at', 'reviewed_at', 'approved_at', 'rejected_at', 'enrolled_at',
        'metadata'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'application_date' => 'date',
        'review_deadline' => 'date',
        'decision_date' => 'date',
        'enrollment_deadline' => 'date',
        'entrance_exam_date' => 'date',
        'entrance_exam_time' => 'datetime',
        'interview_date' => 'date',
        'interview_time' => 'datetime',
        'application_fee_payment_date' => 'date',
        'previous_gpa' => 'decimal:2',
        'entrance_exam_score' => 'decimal:2',
        'interview_score' => 'decimal:2',
        'application_fee' => 'decimal:2',
        'requires_entrance_exam' => 'boolean',
        'requires_interview' => 'boolean',
        'application_fee_paid' => 'boolean',
        'required_documents' => 'array',
        'submitted_documents' => 'array',
        'document_paths' => 'array',
        'metadata' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'enrolled_at' => 'datetime'
    ];

    public function applyingClass(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'applying_class_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(AdmissionApproval::class);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'under_review' => 'yellow',
            'first_level_approved' => 'green',
            'second_level_approved' => 'green',
            'rejected' => 'red',
            'accepted' => 'green',
            'enrolled' => 'green',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'first_level_approved' => 'First Level Approved',
            'second_level_approved' => 'Second Level Approved',
            'rejected' => 'Rejected',
            'accepted' => 'Accepted',
            'enrolled' => 'Enrolled',
            default => 'Unknown'
        };
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
    }

    public function getParentFullNameAttribute()
    {
        return trim($this->parent_first_name . ' ' . ($this->parent_middle_name ? $this->parent_middle_name . ' ' : '') . $this->parent_last_name);
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function generateApplicationNumber()
    {
        $year = now()->year;
        $prefix = 'APP';
        
        $lastApplication = self::whereYear('application_date', $year)
            ->where('application_number', 'like', $prefix . $year . '%')
            ->orderBy('application_number', 'desc')
            ->first();

        if ($lastApplication) {
            $lastNumber = (int) substr($lastApplication->application_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function submit()
    {
        if ($this->status !== 'draft' || !$this->application_fee_paid) {
            throw new \Exception('Application cannot be submitted in current status');
        }

        $this->update([
            'status' => 'submitted',
            'submitted_at' => now()
        ]);
    }

    public function approve($level = 'first_level', $approverId = null, $comments = null)
    {
        $status = $level === 'first_level' ? 'first_level_approved' : 'second_level_approved';
        
        $this->update([
            'status' => $status,
            'approved_at' => now(),
            'approved_by' => $approverId
        ]);

        AdmissionApproval::create([
            'admission_application_id' => $this->id,
            'approver_id' => $approverId,
            'approval_level' => $level,
            'status' => 'approved',
            'comments' => $comments,
            'approved_at' => now()
        ]);

        if ($level === 'second_level') {
            $this->update(['status' => 'accepted']);
        }
    }

    public function reject($reason = null, $rejectorId = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => now(),
            'reviewed_by' => $rejectorId
        ]);
    }
}
