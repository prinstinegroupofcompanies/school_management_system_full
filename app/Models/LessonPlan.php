<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'subject_id', 'class_id', 'title', 'description',
        'objectives', 'materials_needed', 'activities', 'assessment',
        'homework', 'notes', 'lesson_date', 'start_time', 'end_time',
        'duration_minutes', 'status', 'rejection_reason', 'version',
        'is_active', 'attachments', 'metadata'
    ];

    protected $casts = [
        'lesson_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_minutes' => 'integer',
        'is_active' => 'boolean',
        'attachments' => 'array',
        'metadata' => 'array'
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(LessonPlanApproval::class);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['submitted', 'first_level_approved']);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'second_level_approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function getFirstLevelApprovalAttribute()
    {
        return $this->approvals()->where('approval_level', 'first_level')->first();
    }

    public function getSecondLevelApprovalAttribute()
    {
        return $this->approvals()->where('approval_level', 'second_level')->first();
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'first_level_approved' => 'yellow',
            'second_level_approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'first_level_approved' => 'First Level Approved',
            'second_level_approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function canBeSubmitted()
    {
        return $this->status === 'draft';
    }

    public function canBeApproved()
    {
        return in_array($this->status, ['submitted', 'first_level_approved']);
    }

    public function canBeRejected()
    {
        return in_array($this->status, ['submitted', 'first_level_approved']);
    }

    public function submit()
    {
        if ($this->canBeSubmitted()) {
            $this->status = 'submitted';
            $this->save();
            
            // Create first level approval record
            $this->approvals()->create([
                'approver_id' => $this->getDepartmentHeadId(),
                'approval_level' => 'first_level',
                'status' => 'pending'
            ]);
        }
    }

    public function approve($approverId, $level, $comments = null, $eSignature = null)
    {
        if ($this->canBeApproved()) {
            $approval = $this->approvals()->where('approval_level', $level)->first();
            
            if ($approval) {
                $approval->update([
                    'approver_id' => $approverId,
                    'status' => 'approved',
                    'comments' => $comments,
                    'e_signature' => $eSignature,
                    'approved_at' => now()
                ]);

                if ($level === 'first_level') {
                    $this->status = 'first_level_approved';
                    // Create second level approval record
                    $this->approvals()->create([
                        'approver_id' => $this->getAdminId(),
                        'approval_level' => 'second_level',
                        'status' => 'pending'
                    ]);
                } else {
                    $this->status = 'second_level_approved';
                }
                
                $this->save();
            }
        }
    }

    public function reject($approverId, $level, $reason, $eSignature = null)
    {
        if ($this->canBeRejected()) {
            $approval = $this->approvals()->where('approval_level', $level)->first();
            
            if ($approval) {
                $approval->update([
                    'approver_id' => $approverId,
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                    'e_signature' => $eSignature,
                    'rejected_at' => now()
                ]);

                $this->status = 'rejected';
                $this->rejection_reason = $reason;
                $this->save();
            }
        }
    }

    private function getDepartmentHeadId()
    {
        // Logic to get department head ID based on subject/teacher
        return User::where('role', 'teacher')
            ->whereHas('teacher', function($query) {
                $query->where('department', $this->subject->teacher->department ?? 'General');
            })
            ->where('is_department_head', true)
            ->first()?->id ?? 1; // Fallback to admin
    }

    private function getAdminId()
    {
        return User::where('role', 'admin')->first()?->id ?? 1;
    }
}
