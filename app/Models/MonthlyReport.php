<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonthlyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_number', 'staff_id', 'report_month', 'report_year', 'report_type', 'status',
        'report_period_start', 'report_period_end', 'total_working_days', 'actual_working_days',
        'days_absent', 'days_late', 'days_early_departure',
        'overall_performance_score', 'attendance_score', 'punctuality_score', 'task_completion_score',
        'quality_score', 'collaboration_score',
        'lessons_taught', 'lessons_planned', 'lesson_plans_submitted', 'lesson_plans_approved', 'lesson_plans_rejected',
        'grades_submitted', 'grades_approved', 'grades_rejected', 'students_taught', 'subjects_taught', 'classes_taught',
        'tasks_assigned', 'tasks_completed', 'tasks_overdue', 'meetings_attended', 'meetings_missed',
        'projects_managed', 'projects_completed', 'deadlines_met', 'deadlines_missed',
        'training_hours', 'workshops_attended', 'certifications_earned', 'professional_development_activities',
        'key_achievements', 'challenges_faced', 'improvements_made', 'innovations_introduced',
        'student_feedback_summary', 'peer_feedback_summary',
        'goals_achieved', 'goals_not_achieved', 'next_month_goals', 'support_needed',
        'executive_summary', 'detailed_analysis', 'recommendations', 'action_items', 'notes',
        'reviewed_by', 'approved_by', 'submitted_at', 'reviewed_at', 'approved_at', 'rejected_at',
        'review_comments', 'rejection_reason',
        'attachments', 'pdf_path', 'excel_path', 'metadata', 'is_auto_generated', 'is_confidential', 'version'
    ];

    protected $casts = [
        'report_period_start' => 'date',
        'report_period_end' => 'date',
        'overall_performance_score' => 'decimal:2',
        'attendance_score' => 'decimal:2',
        'punctuality_score' => 'decimal:2',
        'task_completion_score' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'collaboration_score' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'attachments' => 'array',
        'metadata' => 'array',
        'is_auto_generated' => 'boolean',
        'is_confidential' => 'boolean'
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(MonthlyReportMetric::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByMonth($query, $month)
    {
        return $query->where('report_month', $month);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('report_year', $year);
    }

    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'reviewed' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'reviewed' => 'Reviewed',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }

    public function getTypeTextAttribute()
    {
        return match($this->report_type) {
            'teacher' => 'Teacher Report',
            'staff' => 'Staff Report',
            'department' => 'Department Report',
            'school' => 'School Report',
            default => 'Unknown'
        };
    }

    public function getPerformanceStatusAttribute()
    {
        $score = $this->overall_performance_score ?? 0;
        
        if ($score >= 90) return 'excellent';
        if ($score >= 80) return 'good';
        if ($score >= 70) return 'satisfactory';
        if ($score >= 60) return 'needs_improvement';
        return 'poor';
    }

    public function getAttendancePercentageAttribute()
    {
        if ($this->total_working_days == 0) return 0;
        return round((($this->total_working_days - $this->days_absent) / $this->total_working_days) * 100, 2);
    }

    public function getTaskCompletionPercentageAttribute()
    {
        if ($this->tasks_assigned == 0) return 0;
        return round(($this->tasks_completed / $this->tasks_assigned) * 100, 2);
    }

    public function generateReportNumber()
    {
        $year = $this->report_year ?? now()->year;
        $month = $this->report_month ?? now()->format('m');
        $prefix = 'MR';
        
        $lastReport = self::where('report_year', $year)
            ->where('report_month', $month)
            ->where('report_number', 'like', $prefix . $year . $month . '%')
            ->orderBy('report_number', 'desc')
            ->first();

        if ($lastReport) {
            $lastNumber = (int) substr($lastReport->report_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function submit()
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now()
        ]);
    }

    public function approve($approverId, $comments = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approverId,
            'review_comments' => $comments
        ]);
    }

    public function reject($approverId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'approved_by' => $approverId,
            'rejection_reason' => $reason
        ]);
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function canBeSubmitted()
    {
        return $this->status === 'draft';
    }

    public function canBeApproved()
    {
        return $this->status === 'reviewed';
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'rejected']);
    }
