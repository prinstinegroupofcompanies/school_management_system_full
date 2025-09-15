<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id','class_id','subject_id','teacher_id','academic_year','semester',
        'academic_period_id','period_1','period_2','period_3','period_4','period_5','period_6',
        'exam','period_average','year_avg','status','approved_by','approved_at','is_promoted','honors_status',
    ];

    protected $casts = [
        'academic_year' => 'integer',
        'semester' => 'integer',
        'period_1' => 'integer','period_2' => 'integer','period_3' => 'integer','period_4' => 'integer',
        'period_5' => 'integer','period_6' => 'integer','exam' => 'integer',
        'period_average' => 'decimal:2',
        'year_avg' => 'decimal:2',
        'approved_at' => 'datetime',
        'is_promoted' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function calculatePeriodAverage(): void
    {
        $periods = collect([
            $this->period_1, $this->period_2, $this->period_3, 
            $this->period_4, $this->period_5, $this->period_6, $this->exam
        ])->filter(fn($v) => $v !== null);
        
        $this->period_average = $periods->count() ? round($periods->avg(), 2) : null;
        
        // Calculate year average based on all periods
        if ($this->period_average !== null) {
            $this->year_avg = $this->period_average;
        } else {
            $this->year_avg = null;
        }
    }

    public function calculateSemesterAverages(): void
    {
        // For backward compatibility, calculate period average
        $this->calculatePeriodAverage();
    }

    public function determinePromotionAndHonors(): void
    {
        if ($this->year_avg === null) {
            $this->is_promoted = false;
            $this->honors_status = null;
            return;
        }
        $this->is_promoted = $this->year_avg >= 70;
        if ($this->year_avg >= 90) {
            $this->honors_status = 'Honor and Excellent';
        } elseif ($this->year_avg >= 80) {
            $this->honors_status = 'Honor';
        } else {
            $this->honors_status = null;
        }
    }
}


