<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guardian_id',
        'relationship',
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'date_of_birth',
        'occupation',
        'employer',
        'work_address',
        'work_phone',
        'work_email',
        'monthly_income',
        'income_currency',
        'education_level',
        'marital_status',
        'nationality',
        'religion',
        'emergency_contact',
        'emergency_contact_relation',
        'emergency_contact_phone',
        'emergency_contact_address',
        'is_primary_guardian',
        'is_emergency_contact',
        'is_financial_guardian',
        'is_authorized_pickup',
        'special_instructions',
        'medical_notes',
        'profile_photo',
        'status',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'monthly_income' => 'decimal:2',
        'is_primary_guardian' => 'boolean',
        'is_emergency_contact' => 'boolean',
        'is_financial_guardian' => 'boolean',
        'is_authorized_pickup' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'guardian_id');
    }

    public function fatherStudents(): HasMany
    {
        return $this->hasMany(Student::class, 'father_id');
    }

    public function motherStudents(): HasMany
    {
        return $this->hasMany(Student::class, 'mother_id');
    }

    public function localGuardianStudents(): HasMany
    {
        return $this->hasMany(Student::class, 'local_guardian_id');
    }

    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }

    public function getShortNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
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

    public function scopePrimary($query)
    {
        return $query->where('is_primary_guardian', true);
    }

    public function scopeByRelationship($query, $relationship)
    {
        return $query->where('relationship', $relationship);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
} 