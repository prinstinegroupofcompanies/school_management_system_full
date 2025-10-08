<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranscriptGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'transcript_id', 'subject_id', 'subject_code', 'subject_name', 'credits',
        'grade_letter', 'grade_points', 'percentage', 'semester', 'academic_year',
        'status', 'notes', 'is_repeated', 'is_transfer', 'transfer_institution',
        'grade_date', 'teacher_id'
    ];

    protected $casts = [
        'grade_points' => 'decimal:1',
        'percentage' => 'decimal:2',
        'grade_date' => 'date',
        'is_repeated' => 'boolean',
        'is_transfer' => 'boolean'
    ];

    public function transcript(): BelongsTo
    {
        return $this->belongsTo(Transcript::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade_letter', $grade);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopePassed($query)
    {
        return $query->where('status', 'passed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeTransfer($query)
    {
        return $query->where('is_transfer', true);
    }

    public function scopeRepeated($query)
    {
        return $query->where('is_repeated', true);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'passed' => 'green',
            'failed' => 'red',
            'incomplete' => 'yellow',
            'withdrawn' => 'gray',
            'audit' => 'blue',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'passed' => 'Passed',
            'failed' => 'Failed',
            'incomplete' => 'Incomplete',
            'withdrawn' => 'Withdrawn',
            'audit' => 'Audit',
            default => 'Unknown'
        };
    }

    public function getGradeColorAttribute()
    {
        return match(strtoupper($this->grade_letter)) {
            'A+', 'A', 'A-' => 'green',
            'B+', 'B', 'B-' => 'blue',
            'C+', 'C', 'C-' => 'yellow',
            'D+', 'D' => 'orange',
            'F' => 'red',
            'I' => 'gray',
            'W' => 'gray',
            default => 'gray'
        };
    }

    public function getGradePointsAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        // Calculate grade points if not set
        return match(strtoupper($this->grade_letter)) {
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

    public function getQualityPointsAttribute()
    {
        return $this->grade_points * $this->credits;
    }

    public function isPassingGrade()
    {
        return in_array(strtoupper($this->grade_letter), ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D']);
    }

    public function isFailingGrade()
    {
        return strtoupper($this->grade_letter) === 'F';
    }

    public function isIncompleteGrade()
    {
        return strtoupper($this->grade_letter) === 'I';
    }

    public function isWithdrawnGrade()
    {
        return strtoupper($this->grade_letter) === 'W';
    }

    public function getFormattedGradeAttribute()
    {
        $grade = $this->grade_letter;
        
        if ($this->is_transfer) {
            $grade .= ' (T)';
        }
        
        if ($this->is_repeated) {
            $grade .= ' (R)';
        }
        
        return $grade;
    }

    public function getFormattedSubjectAttribute()
    {
        $subject = $this->subject_name;
        
        if ($this->subject_code) {
            $subject = $this->subject_code . ' - ' . $subject;
        }
        
        return $subject;
    }

    public function getFormattedCreditsAttribute()
    {
        return $this->credits . ' credit' . ($this->credits > 1 ? 's' : '');
    }

    public function getFormattedPercentageAttribute()
    {
        return $this->percentage ? $this->percentage . '%' : 'N/A';
    }

    public function getFormattedGradePointsAttribute()
    {
        return number_format($this->grade_points, 1);
    }

    public function getFormattedQualityPointsAttribute()
    {
        return number_format($this->quality_points, 1);
    }

    public function getFormattedGradeDateAttribute()
    {
        return $this->grade_date ? $this->grade_date->format('M d, Y') : 'N/A';
    }

    public function getGradeDescriptionAttribute()
    {
        return match(strtoupper($this->grade_letter)) {
            'A+' => 'Excellent (97-100%)',
            'A' => 'Excellent (93-96%)',
            'A-' => 'Excellent (90-92%)',
            'B+' => 'Good (87-89%)',
            'B' => 'Good (83-86%)',
            'B-' => 'Good (80-82%)',
            'C+' => 'Satisfactory (77-79%)',
            'C' => 'Satisfactory (73-76%)',
            'C-' => 'Satisfactory (70-72%)',
            'D+' => 'Passing (67-69%)',
            'D' => 'Passing (63-66%)',
            'F' => 'Failing (Below 63%)',
            'I' => 'Incomplete',
            'W' => 'Withdrawn',
            default => 'Unknown'
        };
    }

    public function getGradeNumericValueAttribute()
    {
        return match(strtoupper($this->grade_letter)) {
            'A+' => 100,
            'A' => 95,
            'A-' => 91,
            'B+' => 88,
            'B' => 85,
            'B-' => 81,
            'C+' => 78,
            'C' => 75,
            'C-' => 71,
            'D+' => 68,
            'D' => 65,
            'F' => 0,
            default => 0
        };
    }

    public function getGradeLetterAttribute($value)
    {
        return strtoupper($value);
    }

    public function setGradeLetterAttribute($value)
    {
        $this->attributes['grade_letter'] = strtoupper($value);
    }

    public function getSubjectCodeAttribute($value)
    {
        return $value ?: $this->subject->code ?? '';
    }

    public function getSubjectNameAttribute($value)
    {
        return $value ?: $this->subject->name ?? '';
    }

    public function getCreditsAttribute($value)
    {
        return $value ?: $this->subject->credits ?? 1;
    }

    public function getTeacherNameAttribute()
    {
        return $this->teacher ? $this->teacher->user->name : 'N/A';
    }

    public function getSubjectCodeNameAttribute()
    {
        return $this->subject_code ? $this->subject_code . ' - ' . $this->subject_name : $this->subject_name;
    }

    public function getGradeStatusAttribute()
    {
        if ($this->isPassingGrade()) {
            return 'Passed';
        } elseif ($this->isFailingGrade()) {
            return 'Failed';
        } elseif ($this->isIncompleteGrade()) {
            return 'Incomplete';
        } elseif ($this->isWithdrawnGrade()) {
            return 'Withdrawn';
        } else {
            return 'Unknown';
        }
    }

    public function getGradeStatusColorAttribute()
    {
        if ($this->isPassingGrade()) {
            return 'green';
        } elseif ($this->isFailingGrade()) {
            return 'red';
        } elseif ($this->isIncompleteGrade()) {
            return 'yellow';
        } elseif ($this->isWithdrawnGrade()) {
            return 'gray';
        } else {
            return 'gray';
        }
    }

    public function getGradeStatusIconAttribute()
    {
        if ($this->isPassingGrade()) {
            return 'check-circle';
        } elseif ($this->isFailingGrade()) {
            return 'times-circle';
        } elseif ($this->isIncompleteGrade()) {
            return 'exclamation-circle';
        } elseif ($this->isWithdrawnGrade()) {
            return 'minus-circle';
        } else {
            return 'question-circle';
        }
    }

    public function getGradeStatusBadgeAttribute()
    {
        return [
            'text' => $this->grade_status,
            'color' => $this->grade_status_color,
            'icon' => $this->grade_status_icon
        ];
    }

    public function getGradeBadgeAttribute()
    {
        return [
            'text' => $this->formatted_grade,
            'color' => $this->grade_color,
            'description' => $this->grade_description
        ];
    }

    public function getSubjectBadgeAttribute()
    {
        return [
            'text' => $this->formatted_subject,
            'code' => $this->subject_code,
            'name' => $this->subject_name,
            'credits' => $this->formatted_credits
        ];
    }

    public function getGradeSummaryAttribute()
    {
        return [
            'letter' => $this->grade_letter,
            'points' => $this->formatted_grade_points,
            'percentage' => $this->formatted_percentage,
            'credits' => $this->credits,
            'quality_points' => $this->formatted_quality_points,
            'status' => $this->grade_status,
            'description' => $this->grade_description,
            'date' => $this->formatted_grade_date,
            'teacher' => $this->teacher_name,
            'is_transfer' => $this->is_transfer,
            'is_repeated' => $this->is_repeated,
            'transfer_institution' => $this->transfer_institution
        ];
    }

    public function getGradeDetailsAttribute()
    {
        return [
            'transcript_id' => $this->transcript_id,
            'subject_id' => $this->subject_id,
            'subject' => $this->subject_badge,
            'grade' => $this->grade_badge,
            'status' => $this->grade_status_badge,
            'summary' => $this->grade_summary,
            'notes' => $this->notes,
            'metadata' => [
                'semester' => $this->semester,
                'academic_year' => $this->academic_year,
                'grade_date' => $this->grade_date,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at
            ]
        ];
    }
}
