<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transcript extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year',
        'terms_data',
        'cgpa',
        'remarks',
        'generated_by',
        'generated_at',
        'generation_date',
        'transcript_number',
    ];

    protected $casts = [
        'terms_data' => 'array',
        'cgpa' => 'float',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who generated the transcript.
     */
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Calculate CGPA from terms data.
     */
    public static function calculateCGPA(array $termsData): float
    {
        $totalGPA = 0;
        $termCount = 0;
        
        foreach ($termsData as $term) {
            if (isset($term['gpa'])) {
                $totalGPA += $term['gpa'];
                $termCount++;
            }
        }
        
        return $termCount > 0 ? $totalGPA / $termCount : 0.0;
    }

    /**
     * Generate transcript data for a student.
     */
    public static function generateForStudent(Student $student, int $academicYear): self
    {
        $termsData = [];
        $terms = ['Term 1', 'Term 2', 'Term 3'];
        
        foreach ($terms as $term) {
            $grades = Grade::where('student_id', $student->id)
                ->where('term', $term)
                ->where('year', $academicYear)
                ->with('subject')
                ->get();
            
            $termGrades = [];
            $termGPA = 0;

        foreach ($grades as $grade) {
                $termGrades[] = [
                    'subject' => $grade->subject->name ?? '',
                    'mid_term' => $grade->mid_term_score ?? 0,
                    'final' => $grade->final_score ?? 0,
                    'total' => $grade->total_score ?? 0,
                    'grade' => $grade->letter_grade ?? 'F',
                ];
                
                $termGPA += $grade->gpa ?? 0;
            }
            
            $termCount = count($termGrades);
            $termsData[] = [
                'term' => $term,
                'subjects' => $termGrades,
                'gpa' => $termCount > 0 ? $termGPA / $termCount : 0.0,
            ];
        }
        
        $cgpa = self::calculateCGPA($termsData);
        
        return self::create([
            'student_id' => $student->id,
            'academic_year' => $academicYear,
            'terms_data' => $termsData,
            'cgpa' => $cgpa,
            'generated_by' => auth()->id() ?? 1,
            'generated_at' => now(),
        ]);
    }

    /**
     * Simple transcript generation: uses grades by semester and builds terms_data.
     * Works with the existing transcripts table (sets transcript_number, class_id, generation_date when present).
     */
    public static function generateSimpleTranscript(Student $student, int $academicYear): self
    {
        $termsData = [];

        // Semester 1 (Term 1)
        $sem1Grades = Grade::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('semester', 1)
            ->where('status', 'approved')
            ->with('subject')
            ->orderBy('subject_id')
            ->get();
        $termsData[] = self::buildTermBlock('Semester 1 (Term 1)', $sem1Grades);

        // Semester 2 (Term 2)
        $sem2Grades = Grade::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('semester', 2)
            ->where('status', 'approved')
            ->with('subject')
            ->orderBy('subject_id')
            ->get();
        $termsData[] = self::buildTermBlock('Semester 2 (Term 2)', $sem2Grades);

        // Year summary: one row per subject (use first occurrence per subject_id for year_avg)
        $yearGrades = Grade::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('status', 'approved')
            ->with('subject')
            ->orderBy('subject_id')
            ->get()
            ->groupBy('subject_id')
            ->map(fn ($group) => $group->first())
            ->values();
        $termsData[] = self::buildTermBlock('Year Summary', $yearGrades, true);

        $cgpa = self::calculateCGPA($termsData);

        $academicYearStr = (string) $academicYear . '-' . ((int) $academicYear + 1);
        $attrs = [
            'student_id' => $student->id,
            'academic_year' => $academicYearStr,
            'terms_data' => $termsData,
            'cgpa' => $cgpa,
            'generated_by' => auth()->id() ?? 1,
            'generated_at' => now(),
        ];

        $columns = \Illuminate\Support\Facades\Schema::getColumnListing((new self)->getTable());
        if (in_array('class_id', $columns) && $student->class_id) {
            $attrs['class_id'] = $student->class_id;
        }
        if (in_array('generation_date', $columns)) {
            $attrs['generation_date'] = now()->format('Y-m-d');
        }
        if (in_array('transcript_number', $columns)) {
            $attrs['transcript_number'] = 'TR-' . $student->id . '-' . $academicYear . '-' . uniqid();
        }

        return self::create($attrs);
    }

    /**
     * Build one term block for terms_data from a collection of grades.
     */
    protected static function buildTermBlock(string $termLabel, $grades, bool $useYearAvg = false): array
    {
        $subjects = [];
        $sum = 0;
        $count = 0;

        foreach ($grades as $grade) {
            $total = $useYearAvg ? ($grade->year_avg ?? 0) : ($grade->year_avg ?? ($grade->sem1_avg ?? 0) + ($grade->sem2_avg ?? 0) / 2);
            if ($total && $total > 0) {
                $sum += (float) $total;
                $count++;
            }
            $subjects[] = [
                'subject' => $grade->subject->name ?? 'N/A',
                'mid_term' => $grade->sem1_avg ?? 0,
                'final' => $grade->sem2_avg ?? 0,
                'total' => $grade->year_avg ?? 0,
                'grade' => self::calculateLetterGrade((float) ($grade->year_avg ?? 0)),
            ];
        }

        return [
            'term' => $termLabel,
            'subjects' => $subjects,
            'gpa' => $count > 0 ? $sum / $count : 0.0,
        ];
    }

    /**
     * Calculate letter grade from percentage
     */
    private static function calculateLetterGrade(float $percentage): string
    {
        if ($percentage >= 97) return 'A+';
        if ($percentage >= 93) return 'A';
        if ($percentage >= 90) return 'A-';
        if ($percentage >= 87) return 'B+';
        if ($percentage >= 83) return 'B';
        if ($percentage >= 80) return 'B-';
        if ($percentage >= 77) return 'C+';
        if ($percentage >= 73) return 'C';
        if ($percentage >= 70) return 'C-';
        if ($percentage >= 67) return 'D+';
        if ($percentage >= 65) return 'D';
        return 'F';
    }
}
