<?php

namespace App\Helpers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Subject;

class GradeHelper
{
    /**
     * Get letter grade based on numeric score
     */
    public static function getLetterGrade(?float $score): string
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
    public static function getGradeColorClass(?float $score): string
    {
        if ($score === null) return 'bg-gray-100 text-gray-800';
        
        if ($score >= 90) return 'bg-green-100 text-green-800';
        if ($score >= 80) return 'bg-blue-100 text-blue-800';
        if ($score >= 70) return 'bg-yellow-100 text-yellow-800';
        if ($score >= 60) return 'bg-orange-100 text-orange-800';
        if ($score >= 50) return 'bg-red-100 text-red-800';
        return 'bg-red-200 text-red-900';
    }

    /**
     * Calculate student's overall average for a class
     */
    public static function calculateStudentOverallAverage(Student $student, int $academicYear, int $classId): ?float
    {
        $grades = Grade::where('student_id', $student->id)
                      ->where('class_id', $classId)
                      ->where('academic_year', $academicYear)
                      ->where('status', 'approved')
                      ->whereNotNull('year_avg')
                      ->get();

        if ($grades->isEmpty()) {
            return null;
        }

        $totalScore = $grades->sum('year_avg');
        $subjectCount = $grades->count();

        return $subjectCount > 0 ? round($totalScore / $subjectCount, 2) : null;
    }

    /**
     * Check if student is eligible for promotion
     */
    public static function isEligibleForPromotion(Student $student, int $academicYear, int $classId): bool
    {
        $overallAverage = self::calculateStudentOverallAverage($student, $academicYear, $classId);
        return $overallAverage !== null && $overallAverage >= 70.0;
    }

    /**
     * Get all subjects for a class
     */
    public static function getClassSubjects(int $classId): \Illuminate\Database\Eloquent\Collection
    {
        return Subject::whereHas('classes', function($query) use ($classId) {
            $query->where('class_rooms.id', $classId);
        })->get();
    }

    /**
     * Ensure student has grade records for all class subjects
     */
    public static function ensureStudentHasAllSubjectGrades(Student $student, int $academicYear, int $classId): void
    {
        $classSubjects = self::getClassSubjects($classId);
        
        foreach ($classSubjects as $subject) {
            // Check if grade record exists for this student/subject/class combination
            $existingGrade = Grade::where('student_id', $student->id)
                                ->where('class_id', $classId)
                                ->where('subject_id', $subject->id)
                                ->where('academic_year', $academicYear)
                                ->first();

            if (!$existingGrade) {
                // Only create if subject has a teacher assigned
                if ($subject->teacher_id) {
                    Grade::create([
                        'student_id' => $student->id,
                        'class_id' => $classId,
                        'subject_id' => $subject->id,
                        'teacher_id' => $subject->teacher_id,
                        'academic_year' => $academicYear,
                        'semester' => 1,
                        'status' => 'draft',
                    ]);
                }
            }
        }
    }

    /**
     * Get student's grade summary for a class
     */
    public static function getStudentGradeSummary(Student $student, int $academicYear, int $classId): array
    {
        $grades = Grade::where('student_id', $student->id)
                      ->where('class_id', $classId)
                      ->where('academic_year', $academicYear)
                      ->where('status', 'approved')
                      ->with(['subject', 'teacher.user'])
                      ->get();

        $summary = [
            'total_subjects' => $grades->count(),
            'subjects_with_grades' => $grades->whereNotNull('year_avg')->count(),
            'overall_average' => self::calculateStudentOverallAverage($student, $academicYear, $classId),
            'semester1_average' => null,
            'semester2_average' => null,
            'is_eligible_for_promotion' => false,
            'grades_by_subject' => [],
            'period_averages' => [
                'period_1' => null,
                'period_2' => null,
                'period_3' => null,
                'period_4' => null,
                'period_5' => null,
                'period_6' => null,
            ]
        ];

        if ($grades->isNotEmpty()) {
            // Calculate semester averages
            $sem1Grades = $grades->whereNotNull('sem1_avg');
            $sem2Grades = $grades->whereNotNull('sem2_avg');

            $summary['semester1_average'] = $sem1Grades->isNotEmpty() ? 
                round($sem1Grades->avg('sem1_avg'), 2) : null;
            $summary['semester2_average'] = $sem2Grades->isNotEmpty() ? 
                round($sem2Grades->avg('sem2_avg'), 2) : null;

            // Check promotion eligibility
            $summary['is_eligible_for_promotion'] = self::isEligibleForPromotion($student, $academicYear, $classId);

            // Calculate period averages across all subjects
            for ($period = 1; $period <= 6; $period++) {
                $periodGrades = collect();
                foreach ($grades as $grade) {
                    $periodAvg = $grade->calculatePeriodAverage($period);
                    if ($periodAvg !== null) {
                        $periodGrades->push($periodAvg);
                    }
                }
                $summary['period_averages']['period_' . $period] = $periodGrades->isNotEmpty() ? 
                    round($periodGrades->avg(), 2) : null;
            }

            // Group grades by subject
            foreach ($grades as $grade) {
                $summary['grades_by_subject'][] = [
                    'subject' => $grade->subject->name,
                    'teacher' => $grade->teacher->user->name,
                    'sem1_avg' => $grade->sem1_avg,
                    'sem2_avg' => $grade->sem2_avg,
                    'year_avg' => $grade->year_avg,
                    'letter_grade' => self::getLetterGrade($grade->year_avg),
                    'periods' => $grade->getSemesterPeriods(1) + $grade->getSemesterPeriods(2),
                ];
            }
        }

        return $summary;
    }

    /**
     * Get class grade statistics
     */
    public static function getClassGradeStatistics(int $classId, int $academicYear): array
    {
        $grades = Grade::where('class_id', $classId)
                      ->where('academic_year', $academicYear)
                      ->where('status', 'approved')
                      ->get();

        $students = Student::where('class_id', $classId)->get();
        $classSubjects = self::getClassSubjects($classId);

        $statistics = [
            'total_students' => $students->count(),
            'total_subjects' => $classSubjects->count(),
            'total_grades' => $grades->count(),
            'average_class_score' => null,
            'grade_distribution' => [
                'A+' => 0, 'A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D' => 0
            ],
            'promotion_eligible' => 0,
            'subject_averages' => [],
        ];

        if ($grades->isNotEmpty()) {
            // Calculate class average
            $validGrades = $grades->whereNotNull('year_avg');
            $statistics['average_class_score'] = $validGrades->isNotEmpty() ? 
                round($validGrades->avg('year_avg'), 2) : null;

            // Grade distribution
            foreach ($validGrades as $grade) {
                $letterGrade = self::getLetterGrade($grade->year_avg);
                if (isset($statistics['grade_distribution'][$letterGrade])) {
                    $statistics['grade_distribution'][$letterGrade]++;
                }
            }

            // Count promotion eligible students
            foreach ($students as $student) {
                if (self::isEligibleForPromotion($student, $academicYear, $classId)) {
                    $statistics['promotion_eligible']++;
                }
            }

            // Subject averages
            foreach ($classSubjects as $subject) {
                $subjectGrades = $grades->where('subject_id', $subject->id)->whereNotNull('year_avg');
                $statistics['subject_averages'][$subject->name] = $subjectGrades->isNotEmpty() ? 
                    round($subjectGrades->avg('year_avg'), 2) : null;
            }
        }

        return $statistics;
    }
}
