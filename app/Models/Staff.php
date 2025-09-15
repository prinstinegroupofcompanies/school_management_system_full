<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'department_id',
        'designation_id',
        'qualification',
        'specialization',
        'experience_years',
        'joining_date',
        'contract_start_date',
        'contract_end_date',
        'employment_type',
        'employment_status',
        'basic_salary',
        'salary_currency',
        'bank_name',
        'bank_account_number',
        'bank_branch',
        'tax_identification_number',
        'social_security_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'emergency_contact_address',
        'documents',
        'certifications',
        'skills',
        'bio',
        'achievements',
        'notes',
        'profile_photo',
        'signature',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'basic_salary' => 'decimal:2',
        'experience_years' => 'integer',
        'documents' => 'array',
        'certifications' => 'array',
        'skills' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    // Note: StaffAttendance and LeaveRequest models not implemented yet
    // public function attendances(): HasMany
    // {
    //     return $this->hasMany(StaffAttendance::class);
    // }

    // public function leaves(): HasMany
    // {
    //     return $this->hasMany(LeaveRequest::class);
    // }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function performances(): HasMany
    {
        return $this->hasMany(StaffPerformance::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(StaffSchedule::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    public function getEmailAttribute(): string
    {
        return $this->user->email;
    }

    public function getPhoneAttribute(): string
    {
        return $this->user->phone ?? '';
    }

    public function scopeActive($query)
    {
        return $query->where('employment_status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('employment_status', '!=', 'active');
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByDesignation($query, $designationId)
    {
        return $query->where('designation_id', $designationId);
    }
} 