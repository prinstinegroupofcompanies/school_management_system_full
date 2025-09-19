<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternationalGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'teacher_id',
        'class_id',
        'assessment_type',
        'assessment_title',
        'assessment_description',
        'assessment_date',
        'academic_year',
        'semester',
        'raw_score',
        'max_score',
        'percentage',
        'letter_grade',
        'gpa_points',
        'ib_grade',
        'cambridge_grade',
        'percentage_grade',
        'proficiency_level',
        'learning_standards_met',
        'teacher_comments',
        'feedback',
        'rubric_scores',
        'status',
        'approved_by',
        'submitted_at',
        'approved_at',
        'approval_notes',
        'visible_to_student',
        'visible_to_parent',
        'published_at',
        'weight',
        'counts_toward_final',
        'is_extra_credit',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'raw_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'gpa_points' => 'decimal:2',
        'percentage_grade' => 'decimal:2',
        'learning_standards_met' => 'array',
        'rubric_scores' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
        'visible_to_student' => 'boolean',
        'visible_to_parent' => 'boolean',
        'weight' => 'decimal:2',
        'counts_toward_final' => 'boolean',
        'is_extra_credit' => 'boolean',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeForAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeForSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('visible_to_student', true);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'submitted');
    }

    // Mutators and Accessors
    public function setRawScoreAttribute($value)
    {
        $this->attributes['raw_score'] = $value;
        $this->calculatePercentage();
        $this->calculateLetterGrade();
        $this->calculateGPA();
    }

    public function setMaxScoreAttribute($value)
    {
        $this->attributes['max_score'] = $value;
        $this->calculatePercentage();
        $this->calculateLetterGrade();
        $this->calculateGPA();
    }

    // Helper methods
    private function calculatePercentage()
    {
        if (isset($this->attributes['raw_score']) && isset($this->attributes['max_score']) && $this->attributes['max_score'] > 0) {
            $this->attributes['percentage'] = ($this->attributes['raw_score'] / $this->attributes['max_score']) * 100;
            $this->attributes['percentage_grade'] = $this->attributes['percentage'];
        }
    }

    private function calculateLetterGrade()
    {
        if (!isset($this->attributes['percentage'])) return;

        $percentage = $this->attributes['percentage'];
        
        // International A-F grading scale
        if ($percentage >= 97) $this->attributes['letter_grade'] = 'A+';
        elseif ($percentage >= 93) $this->attributes['letter_grade'] = 'A';
        elseif ($percentage >= 90) $this->attributes['letter_grade'] = 'A-';
        elseif ($percentage >= 87) $this->attributes['letter_grade'] = 'B+';
        elseif ($percentage >= 83) $this->attributes['letter_grade'] = 'B';
        elseif ($percentage >= 80) $this->attributes['letter_grade'] = 'B-';
        elseif ($percentage >= 77) $this->attributes['letter_grade'] = 'C+';
        elseif ($percentage >= 73) $this->attributes['letter_grade'] = 'C';
        elseif ($percentage >= 70) $this->attributes['letter_grade'] = 'C-';
        elseif ($percentage >= 67) $this->attributes['letter_grade'] = 'D+';
        elseif ($percentage >= 65) $this->attributes['letter_grade'] = 'D';
        else $this->attributes['letter_grade'] = 'F';

        // Set proficiency level
        if ($percentage >= 90) $this->attributes['proficiency_level'] = 'exceeds';
        elseif ($percentage >= 80) $this->attributes['proficiency_level'] = 'meets';
        elseif ($percentage >= 70) $this->attributes['proficiency_level'] = 'approaching';
        else $this->attributes['proficiency_level'] = 'below';
    }

    private function calculateGPA()
    {
        if (!isset($this->attributes['letter_grade'])) return;

        // 4.0 GPA scale
        $gpaMap = [
            'A+' => 4.0, 'A' => 4.0, 'A-' => 3.7,
            'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
            'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
            'D+' => 1.3, 'D' => 1.0, 'F' => 0.0
        ];

        $this->attributes['gpa_points'] = $gpaMap[$this->attributes['letter_grade']] ?? 0.0;
    }

    public function calculateIBGrade()
    {
        if (!isset($this->attributes['percentage'])) return null;

        $percentage = $this->attributes['percentage'];
        
        // IB 7-point scale
        if ($percentage >= 96) return 7;
        elseif ($percentage >= 91) return 6;
        elseif ($percentage >= 81) return 5;
        elseif ($percentage >= 71) return 4;
        elseif ($percentage >= 61) return 3;
        elseif ($percentage >= 51) return 2;
        elseif ($percentage >= 41) return 1;
        else return 0;
    }

    public function calculateCambridgeGrade()
    {
        if (!isset($this->attributes['percentage'])) return null;

        $percentage = $this->attributes['percentage'];
        
        // Cambridge International scale
        if ($percentage >= 90) return 'A*';
        elseif ($percentage >= 80) return 'A';
        elseif ($percentage >= 70) return 'B';
        elseif ($percentage >= 60) return 'C';
        elseif ($percentage >= 50) return 'D';
        elseif ($percentage >= 40) return 'E';
        else return 'U'; // Ungraded
    }

    public function submit()
    {
        $this->status = 'submitted';
        $this->submitted_at = now();
        $this->save();
    }

    public function approve($approver, $notes = null)
    {
        $this->status = 'approved';
        $this->approved_by = $approver->id;
        $this->approved_at = now();
        $this->approval_notes = $notes;
        $this->save();
    }

    public function publish()
    {
        $this->status = 'published';
        $this->visible_to_student = true;
        $this->visible_to_parent = true;
        $this->published_at = now();
        $this->save();
    }

    public function reject($approver, $notes)
    {
        $this->status = 'rejected';
        $this->approved_by = $approver->id;
        $this->approved_at = now();
        $this->approval_notes = $notes;
        $this->save();
    }

    public function isPassing(): bool
    {
        return $this->percentage >= 60; // International passing standard
    }

    public function getGradeColor(): string
    {
        return match($this->letter_grade) {
            'A+', 'A', 'A-' => 'green',
            'B+', 'B', 'B-' => 'blue',
            'C+', 'C', 'C-' => 'yellow',
            'D+', 'D' => 'orange',
            'F' => 'red',
            default => 'gray'
        };
    }
}