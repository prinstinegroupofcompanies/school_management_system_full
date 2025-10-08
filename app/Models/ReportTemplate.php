<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_name', 'template_code', 'description', 'report_type', 'category',
        'data_sources', 'report_structure', 'filters', 'charts_config', 'export_formats',
        'template_file', 'permissions', 'notification_settings', 'is_public', 'is_active',
        'sort_order', 'metadata'
    ];

    protected $casts = [
        'data_sources' => 'array',
        'report_structure' => 'array',
        'filters' => 'array',
        'charts_config' => 'array',
        'export_formats' => 'array',
        'permissions' => 'array',
        'notification_settings' => 'array',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array'
    ];

    // Relationships
    public function schedules(): HasMany
    {
        return $this->hasMany(ReportSchedule::class, 'template_id');
    }

    public function executions(): HasMany
    {
        return $this->hasMany(ReportExecution::class, 'template_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ReportSubscription::class, 'template_id');
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

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('template_name');
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

    public function getVisibilityColorAttribute(): string
    {
        return $this->is_public ? 'info' : 'warning';
    }

    public function getVisibilityTextAttribute(): string
    {
        return $this->is_public ? 'Public' : 'Private';
    }

    public function getReportTypeTextAttribute(): string
    {
        return match ($this->report_type) {
            'academic' => 'Academic',
            'financial' => 'Financial',
            'administrative' => 'Administrative',
            'attendance' => 'Attendance',
            'performance' => 'Performance',
            'inventory' => 'Inventory',
            'health_safety' => 'Health & Safety',
            'visitor' => 'Visitor Management',
            default => ucfirst(str_replace('_', ' ', $this->report_type))
        };
    }

    public function getCategoryTextAttribute(): string
    {
        return match ($this->category) {
            'student' => 'Student',
            'teacher' => 'Teacher',
            'staff' => 'Staff',
            'finance' => 'Finance',
            'academic' => 'Academic',
            'administrative' => 'Administrative',
            'general' => 'General',
            default => ucfirst(str_replace('_', ' ', $this->category))
        };
    }

    public function getDataSourcesFormattedAttribute(): array
    {
        return $this->data_sources ?? [];
    }

    public function getReportStructureFormattedAttribute(): array
    {
        return $this->report_structure ?? [];
    }

    public function getFiltersFormattedAttribute(): array
    {
        return $this->filters ?? [];
    }

    public function getChartsConfigFormattedAttribute(): array
    {
        return $this->charts_config ?? [];
    }

    public function getExportFormatsFormattedAttribute(): array
    {
        return $this->export_formats ?? ['PDF', 'Excel', 'CSV'];
    }

    public function getPermissionsFormattedAttribute(): array
    {
        return $this->permissions ?? [];
    }

    public function getNotificationSettingsFormattedAttribute(): array
    {
        return $this->notification_settings ?? [];
    }

    public function getExecutionCountAttribute(): int
    {
        return $this->executions()->count();
    }

    public function getActiveScheduleCountAttribute(): int
    {
        return $this->schedules()->where('is_active', true)->count();
    }

    public function getSubscriptionCountAttribute(): int
    {
        return $this->subscriptions()->where('is_active', true)->count();
    }

    public function getLastExecutionAttribute(): ?ReportExecution
    {
        return $this->executions()->latest()->first();
    }

    public function getLastExecutionDateAttribute(): ?string
    {
        $lastExecution = $this->last_execution;
        return $lastExecution ? $lastExecution->started_at?->format('M d, Y H:i') : 'Never';
    }

    // Methods
    public function generateTemplateCode(): string
    {
        $prefix = strtoupper(substr($this->report_type, 0, 3));
        $suffix = strtoupper(substr(str_replace(' ', '', $this->template_name), 0, 6));
        return $prefix . '-' . $suffix . '-' . strtoupper(uniqid());
    }

    public function activate(): bool
    {
        $this->update(['is_active' => true]);
        return true;
    }

    public function deactivate(): bool
    {
        $this->update(['is_active' => false]);
        return true;
    }

    public function makePublic(): bool
    {
        $this->update(['is_public' => true]);
        return true;
    }

    public function makePrivate(): bool
    {
        $this->update(['is_public' => false]);
        return true;
    }

    public function canBeDeleted(): bool
    {
        return $this->executions()->count() === 0 && 
               $this->schedules()->count() === 0 && 
               $this->subscriptions()->count() === 0;
    }

    public function canBeEdited(): bool
    {
        return $this->executions()->where('status', 'running')->count() === 0;
    }

    public function getAvailableFilters(): array
    {
        $filters = $this->filters ?? [];
        return array_map(function ($filter) {
            return [
                'name' => $filter['name'] ?? '',
                'type' => $filter['type'] ?? 'text',
                'label' => $filter['label'] ?? $filter['name'] ?? '',
                'options' => $filter['options'] ?? [],
                'required' => $filter['required'] ?? false,
                'default' => $filter['default'] ?? null
            ];
        }, $filters);
    }

    public function getDataSources(): array
    {
        $sources = $this->data_sources ?? [];
        return array_map(function ($source) {
            return [
                'name' => $source['name'] ?? '',
                'query' => $source['query'] ?? '',
                'parameters' => $source['parameters'] ?? [],
                'joins' => $source['joins'] ?? [],
                'conditions' => $source['conditions'] ?? []
            ];
        }, $sources);
    }

    public function getReportStructure(): array
    {
        $structure = $this->report_structure ?? [];
        return [
            'sections' => $structure['sections'] ?? [],
            'layout' => $structure['layout'] ?? 'standard',
            'header' => $structure['header'] ?? [],
            'footer' => $structure['footer'] ?? [],
            'styling' => $structure['styling'] ?? []
        ];
    }

    public function getChartsConfiguration(): array
    {
        $charts = $this->charts_config ?? [];
        return array_map(function ($chart) {
            return [
                'type' => $chart['type'] ?? 'bar',
                'title' => $chart['title'] ?? '',
                'data_source' => $chart['data_source'] ?? '',
                'x_axis' => $chart['x_axis'] ?? '',
                'y_axis' => $chart['y_axis'] ?? '',
                'colors' => $chart['colors'] ?? [],
                'options' => $chart['options'] ?? []
            ];
        }, $charts);
    }

    public function getExportSettings(): array
    {
        return [
            'formats' => $this->export_formats ?? ['PDF', 'Excel', 'CSV'],
            'default_format' => $this->export_formats[0] ?? 'PDF',
            'page_size' => $this->metadata['page_size'] ?? 'A4',
            'orientation' => $this->metadata['orientation'] ?? 'portrait',
            'margins' => $this->metadata['margins'] ?? ['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1]
        ];
    }

    public function getPermissions(): array
    {
        $permissions = $this->permissions ?? [];
        return [
            'roles' => $permissions['roles'] ?? [],
            'users' => $permissions['users'] ?? [],
            'departments' => $permissions['departments'] ?? [],
            'restrictions' => $permissions['restrictions'] ?? []
        ];
    }

    public function hasPermission(User $user): bool
    {
        if ($this->is_public) {
            return true;
        }

        $permissions = $this->getPermissions();
        
        // Check if user is specifically allowed
        if (in_array($user->id, $permissions['users'])) {
            return true;
        }

        // Check if user's role is allowed
        if ($user->role && in_array($user->role, $permissions['roles'])) {
            return true;
        }

        // Check if user's department is allowed
        if ($user->department && in_array($user->department, $permissions['departments'])) {
            return true;
        }

        return false;
    }

    public function getNotificationSettings(): array
    {
        $settings = $this->notification_settings ?? [];
        return [
            'email_notifications' => $settings['email_notifications'] ?? true,
            'sms_notifications' => $settings['sms_notifications'] ?? false,
            'web_notifications' => $settings['web_notifications'] ?? true,
            'recipients' => $settings['recipients'] ?? [],
            'triggers' => $settings['triggers'] ?? ['completion', 'failure']
        ];
    }

    public function validateParameters(array $parameters): array
    {
        $errors = [];
        $filters = $this->getAvailableFilters();

        foreach ($filters as $filter) {
            $name = $filter['name'];
            $required = $filter['required'];
            $type = $filter['type'];

            if ($required && empty($parameters[$name])) {
                $errors[$name] = "The {$filter['label']} field is required.";
            }

            if (!empty($parameters[$name])) {
                switch ($type) {
                    case 'date':
                        if (!strtotime($parameters[$name])) {
                            $errors[$name] = "The {$filter['label']} must be a valid date.";
                        }
                        break;
                    case 'number':
                        if (!is_numeric($parameters[$name])) {
                            $errors[$name] = "The {$filter['label']} must be a valid number.";
                        }
                        break;
                    case 'email':
                        if (!filter_var($parameters[$name], FILTER_VALIDATE_EMAIL)) {
                            $errors[$name] = "The {$filter['label']} must be a valid email address.";
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    public function getExecutionStatistics(): array
    {
        $executions = $this->executions();
        
        return [
            'total_executions' => $executions->count(),
            'successful_executions' => $executions->where('status', 'completed')->count(),
            'failed_executions' => $executions->where('status', 'failed')->count(),
            'pending_executions' => $executions->where('status', 'pending')->count(),
            'running_executions' => $executions->where('status', 'running')->count(),
            'average_execution_time' => $executions->where('status', 'completed')
                ->whereNotNull('execution_time')
                ->avg('execution_time'),
            'last_execution' => $this->last_execution_date,
            'success_rate' => $executions->count() > 0 
                ? round(($executions->where('status', 'completed')->count() / $executions->count()) * 100, 2)
                : 0
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (!$template->template_code) {
                $template->template_code = $template->generateTemplateCode();
            }
        });
    }
}