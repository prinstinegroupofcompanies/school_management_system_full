<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffPerformance extends Model
{
    use HasFactory;

    protected $table = 'staff_performance';

    protected $fillable = [
        'staff_id',
        'evaluator_id',
        'evaluation_period',
        'evaluation_date',
        'period_start',
        'period_end',
        'punctuality',
        'work_quality',
        'teamwork',
        'communication',
        'initiative',
        'problem_solving',
        'leadership',
        'adaptability',
        'overall_score',
        'performance_rating',
        'goals_achieved',
        'goals_pending',
        'strengths',
        'areas_for_improvement',
        'development_plan',
        'training_recommendations',
        'next_period_goals',
        'evaluator_comments',
        'staff_comments',
        'hr_comments',
        'status',
        'is_confidential'
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'goals_achieved' => 'array',
        'goals_pending' => 'array',
        'training_recommendations' => 'array',
        'is_confidential' => 'boolean',
        'overall_score' => 'decimal:2'
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function calculateOverallScore(): void
    {
        $scores = [
            $this->punctuality,
            $this->work_quality,
            $this->teamwork,
            $this->communication,
            $this->initiative,
            $this->problem_solving,
            $this->leadership,
            $this->adaptability
        ];

        $this->overall_score = round(array_sum($scores) / count($scores), 2);
        
        // Set performance rating based on overall score
        if ($this->overall_score >= 4.5) {
            $this->performance_rating = 'excellent';
        } elseif ($this->overall_score >= 3.5) {
            $this->performance_rating = 'good';
        } elseif ($this->overall_score >= 2.5) {
            $this->performance_rating = 'satisfactory';
        } elseif ($this->overall_score >= 1.5) {
            $this->performance_rating = 'needs_improvement';
        } else {
            $this->performance_rating = 'unsatisfactory';
        }
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('evaluation_period', $period);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('performance_rating', $rating);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function getPerformanceColorAttribute(): string
    {
        return match($this->performance_rating) {
            'excellent' => 'green',
            'good' => 'blue',
            'satisfactory' => 'yellow',
            'needs_improvement' => 'orange',
            'unsatisfactory' => 'red',
            default => 'gray'
        };
    }
}
