<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyReportMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_report_id', 'metric_name', 'metric_category', 'metric_type',
        'metric_value', 'metric_unit', 'metric_description', 'target_value',
        'previous_value', 'improvement_percentage', 'performance_status',
        'notes', 'metadata'
    ];

    protected $casts = [
        'metric_value' => 'decimal:3',
        'target_value' => 'decimal:3',
        'previous_value' => 'decimal:3',
        'improvement_percentage' => 'decimal:2',
        'metadata' => 'array'
    ];

    public function monthlyReport(): BelongsTo
    {
        return $this->belongsTo(MonthlyReport::class);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('metric_category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('metric_type', $type);
    }

    public function scopeByPerformanceStatus($query, $status)
    {
        return $query->where('performance_status', $status);
    }

    public function scopeExcellent($query)
    {
        return $query->where('performance_status', 'excellent');
    }

    public function scopeGood($query)
    {
        return $query->where('performance_status', 'good');
    }

    public function scopeSatisfactory($query)
    {
        return $query->where('performance_status', 'satisfactory');
    }

    public function scopeNeedsImprovement($query)
    {
        return $query->where('performance_status', 'needs_improvement');
    }

    public function scopePoor($query)
    {
        return $query->where('performance_status', 'poor');
    }

    public function getPerformanceStatusColorAttribute()
    {
        return match($this->performance_status) {
            'excellent' => 'green',
            'good' => 'blue',
            'satisfactory' => 'yellow',
            'needs_improvement' => 'orange',
            'poor' => 'red',
            default => 'gray'
        };
    }

    public function getPerformanceStatusTextAttribute()
    {
        return match($this->performance_status) {
            'excellent' => 'Excellent',
            'good' => 'Good',
            'satisfactory' => 'Satisfactory',
            'needs_improvement' => 'Needs Improvement',
            'poor' => 'Poor',
            default => 'Not Available'
        };
    }

    public function getFormattedValueAttribute()
    {
        if ($this->metric_value === null) return 'N/A';
        
        $value = number_format($this->metric_value, 2);
        return $this->metric_unit ? "{$value} {$this->metric_unit}" : $value;
    }

    public function getFormattedTargetAttribute()
    {
        if ($this->target_value === null) return 'N/A';
        
        $value = number_format($this->target_value, 2);
        return $this->metric_unit ? "{$value} {$this->metric_unit}" : $value;
    }

    public function getFormattedPreviousAttribute()
    {
        if ($this->previous_value === null) return 'N/A';
        
        $value = number_format($this->previous_value, 2);
        return $this->metric_unit ? "{$value} {$this->metric_unit}" : $value;
    }

    public function getFormattedImprovementAttribute()
    {
        if ($this->improvement_percentage === null) return 'N/A';
        
        $sign = $this->improvement_percentage >= 0 ? '+' : '';
        return "{$sign}{$this->improvement_percentage}%";
    }

    public function getImprovementColorAttribute()
    {
        if ($this->improvement_percentage === null) return 'gray';
        
        if ($this->improvement_percentage > 0) return 'green';
        if ($this->improvement_percentage < 0) return 'red';
        return 'gray';
    }

    public function getTargetAchievementAttribute()
    {
        if ($this->target_value === null || $this->target_value == 0) return null;
        
        return round(($this->metric_value / $this->target_value) * 100, 2);
    }

    public function getTargetAchievementColorAttribute()
    {
        $achievement = $this->target_achievement;
        
        if ($achievement === null) return 'gray';
        if ($achievement >= 100) return 'green';
        if ($achievement >= 80) return 'blue';
        if ($achievement >= 60) return 'yellow';
        if ($achievement >= 40) return 'orange';
        return 'red';
    }

    public function getMetricSummaryAttribute()
    {
        return [
            'name' => $this->metric_name,
            'category' => $this->metric_category,
            'type' => $this->metric_type,
            'value' => $this->formatted_value,
            'target' => $this->formatted_target,
            'previous' => $this->formatted_previous,
            'improvement' => $this->formatted_improvement,
            'improvement_color' => $this->improvement_color,
            'target_achievement' => $this->target_achievement,
            'target_achievement_color' => $this->target_achievement_color,
            'performance_status' => $this->performance_status_text,
            'performance_color' => $this->performance_status_color,
            'description' => $this->metric_description,
            'notes' => $this->notes
        ];
    }

    public function calculateImprovementPercentage()
    {
        if ($this->previous_value === null || $this->previous_value == 0) {
            return null;
        }

        return round((($this->metric_value - $this->previous_value) / $this->previous_value) * 100, 2);
    }

    public function determinePerformanceStatus()
    {
        if ($this->target_value === null) {
            return null;
        }

        $achievement = $this->target_achievement;
        
        if ($achievement >= 120) return 'excellent';
        if ($achievement >= 100) return 'good';
        if ($achievement >= 80) return 'satisfactory';
        if ($achievement >= 60) return 'needs_improvement';
        return 'poor';
    }

    public function updatePerformanceStatus()
    {
        $this->improvement_percentage = $this->calculateImprovementPercentage();
        $this->performance_status = $this->determinePerformanceStatus();
        $this->save();
    }
}
