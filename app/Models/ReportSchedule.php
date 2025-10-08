<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ReportSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id', 'created_by', 'schedule_name', 'description', 'frequency',
        'schedule_config', 'report_params', 'recipients', 'export_settings',
        'is_active', 'last_executed', 'next_execution', 'execution_count', 'metadata'
    ];

    protected $casts = [
        'schedule_config' => 'array',
        'report_params' => 'array',
        'recipients' => 'array',
        'export_settings' => 'array',
        'is_active' => 'boolean',
        'last_executed' => 'datetime',
        'next_execution' => 'datetime',
        'execution_count' => 'integer',
        'metadata' => 'array'
    ];

    // Relationships
    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class, 'template_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function executions(): HasMany
    {
        return $this->hasMany(ReportExecution::class, 'schedule_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByFrequency($query, $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    public function scopeByTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }

    public function scopeByCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeDueForExecution($query)
    {
        return $query->where('is_active', true)
                    ->where('next_execution', '<=', now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('is_active', true)
                    ->where('next_execution', '<', now()->subHour());
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'danger';
    }

    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getFrequencyTextAttribute(): string
    {
        return match ($this->frequency) {
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            'custom' => 'Custom',
            default => ucfirst($this->frequency)
        };
    }

    public function getFrequencyColorAttribute(): string
    {
        return match ($this->frequency) {
            'daily' => 'primary',
            'weekly' => 'info',
            'monthly' => 'success',
            'quarterly' => 'warning',
            'yearly' => 'danger',
            'custom' => 'secondary',
            default => 'secondary'
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->is_active && $this->next_execution && $this->next_execution->isPast();
    }

    public function getIsDueAttribute(): bool
    {
        return $this->is_active && $this->next_execution && $this->next_execution->isPast();
    }

    public function getFormattedLastExecutedAttribute(): string
    {
        return $this->last_executed ? $this->last_executed->format('M d, Y H:i') : 'Never';
    }

    public function getFormattedNextExecutionAttribute(): string
    {
        return $this->next_execution ? $this->next_execution->format('M d, Y H:i') : 'Not scheduled';
    }

    public function getRecipientsFormattedAttribute(): array
    {
        return $this->recipients ?? [];
    }

    public function getExportSettingsFormattedAttribute(): array
    {
        return $this->export_settings ?? [];
    }

    public function getScheduleConfigFormattedAttribute(): array
    {
        return $this->schedule_config ?? [];
    }

    public function getReportParamsFormattedAttribute(): array
    {
        return $this->report_params ?? [];
    }

    public function getSuccessRateAttribute(): float
    {
        $totalExecutions = $this->executions()->count();
        if ($totalExecutions === 0) {
            return 0;
        }

        $successfulExecutions = $this->executions()->where('status', 'completed')->count();
        return round(($successfulExecutions / $totalExecutions) * 100, 2);
    }

    public function getAverageExecutionTimeAttribute(): float
    {
        return $this->executions()
            ->where('status', 'completed')
            ->whereNotNull('execution_time')
            ->avg('execution_time') ?? 0;
    }

    public function getLastExecutionStatusAttribute(): string
    {
        $lastExecution = $this->executions()->latest()->first();
        return $lastExecution ? $lastExecution->status : 'none';
    }

    public function getLastExecutionStatusColorAttribute(): string
    {
        return match ($this->last_execution_status) {
            'completed' => 'success',
            'failed' => 'danger',
            'running' => 'warning',
            'pending' => 'info',
            'cancelled' => 'secondary',
            default => 'secondary'
        };
    }

    // Methods
    public function activate(): bool
    {
        $this->update(['is_active' => true]);
        $this->calculateNextExecution();
        return true;
    }

    public function deactivate(): bool
    {
        $this->update(['is_active' => false]);
        return true;
    }

    public function calculateNextExecution(): void
    {
        if (!$this->is_active) {
            return;
        }

        $now = now();
        $lastExecution = $this->last_executed ?? $now;

        $nextExecution = match ($this->frequency) {
            'daily' => $lastExecution->addDay(),
            'weekly' => $lastExecution->addWeek(),
            'monthly' => $lastExecution->addMonth(),
            'quarterly' => $lastExecution->addMonths(3),
            'yearly' => $lastExecution->addYear(),
            'custom' => $this->calculateCustomNextExecution($lastExecution),
            default => $lastExecution->addDay()
        };

        // Apply time configuration if available
        $config = $this->schedule_config;
        if (isset($config['time'])) {
            $time = Carbon::parse($config['time']);
            $nextExecution = $nextExecution->setTime($time->hour, $time->minute, $time->second);
        }

        $this->update(['next_execution' => $nextExecution]);
    }

    private function calculateCustomNextExecution(Carbon $lastExecution): Carbon
    {
        $config = $this->schedule_config;
        
        if (isset($config['interval']) && isset($config['unit'])) {
            $interval = $config['interval'];
            $unit = $config['unit'];
            
            return match ($unit) {
                'minutes' => $lastExecution->addMinutes($interval),
                'hours' => $lastExecution->addHours($interval),
                'days' => $lastExecution->addDays($interval),
                'weeks' => $lastExecution->addWeeks($interval),
                'months' => $lastExecution->addMonths($interval),
                default => $lastExecution->addDay()
            };
        }

        return $lastExecution->addDay();
    }

    public function execute(): ReportExecution
    {
        $execution = $this->executions()->create([
            'template_id' => $this->template_id,
            'executed_by' => $this->created_by,
            'execution_id' => 'EXEC-' . strtoupper(uniqid()),
            'status' => 'pending',
            'report_params' => $this->report_params,
            'filters' => $this->report_params,
            'export_format' => $this->export_settings['format'] ?? 'PDF',
            'started_at' => now()
        ]);

        // Update schedule
        $this->update([
            'last_executed' => now(),
            'execution_count' => $this->execution_count + 1
        ]);

        // Calculate next execution
        $this->calculateNextExecution();

        return $execution;
    }

    public function canBeExecuted(): bool
    {
        return $this->is_active && $this->is_due;
    }

    public function canBeEdited(): bool
    {
        return $this->executions()->where('status', 'running')->count() === 0;
    }

    public function canBeDeleted(): bool
    {
        return $this->executions()->count() === 0;
    }

    public function getExecutionHistory(int $limit = 10): array
    {
        return $this->executions()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($execution) {
                return [
                    'id' => $execution->id,
                    'execution_id' => $execution->execution_id,
                    'status' => $execution->status,
                    'status_color' => $execution->status_color,
                    'started_at' => $execution->started_at?->format('M d, Y H:i'),
                    'completed_at' => $execution->completed_at?->format('M d, Y H:i'),
                    'execution_time' => $execution->execution_time,
                    'file_name' => $execution->file_name,
                    'export_format' => $execution->export_format
                ];
            })
            ->toArray();
    }

    public function getRecipientsList(): array
    {
        $recipients = $this->recipients ?? [];
        return array_map(function ($recipient) {
            return [
                'email' => $recipient['email'] ?? '',
                'name' => $recipient['name'] ?? '',
                'type' => $recipient['type'] ?? 'email'
            ];
        }, $recipients);
    }

    public function addRecipient(string $email, string $name = '', string $type = 'email'): bool
    {
        $recipients = $this->recipients ?? [];
        
        // Check if recipient already exists
        $exists = collect($recipients)->contains('email', $email);
        if ($exists) {
            return false;
        }

        $recipients[] = [
            'email' => $email,
            'name' => $name,
            'type' => $type
        ];

        $this->update(['recipients' => $recipients]);
        return true;
    }

    public function removeRecipient(string $email): bool
    {
        $recipients = $this->recipients ?? [];
        $filtered = collect($recipients)->reject(function ($recipient) use ($email) {
            return ($recipient['email'] ?? '') === $email;
        })->values()->toArray();

        $this->update(['recipients' => $filtered]);
        return true;
    }

    public function getStatistics(): array
    {
        $executions = $this->executions();
        
        return [
            'total_executions' => $executions->count(),
            'successful_executions' => $executions->where('status', 'completed')->count(),
            'failed_executions' => $executions->where('status', 'failed')->count(),
            'success_rate' => $this->success_rate,
            'average_execution_time' => $this->average_execution_time,
            'last_execution' => $this->formatted_last_executed,
            'next_execution' => $this->formatted_next_execution,
            'is_overdue' => $this->is_overdue,
            'recipients_count' => count($this->recipients ?? [])
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($schedule) {
            if ($schedule->is_active) {
                $schedule->calculateNextExecution();
            }
        });
    }
}