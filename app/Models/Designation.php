<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'hierarchy_level',
        'base_salary',
        'currency',
        'responsibilities',
        'qualifications_required',
        'experience_required',
        'status',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'hierarchy_level' => 'integer',
        'base_salary' => 'decimal:2',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('hierarchy_level')->orderBy('display_order')->orderBy('name');
    }
}
