<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'class_id', 'subject_id', 'teacher_id',
        'academic_year', 'semester',
        // Semester 1
        'sem1_p1', 'sem1_p2', 'sem1_p3', 'sem1_exam', 'sem1_avg',
        // Semester 2
        'sem2_p4', 'sem2_p5', 'sem2_p6', 'sem2_exam', 'sem2_avg',
        // Yearly summary
        'year_avg', 'status', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'academic_year' => 'integer',
        'semester' => 'integer',
        'sem1_p1' => 'decimal:2', 'sem1_p2' => 'decimal:2', 'sem1_p3' => 'decimal:2', 
        'sem1_exam' => 'decimal:2', 'sem1_avg' => 'decimal:2',
        'sem2_p4' => 'decimal:2', 'sem2_p5' => 'decimal:2', 'sem2_p6' => 'decimal:2',
        'sem2_exam' => 'decimal:2', 'sem2_avg' => 'decimal:2',
        'year_avg' => 'decimal:2',
        'approved_at' => 'datetime',
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

    // Note: academic_period relationship removed due to table compatibility issues
    // public function academicPeriod()
    // {
    //     return $this->belongsTo(AcademicPeriod::class);
    // }

    public function calculateSemesterAverages(): void
    {
        // Calculate Semester 1 average
        $sem1Periods = collect([
            $this->sem1_p1, $this->sem1_p2, $this->sem1_p3, $this->sem1_exam
        ])->filter(fn($v) => $v !== null);
        
        $this->sem1_avg = $sem1Periods->count() ? round($sem1Periods->avg(), 2) : null;
        
        // Calculate Semester 2 average
        $sem2Periods = collect([
            $this->sem2_p4, $this->sem2_p5, $this->sem2_p6, $this->sem2_exam
        ])->filter(fn($v) => $v !== null);
        
        $this->sem2_avg = $sem2Periods->count() ? round($sem2Periods->avg(), 2) : null;
        
        // Calculate yearly average
        $yearlyPeriods = collect([$this->sem1_avg, $this->sem2_avg])
            ->filter(fn($v) => $v !== null);
        
        $this->year_avg = $yearlyPeriods->count() ? round($yearlyPeriods->avg(), 2) : null;
    }

    /**
     * Calculate period average for a specific period
     */
    public function calculatePeriodAverage(int $period): ?float
    {
        $periodGrades = collect();
        
        switch ($period) {
            case 1:
                $periodGrades = collect([$this->sem1_p1])->filter(fn($v) => $v !== null);
                break;
            case 2:
                $periodGrades = collect([$this->sem1_p2])->filter(fn($v) => $v !== null);
                break;
            case 3:
                $periodGrades = collect([$this->sem1_p3])->filter(fn($v) => $v !== null);
                break;
            case 4:
                $periodGrades = collect([$this->sem2_p4])->filter(fn($v) => $v !== null);
                break;
            case 5:
                $periodGrades = collect([$this->sem2_p5])->filter(fn($v) => $v !== null);
                break;
            case 6:
                $periodGrades = collect([$this->sem2_p6])->filter(fn($v) => $v !== null);
                break;
        }
        
        return $periodGrades->count() ? round($periodGrades->avg(), 2) : null;
    }

    /**
     * Get all period grades for a semester
     */
    public function getSemesterPeriods(int $semester): array
    {
        if ($semester === 1) {
            return [
                'p1' => $this->sem1_p1,
                'p2' => $this->sem1_p2,
                'p3' => $this->sem1_p3,
                'exam' => $this->sem1_exam,
                'average' => $this->sem1_avg
            ];
        } else {
            return [
                'p4' => $this->sem2_p4,
                'p5' => $this->sem2_p5,
                'p6' => $this->sem2_p6,
                'exam' => $this->sem2_exam,
                'average' => $this->sem2_avg
            ];
        }
    }

    /**
     * Check if student is eligible for promotion (70+ average)
     */
    public function isEligibleForPromotion(): bool
    {
        return $this->year_avg !== null && $this->year_avg >= 70.0;
    }

    /**
     * Get letter grade based on numeric score
     */
    public function getLetterGrade(?float $score): string
    {
        if ($score === null) return 'N/A';
        
        if ($score >= 90) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B+';
        if ($score >= 60) return 'B';
        if ($score >= 50) return 'C+';
        if ($score >= 40) return 'C';
        return 'D';
    }

    /**
     * Get grade color class for UI
     */
    public function getGradeColorClass(?float $score): string
    {
        if ($score === null) return 'bg-gray-100 text-gray-800';
        
        if ($score >= 90) return 'bg-green-100 text-green-800';
        if ($score >= 80) return 'bg-blue-100 text-blue-800';
        if ($score >= 70) return 'bg-yellow-100 text-yellow-800';
        if ($score >= 60) return 'bg-orange-100 text-orange-800';
        if ($score >= 50) return 'bg-red-100 text-red-800';
        return 'bg-red-200 text-red-900';
    }
}