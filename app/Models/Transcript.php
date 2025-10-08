<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transcript extends Model
{
    use HasFactory;

    protected $fillable = [
        'transcript_number', 'student_id', 'class_id', 'academic_year', 'semester',
        'type', 'status', 'gpa', 'cgpa', 'total_credits', 'earned_credits',
        'total_subjects', 'passed_subjects', 'failed_subjects',
        'class_rank', 'grade_rank', 'total_students_in_class', 'total_students_in_grade', 'percentile',
        'a_grades', 'b_grades', 'c_grades', 'd_grades', 'f_grades', 'incomplete_grades',
        'academic_standing', 'academic_honors', 'disciplinary_actions',
        'total_days', 'days_present', 'days_absent', 'attendance_percentage',
        'generation_date', 'issue_date', 'valid_until',
        'generated_at', 'approved_at', 'issued_at',
        'generated_by', 'approved_by', 'issued_by',
        'approver_signature', 'registrar_signature',
        'pdf_path', 'excel_path', 'watermark', 'is_sealed', 'seal_path',
        'notes', 'internal_notes', 'metadata', 'grade_data'
    ];

    protected $casts = [
        'gpa' => 'decimal:2',
        'cgpa' => 'decimal:2',
        'percentile' => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'generation_date' => 'date',
        'issue_date' => 'date',
        'valid_until' => 'date',
        'generated_at' => 'datetime',
        'approved_at' => 'datetime',
        'issued_at' => 'datetime',
        'is_sealed' => 'boolean',
        'metadata' => 'array',
        'grade_data' => 'array'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function transcriptGrades(): HasMany
    {
        return $this->hasMany(TranscriptGrade::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeOfficial($query)
    {
        return $query->where('type', 'official');
    }

    public function scopeUnofficial($query)
    {
        return $query->where('type', 'unofficial');
    }

    public function scopeGenerated($query)
    {
        return $query->where('status', 'generated');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'generated' => 'blue',
            'approved' => 'green',
            'issued' => 'green',
            'archived' => 'gray',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'generated' => 'Generated',
            'approved' => 'Approved',
            'issued' => 'Issued',
            'archived' => 'Archived',
            default => 'Unknown'
        };
    }

    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'official' => 'Official',
            'unofficial' => 'Unofficial',
            'interim' => 'Interim',
            default => 'Unknown'
        };
    }

    public function getAcademicStandingTextAttribute()
    {
        return match($this->academic_standing) {
            'excellent' => 'Excellent',
            'good' => 'Good',
            'satisfactory' => 'Satisfactory',
            'needs_improvement' => 'Needs Improvement',
            'unsatisfactory' => 'Unsatisfactory',
            default => 'Not Available'
        };
    }

    public function getAcademicStandingColorAttribute()
    {
        return match($this->academic_standing) {
            'excellent' => 'green',
            'good' => 'blue',
            'satisfactory' => 'yellow',
            'needs_improvement' => 'orange',
            'unsatisfactory' => 'red',
            default => 'gray'
        };
    }

    public function generateTranscriptNumber()
    {
        $year = now()->year;
        $prefix = 'TRN';
        
        $lastTranscript = self::whereYear('generation_date', $year)
            ->where('transcript_number', 'like', $prefix . $year . '%')
            ->orderBy('transcript_number', 'desc')
            ->first();

        if ($lastTranscript) {
            $lastNumber = (int) substr($lastTranscript->transcript_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function calculateGPA()
    {
        $grades = $this->transcriptGrades()->where('status', 'passed')->get();
        
        if ($grades->isEmpty()) {
            return 0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($grades as $grade) {
            $totalPoints += $grade->grade_points * $grade->credits;
            $totalCredits += $grade->credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;
    }

    public function calculateRanking()
    {
        // Calculate class ranking
        $classStudents = self::where('class_id', $this->class_id)
            ->where('academic_year', $this->academic_year)
            ->where('status', '!=', 'draft')
            ->orderBy('gpa', 'desc')
            ->get();

        $rank = 1;
        foreach ($classStudents as $student) {
            if ($student->id === $this->id) {
                $this->class_rank = $rank;
                $this->total_students_in_class = $classStudents->count();
                break;
            }
            $rank++;
        }

        // Calculate percentile
        if ($this->total_students_in_class > 0) {
            $this->percentile = round((($this->total_students_in_class - $this->class_rank + 1) / $this->total_students_in_class) * 100, 2);
        }

        $this->save();
    }

    public function calculateGradeSummary()
    {
        $grades = $this->transcriptGrades;
        
        $this->a_grades = $grades->where('grade_letter', 'A')->count();
        $this->b_grades = $grades->where('grade_letter', 'B')->count();
        $this->c_grades = $grades->where('grade_letter', 'C')->count();
        $this->d_grades = $grades->where('grade_letter', 'D')->count();
        $this->f_grades = $grades->where('grade_letter', 'F')->count();
        $this->incomplete_grades = $grades->where('grade_letter', 'I')->count();
        
        $this->total_subjects = $grades->count();
        $this->passed_subjects = $grades->whereIn('grade_letter', ['A', 'B', 'C', 'D'])->count();
        $this->failed_subjects = $grades->where('grade_letter', 'F')->count();
        
        $this->total_credits = $grades->sum('credits');
        $this->earned_credits = $grades->whereIn('grade_letter', ['A', 'B', 'C', 'D'])->sum('credits');
        
        $this->save();
    }

    public function determineAcademicStanding()
    {
        $gpa = $this->gpa ?? $this->calculateGPA();
        
        if ($gpa >= 3.5) {
            $this->academic_standing = 'excellent';
        } elseif ($gpa >= 3.0) {
            $this->academic_standing = 'good';
        } elseif ($gpa >= 2.0) {
            $this->academic_standing = 'satisfactory';
        } elseif ($gpa >= 1.0) {
            $this->academic_standing = 'needs_improvement';
        } else {
            $this->academic_standing = 'unsatisfactory';
        }
        
        $this->save();
    }

    public function generate()
    {
        $this->update([
            'status' => 'generated',
            'generated_at' => now(),
            'generated_by' => auth()->id()
        ]);
    }

    public function approve($approverId = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approverId ?? auth()->id()
        ]);
    }

    public function issue($issuerId = null)
    {
        $this->update([
            'status' => 'issued',
            'issued_at' => now(),
            'issued_by' => $issuerId ?? auth()->id(),
            'issue_date' => now()->toDateString()
        ]);
    }

    public function archive()
    {
        $this->update(['status' => 'archived']);
    }

    public function isOfficial()
    {
        return $this->type === 'official';
    }

    public function isGenerated()
    {
        return $this->status === 'generated';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isIssued()
    {
        return $this->status === 'issued';
    }

    public function canBeApproved()
    {
        return $this->status === 'generated';
    }

    public function canBeIssued()
    {
        return $this->status === 'approved';
    }

    public function canBeArchived()
    {
        return in_array($this->status, ['issued', 'approved']);
    }

    public static function generateForStudent($studentId, $academicYear, $semester = null, $type = 'official')
    {
        $student = Student::with(['user', 'classRoom'])->find($studentId);
        
        if (!$student) {
            throw new \Exception('Student not found');
        }

        // Check if transcript already exists
        $existingTranscript = self::where('student_id', $studentId)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('type', $type)
            ->first();

        if ($existingTranscript) {
            return $existingTranscript;
        }

        // Get approved grades for the student
        $grades = Grade::where('student_id', $studentId)
            ->where('status', 'approved')
            ->where('academic_year', $academicYear)
            ->when($semester, function($query) use ($semester) {
                return $query->where('semester', $semester);
            })
            ->with(['subject', 'teacher'])
            ->get();

        if ($grades->isEmpty()) {
            throw new \Exception('No approved grades found for the specified period');
        }

        // Create transcript
        $transcript = self::create([
            'transcript_number' => self::generateTranscriptNumber(),
            'student_id' => $studentId,
            'class_id' => $student->class_id,
            'academic_year' => $academicYear,
            'semester' => $semester,
            'type' => $type,
            'status' => 'draft',
            'generation_date' => now()->toDateString(),
            'metadata' => [
                'generated_via' => 'system',
                'grade_count' => $grades->count()
            ]
        ]);

        // Create transcript grades
        foreach ($grades as $grade) {
            TranscriptGrade::create([
                'transcript_id' => $transcript->id,
                'subject_id' => $grade->subject_id,
                'subject_code' => $grade->subject->code ?? null,
                'subject_name' => $grade->subject->name,
                'credits' => $grade->subject->credits ?? 1,
                'grade_letter' => $grade->grade,
                'grade_points' => self::convertGradeToPoints($grade->grade),
                'percentage' => $grade->percentage,
                'semester' => $grade->semester,
                'academic_year' => $grade->academic_year,
                'status' => self::determineGradeStatus($grade->grade),
                'grade_date' => $grade->created_at->toDateString(),
                'teacher_id' => $grade->teacher_id
            ]);
        }

        // Calculate transcript statistics
        $transcript->calculateGradeSummary();
        $transcript->gpa = $transcript->calculateGPA();
        $transcript->determineAcademicStanding();
        $transcript->calculateRanking();
        $transcript->generate();

        return $transcript;
    }

    private static function convertGradeToPoints($grade)
    {
        return match(strtoupper($grade)) {
            'A+' => 4.0,
            'A' => 4.0,
            'A-' => 3.7,
            'B+' => 3.3,
            'B' => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C' => 2.0,
            'C-' => 1.7,
            'D+' => 1.3,
            'D' => 1.0,
            'F' => 0.0,
            default => 0.0
        };
    }

    private static function determineGradeStatus($grade)
    {
        return match(strtoupper($grade)) {
            'A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D' => 'passed',
            'F' => 'failed',
            'I' => 'incomplete',
            'W' => 'withdrawn',
            default => 'passed'
        };
    }
}
