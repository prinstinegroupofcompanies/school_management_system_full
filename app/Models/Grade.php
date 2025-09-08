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
        'sem1_p1','sem1_p2','sem1_p3','sem1_exam','sem1_avg',
        'sem2_p4','sem2_p5','sem2_p6','sem2_exam','sem2_avg',
        'year_avg','status','approved_by','approved_at','is_promoted','honors_status',
    ];

    protected $casts = [
        'academic_year' => 'integer',
        'semester' => 'integer',
        'sem1_p1' => 'integer','sem1_p2' => 'integer','sem1_p3' => 'integer','sem1_exam' => 'integer',
        'sem2_p4' => 'integer','sem2_p5' => 'integer','sem2_p6' => 'integer','sem2_exam' => 'integer',
        'sem1_avg' => 'decimal:2',
        'sem2_avg' => 'decimal:2',
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

    public function calculateSemesterAverages(): void
    {
        $sem1 = collect([$this->sem1_p1, $this->sem1_p2, $this->sem1_p3, $this->sem1_exam])->filter(fn($v) => $v !== null);
        $sem2 = collect([$this->sem2_p4, $this->sem2_p5, $this->sem2_p6, $this->sem2_exam])->filter(fn($v) => $v !== null);
        $this->sem1_avg = $sem1->count() ? round($sem1->avg(), 2) : null;
        $this->sem2_avg = $sem2->count() ? round($sem2->avg(), 2) : null;
        if ($this->sem1_avg !== null || $this->sem2_avg !== null) {
            $present = collect([$this->sem1_avg, $this->sem2_avg])->filter(fn($v) => $v !== null);
            $this->year_avg = $present->count() ? round($present->avg(), 2) : null;
        } else {
            $this->year_avg = null;
        }
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


