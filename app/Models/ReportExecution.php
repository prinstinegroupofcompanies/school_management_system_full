<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ReportExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id', 'schedule_id', 'executed_by', 'execution_id', 'status',
        'report_params', 'filters', 'file_path', 'file_name', 'file_size',
        'export_format', 'started_at', 'completed_at', 'execution_time',
        'error_message', 'execution_log', 'metadata'
    ];

    protected $casts = [
        'report_params' => 'array',
        'filters' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'execution_time' => 'integer',
        'execution_log' => 'array',
        'metadata' => 'array'
    ];

    // Relationships
    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class, 'template_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ReportSchedule::class, 'schedule_id');
    }

    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }

    public function scopeBySchedule($query, $scheduleId)
    {
        return $query->where('schedule_id', $scheduleId);
    }

    public function scopeByExecutor($query, $userId)
    {
        return $query->where('executed_by', $userId);
    }

    public function scopeByExportFormat($query, $format)
    {
        return $query->where('export_format', $format);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('started_at', [$startDate, $endDate]);
    }

    public function scopeLongRunning($query, $minutes = 30)
    {
        return $query->where('status', 'running')
                    ->where('started_at', '<', now()->subMinutes($minutes));
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'running' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'running' => 'Running',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getExportFormatTextAttribute(): string
    {
        return match ($this->export_format) {
            'PDF' => 'PDF Document',
            'Excel' => 'Excel Spreadsheet',
            'CSV' => 'CSV File',
            'HTML' => 'HTML Document',
            default => strtoupper($this->export_format)
        };
    }

    public function getExportFormatColorAttribute(): string
    {
        return match ($this->export_format) {
            'PDF' => 'danger',
            'Excel' => 'success',
            'CSV' => 'info',
            'HTML' => 'primary',
            default => 'secondary'
        };
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsRunningAttribute(): bool
    {
        return $this->status === 'running';
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getIsLongRunningAttribute(): bool
    {
        return $this->is_running && $this->started_at && $this->started_at->diffInMinutes(now()) > 30;
    }

    public function getFormattedStartedAtAttribute(): string
    {
        return $this->started_at ? $this->started_at->format('M d, Y H:i:s') : 'Not started';
    }

    public function getFormattedCompletedAtAttribute(): string
    {
        return $this->completed_at ? $this->completed_at->format('M d, Y H:i:s') : 'Not completed';
    }

    public function getFormattedExecutionTimeAttribute(): string
    {
        if (!$this->execution_time) {
            return 'N/A';
        }

        if ($this->execution_time < 60) {
            return $this->execution_time . ' seconds';
        } elseif ($this->execution_time < 3600) {
            return round($this->execution_time / 60, 1) . ' minutes';
        } else {
            return round($this->execution_time / 3600, 1) . ' hours';
        }
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileUrlAttribute(): string
    {
        if (!$this->file_path) {
            return '#';
        }

        return asset('storage/' . $this->file_path);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('admin.reports.executions.download', $this->id);
    }

    public function getCanBeDownloadedAttribute(): bool
    {
        return $this->is_completed && $this->file_path && file_exists(storage_path('app/public/' . $this->file_path));
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return in_array($this->status, ['pending', 'running']);
    }

    public function getCanBeRetriedAttribute(): bool
    {
        return $this->is_failed;
    }

    public function getCanBeDeletedAttribute(): bool
    {
        return in_array($this->status, ['completed', 'failed', 'cancelled']);
    }

    public function getReportParamsFormattedAttribute(): array
    {
        return $this->report_params ?? [];
    }

    public function getFiltersFormattedAttribute(): array
    {
        return $this->filters ?? [];
    }

    public function getExecutionLogFormattedAttribute(): array
    {
        return $this->execution_log ?? [];
    }

    // Methods
    public function generateExecutionId(): string
    {
        return 'EXEC-' . strtoupper(uniqid());
    }

    public function start(): bool
    {
        $this->update([
            'status' => 'running',
            'started_at' => now()
        ]);

        return true;
    }

    public function complete(string $filePath, string $fileName, int $fileSize): bool
    {
        $executionTime = $this->started_at ? $this->started_at->diffInSeconds(now()) : 0;

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'execution_time' => $executionTime
        ]);

        return true;
    }

    public function fail(string $errorMessage, array $executionLog = []): bool
    {
        $executionTime = $this->started_at ? $this->started_at->diffInSeconds(now()) : 0;

        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_message' => $errorMessage,
            'execution_log' => $executionLog,
            'execution_time' => $executionTime
        ]);

        return true;
    }

    public function cancel(string $reason = null): bool
    {
        $executionTime = $this->started_at ? $this->started_at->diffInSeconds(now()) : 0;

        $this->update([
            'status' => 'cancelled',
            'completed_at' => now(),
            'error_message' => $reason ?? 'Execution cancelled by user',
            'execution_time' => $executionTime
        ]);

        return true;
    }

    public function retry(): ReportExecution
    {
        $newExecution = $this->template->executions()->create([
            'schedule_id' => $this->schedule_id,
            'executed_by' => $this->executed_by,
            'execution_id' => $this->generateExecutionId(),
            'status' => 'pending',
            'report_params' => $this->report_params,
            'filters' => $this->filters,
            'export_format' => $this->export_format
        ]);

        return $newExecution;
    }

    public function addLogEntry(string $level, string $message, array $context = []): void
    {
        $log = $this->execution_log ?? [];
        $log[] = [
            'timestamp' => now()->toISOString(),
            'level' => $level,
            'message' => $message,
            'context' => $context
        ];

        $this->update(['execution_log' => $log]);
    }

    public function getExecutionSummary(): array
    {
        return [
            'id' => $this->id,
            'execution_id' => $this->execution_id,
            'template_name' => $this->template->template_name ?? 'Unknown',
            'status' => $this->status_text,
            'status_color' => $this->status_color,
            'export_format' => $this->export_format_text,
            'export_format_color' => $this->export_format_color,
            'started_at' => $this->formatted_started_at,
            'completed_at' => $this->formatted_completed_at,
            'execution_time' => $this->formatted_execution_time,
            'file_name' => $this->file_name,
            'file_size' => $this->formatted_file_size,
            'can_download' => $this->can_be_downloaded,
            'can_cancel' => $this->can_be_cancelled,
            'can_retry' => $this->can_be_retried,
            'can_delete' => $this->can_be_deleted
        ];
    }

    public function getDetailedLog(): array
    {
        $log = $this->execution_log ?? [];
        
        return array_map(function ($entry) {
            return [
                'timestamp' => $entry['timestamp'] ?? '',
                'level' => $entry['level'] ?? 'info',
                'message' => $entry['message'] ?? '',
                'context' => $entry['context'] ?? [],
                'formatted_time' => isset($entry['timestamp']) 
                    ? Carbon::parse($entry['timestamp'])->format('M d, Y H:i:s')
                    : ''
            ];
        }, $log);
    }

    public function cleanup(): bool
    {
        // Delete the generated file if it exists
        if ($this->file_path && file_exists(storage_path('app/public/' . $this->file_path))) {
            unlink(storage_path('app/public/' . $this->file_path));
        }

        // Update execution to mark file as cleaned up
        $this->update([
            'file_path' => null,
            'file_name' => null,
            'file_size' => null
        ]);

        return true;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($execution) {
            if (!$execution->execution_id) {
                $execution->execution_id = $execution->generateExecutionId();
            }
        });
    }
}