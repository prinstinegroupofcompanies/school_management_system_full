<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'total_marks',
        'passing_marks',
        'duration_minutes',
        'is_compulsory',
        'counts_for_final',
        'weightage_percentage',
        'status',
        'is_active',
    ];

    protected $casts = [
        'total_marks' => 'integer',
        'passing_marks' => 'decimal:2',
        'duration_minutes' => 'integer',
        'is_compulsory' => 'boolean',
        'counts_for_final' => 'boolean',
        'weightage_percentage' => 'integer',
        'is_active' => 'boolean',
    ];

    public function examSchedules(): HasMany
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCompulsory($query)
    {
        return $query->where('is_compulsory', true);
    }

    public function scopeCountsForFinal($query)
    {
        return $query->where('counts_for_final', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function getTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->type));
    }

    public function getDurationDisplayAttribute(): string
    {
        if ($this->duration_minutes) {
            $hours = intval($this->duration_minutes / 60);
            $minutes = $this->duration_minutes % 60;
            
            if ($hours > 0 && $minutes > 0) {
                return "{$hours}h {$minutes}m";
            } elseif ($hours > 0) {
                return "{$hours}h";
            } else {
                return "{$minutes}m";
            }
        }
        return 'No time limit';
    }

    public function getPassPercentageAttribute(): float
    {
        if ($this->total_marks == 0) return 0;
        return round(($this->passing_marks / $this->total_marks) * 100, 2);
    }
}
