<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ReportSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id', 'user_id', 'subscription_name', 'description', 'frequency',
        'report_params', 'filters', 'email', 'export_settings', 'is_active',
        'last_sent', 'next_send', 'sent_count', 'metadata'
    ];

    protected $casts = [
        'report_params' => 'array',
        'filters' => 'array',
        'export_settings' => 'array',
        'is_active' => 'boolean',
        'last_sent' => 'datetime',
        'next_send' => 'datetime',
        'sent_count' => 'integer',
        'metadata' => 'array'
    ];

    // Relationships
    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class, 'template_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDueForSending($query)
    {
        return $query->where('is_active', true)
                    ->where('next_send', '<=', now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('is_active', true)
                    ->where('next_send', '<', now()->subHour());
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
            default => 'secondary'
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->is_active && $this->next_send && $this->next_send->isPast();
    }

    public function getIsDueAttribute(): bool
    {
        return $this->is_active && $this->next_send && $this->next_send->isPast();
    }

    public function getFormattedLastSentAttribute(): string
    {
        return $this->last_sent ? $this->last_sent->format('M d, Y H:i') : 'Never';
    }

    public function getFormattedNextSendAttribute(): string
    {
        return $this->next_send ? $this->next_send->format('M d, Y H:i') : 'Not scheduled';
    }

    public function getReportParamsFormattedAttribute(): array
    {
        return $this->report_params ?? [];
    }

    public function getFiltersFormattedAttribute(): array
    {
        return $this->filters ?? [];
    }

    public function getExportSettingsFormattedAttribute(): array
    {
        return $this->export_settings ?? [];
    }

    public function getTemplateNameAttribute(): string
    {
        return $this->template->template_name ?? 'Unknown Template';
    }

    public function getUserNameAttribute(): string
    {
        return $this->user->name ?? 'Unknown User';
    }

    public function getUserEmailAttribute(): string
    {
        return $this->user->email ?? 'Unknown Email';
    }

    // Methods
    public function activate(): bool
    {
        $this->update(['is_active' => true]);
        $this->calculateNextSend();
        return true;
    }

    public function deactivate(): bool
    {
        $this->update(['is_active' => false]);
        return true;
    }

    public function calculateNextSend(): void
    {
        if (!$this->is_active) {
            return;
        }

        $now = now();
        $lastSent = $this->last_sent ?? $now;

        $nextSend = match ($this->frequency) {
            'daily' => $lastSent->addDay(),
            'weekly' => $lastSent->addWeek(),
            'monthly' => $lastSent->addMonth(),
            'quarterly' => $lastSent->addMonths(3),
            'yearly' => $lastSent->addYear(),
            default => $lastSent->addDay()
        };

        // Apply time configuration if available
        $settings = $this->export_settings;
        if (isset($settings['send_time'])) {
            $time = Carbon::parse($settings['send_time']);
            $nextSend = $nextSend->setTime($time->hour, $time->minute, $time->second);
        }

        $this->update(['next_send' => $nextSend]);
    }

    public function send(): bool
    {
        // Create a new execution for this subscription
        $execution = $this->template->executions()->create([
            'executed_by' => $this->user_id,
            'execution_id' => 'SUB-' . strtoupper(uniqid()),
            'status' => 'pending',
            'report_params' => $this->report_params,
            'filters' => $this->filters,
            'export_format' => $this->export_settings['format'] ?? 'PDF'
        ]);

        // Update subscription
        $this->update([
            'last_sent' => now(),
            'sent_count' => $this->sent_count + 1
        ]);

        // Calculate next send
        $this->calculateNextSend();

        return true;
    }

    public function canBeSent(): bool
    {
        return $this->is_active && $this->is_due;
    }

    public function canBeEdited(): bool
    {
        return true; // Subscriptions can always be edited
    }

    public function canBeDeleted(): bool
    {
        return true; // Subscriptions can always be deleted
    }

    public function getSubscriptionSummary(): array
    {
        return [
            'id' => $this->id,
            'subscription_name' => $this->subscription_name,
            'template_name' => $this->template_name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'frequency' => $this->frequency_text,
            'frequency_color' => $this->frequency_color,
            'status' => $this->status_text,
            'status_color' => $this->status_color,
            'last_sent' => $this->formatted_last_sent,
            'next_send' => $this->formatted_next_send,
            'sent_count' => $this->sent_count,
            'is_overdue' => $this->is_overdue,
            'is_due' => $this->is_due
        ];
    }

    public function getEmailSettings(): array
    {
        $settings = $this->export_settings ?? [];
        return [
            'format' => $settings['format'] ?? 'PDF',
            'send_time' => $settings['send_time'] ?? '09:00',
            'subject_template' => $settings['subject_template'] ?? 'Report: {template_name}',
            'body_template' => $settings['body_template'] ?? 'Please find attached the {template_name} report.',
            'include_charts' => $settings['include_charts'] ?? true,
            'include_summary' => $settings['include_summary'] ?? true
        ];
    }

    public function updateEmailSettings(array $settings): bool
    {
        $currentSettings = $this->export_settings ?? [];
        $updatedSettings = array_merge($currentSettings, $settings);
        
        $this->update(['export_settings' => $updatedSettings]);
        return true;
    }

    public function getReportParameters(): array
    {
        return $this->report_params ?? [];
    }

    public function updateReportParameters(array $parameters): bool
    {
        $this->update(['report_params' => $parameters]);
        return true;
    }

    public function getFilters(): array
    {
        return $this->filters ?? [];
    }

    public function updateFilters(array $filters): bool
    {
        $this->update(['filters' => $filters]);
        return true;
    }

    public function getStatistics(): array
    {
        return [
            'sent_count' => $this->sent_count,
            'last_sent' => $this->formatted_last_sent,
            'next_send' => $this->formatted_next_send,
            'is_overdue' => $this->is_overdue,
            'is_due' => $this->is_due,
            'frequency' => $this->frequency_text,
            'status' => $this->status_text
        ];
    }

    public function getEmailContent(): array
    {
        $settings = $this->getEmailSettings();
        $template = $this->template;
        
        return [
            'subject' => str_replace(
                ['{template_name}', '{frequency}', '{user_name}'],
                [$template->template_name ?? 'Report', $this->frequency_text, $this->user_name],
                $settings['subject_template']
            ),
            'body' => str_replace(
                ['{template_name}', '{frequency}', '{user_name}', '{last_sent}'],
                [
                    $template->template_name ?? 'Report',
                    $this->frequency_text,
                    $this->user_name,
                    $this->formatted_last_sent
                ],
                $settings['body_template']
            ),
            'template_name' => $template->template_name ?? 'Report',
            'frequency' => $this->frequency_text,
            'user_name' => $this->user_name
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if ($subscription->is_active) {
                $subscription->calculateNextSend();
            }
        });
    }
}