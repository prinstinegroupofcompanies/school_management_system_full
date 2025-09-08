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
        'contract_end_date',
        'salary',
        'bank_name',
        'bank_account_no',
        'emergency_contact',
        'emergency_contact_relation',
        'is_active',
        'profile_photo',
        'signature',
        'bio',
        'social_media_links',
        'awards_achievements',
        'certifications',
        'languages_known',
        'interests_hobbies',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'contract_end_date' => 'date',
        'salary' => 'decimal:2',
        'experience_years' => 'integer',
        'is_active' => 'boolean',
        'social_media_links' => 'array',
        'languages_known' => 'array',
        'interests_hobbies' => 'array',
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

    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
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
        return $this->user->phone;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
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