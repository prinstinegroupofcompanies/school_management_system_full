<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'category', 'subcategory', 'action', 'description',
        'subject_type', 'subject_id', 'causer_type', 'causer_id', 'properties',
        'ip_address', 'user_agent', 'location', 'device_info', 'session_id',
        'request_id', 'response_time', 'status_code', 'error_message',
        'metadata', 'is_successful', 'is_important', 'is_sensitive',
        'created_by', 'notes'
    ];

    protected $casts = [
        'properties' => 'array', 'device_info' => 'array', 'metadata' => 'array',
        'response_time' => 'integer', 'status_code' => 'integer',
        'is_successful' => 'boolean', 'is_important' => 'boolean',
        'is_sensitive' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->morphTo('subject');
    }

    public function causer(): BelongsTo
    {
        return $this->morphTo('causer');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_successful', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySubcategory($query, $subcategory)
    {
        return $query->where('subcategory', $subcategory);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBySubjectType($query, $type)
    {
        return $query->where('subject_type', $type);
    }

    public function scopeBySubjectId($query, $id)
    {
        return $query->where('subject_id', $id);
    }

    public function scopeByCauserType($query, $type)
    {
        return $query->where('causer_type', $type);
    }

    public function scopeByCauserId($query, $id)
    {
        return $query->where('causer_id', $id);
    }

    public function scopeByCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('is_successful', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('is_successful', false);
    }

    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    public function scopeSensitive($query)
    {
        return $query->where('is_sensitive', true);
    }

    public function scopeByIpAddress($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    public function scopeBySessionId($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeByRequestId($query, $requestId)
    {
        return $query->where('request_id', $requestId);
    }

    public function scopeByStatusCode($query, $code)
    {
        return $query->where('status_code', $code);
    }

    public function scopeByResponseTimeRange($query, $minTime, $maxTime)
    {
        return $query->whereBetween('response_time', [$minTime, $maxTime]);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByTimeRange($query, $startTime, $endTime)
    {
        return $query->whereBetween('created_at', [$startTime, $endTime]);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    public function scopeThisYear($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfYear(),
            now()->endOfYear()
        ]);
    }

    public function scopeByUserAgent($query, $userAgent)
    {
        return $query->where('user_agent', 'like', "%{$userAgent}%");
    }

    public function scopeByDeviceType($query, $deviceType)
    {
        return $query->whereJsonContains('device_info->type', $deviceType);
    }

    public function scopeByBrowser($query, $browser)
    {
        return $query->whereJsonContains('device_info->browser', $browser);
    }

    public function scopeByOperatingSystem($query, $os)
    {
        return $query->whereJsonContains('device_info->os', $os);
    }

    public function scopeByMetadata($query, $key, $value = null)
    {
        if ($value === null) {
            return $query->whereJsonContains('metadata', $key);
        }
        return $query->whereJsonContains('metadata->' . $key, $value);
    }

    public function scopeByProperties($query, $key, $value = null)
    {
        if ($value === null) {
            return $query->whereJsonContains('properties', $key);
        }
        return $query->whereJsonContains('properties->' . $key, $value);
    }

    public function scopeByCreatedDateOrder($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByResponseTimeOrder($query)
    {
        return $query->orderBy('response_time', 'desc');
    }

    public function scopeByStatusCodeOrder($query)
    {
        return $query->orderBy('status_code', 'asc');
    }

    public function scopeByTypeOrder($query)
    {
        return $query->orderBy('type')->orderBy('created_at', 'desc');
    }

    public function scopeByCategoryOrder($query)
    {
        return $query->orderBy('category')->orderBy('created_at', 'desc');
    }

    public function scopeByActionOrder($query)
    {
        return $query->orderBy('action')->orderBy('created_at', 'desc');
    }

    public function scopeByUserOrder($query)
    {
        return $query->orderBy('user_id')->orderBy('created_at', 'desc');
    }

    public function scopeByImportanceOrder($query)
    {
        return $query->orderBy('is_important', 'desc')->orderBy('created_at', 'desc');
    }

    public function scopeBySuccessOrder($query)
    {
        return $query->orderBy('is_successful', 'desc')->orderBy('created_at', 'desc');
    }

    public function getTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->type));
    }

    public function getCategoryDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->category));
    }

    public function getSubcategoryDisplayAttribute(): string
    {
        if (!$this->subcategory) return 'N/A';
        return ucwords(str_replace('_', ' ', $this->subcategory));
    }

    public function getActionDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->action));
    }

    public function getSubjectTypeDisplayAttribute(): string
    {
        if (!$this->subject_type) return 'N/A';
        return ucwords(str_replace('_', ' ', $this->subject_type));
    }

    public function getCauserTypeDisplayAttribute(): string
    {
        if (!$this->causer_type) return 'N/A';
        return ucwords(str_replace('_', ' ', $this->causer_type));
    }

    public function getPropertiesDisplayAttribute(): string
    {
        if (!$this->properties || empty($this->properties)) {
            return 'No properties';
        }
        
        $display = [];
        foreach ($this->properties as $key => $value) {
            $display[] = ucwords(str_replace('_', ' ', $key)) . ': ' . $value;
        }
        
        return implode(', ', $display);
    }

    public function getDeviceInfoDisplayAttribute(): string
    {
        if (!$this->device_info || empty($this->device_info)) {
            return 'No device information';
        }
        
        $display = [];
        foreach ($this->device_info as $key => $value) {
            $display[] = ucwords(str_replace('_', ' ', $key)) . ': ' . $value;
        }
        
        return implode(', ', $display);
    }

    public function getMetadataDisplayAttribute(): string
    {
        if (!$this->metadata || empty($this->metadata)) {
            return 'No metadata';
        }
        
        $display = [];
        foreach ($this->metadata as $key => $value) {
            $display[] = ucwords(str_replace('_', ' ', $key)) . ': ' . $value;
        }
        
        return implode(', ', $display);
    }

    public function getNotesDisplayAttribute(): string
    {
        return $this->notes ?: 'No additional notes';
    }

    public function getCreatedByDisplayAttribute(): string
    {
        if (!$this->created_by) return 'System';
        return $this->createdBy->name ?? 'Unknown';
    }

    public function getSubjectDisplayAttribute(): string
    {
        if (!$this->subject_type || !$this->subject_id) return 'N/A';
        
        $subject = $this->subject;
        if ($subject) {
            return $subject->name ?? 'Unknown';
        }
        
        return $this->subject_type . ' #' . $this->subject_id;
    }

    public function getCauserDisplayAttribute(): string
    {
        if (!$this->causer_type || !$this->causer_id) return 'N/A';
        
        $causer = $this->causer;
        if ($causer) {
            return $causer->name ?? 'Unknown';
        }
        
        return $this->causer_type . ' #' . $this->causer_id;
    }

    public function getIpAddressDisplayAttribute(): string
    {
        return $this->ip_address ?: 'Unknown';
    }

    public function getLocationDisplayAttribute(): string
    {
        return $this->location ?: 'Unknown location';
    }

    public function getSessionIdDisplayAttribute(): string
    {
        return $this->session_id ?: 'No session';
    }

    public function getRequestIdDisplayAttribute(): string
    {
        return $this->request_id ?: 'No request ID';
    }

    public function getResponseTimeDisplayAttribute(): string
    {
        if (!$this->response_time) return 'N/A';
        return $this->response_time . 'ms';
    }

    public function getStatusCodeDisplayAttribute(): string
    {
        if (!$this->status_code) return 'N/A';
        return $this->status_code;
    }

    public function getStatusCodeColorAttribute(): string
    {
        if (!$this->status_code) return 'secondary';
        
        if ($this->status_code >= 200 && $this->status_code < 300) return 'success';
        if ($this->status_code >= 300 && $this->status_code < 400) return 'info';
        if ($this->status_code >= 400 && $this->status_code < 500) return 'warning';
        if ($this->status_code >= 500) return 'danger';
        
        return 'secondary';
    }

    public function getErrorMessageDisplayAttribute(): string
    {
        return $this->error_message ?: 'No error';
    }

    public function getIsSuccessfulAttribute(): bool
    {
        return $this->is_successful;
    }

    public function getIsImportantAttribute(): bool
    {
        return $this->is_important;
    }

    public function getIsSensitiveAttribute(): bool
    {
        return $this->is_sensitive;
    }

    public function getIsFailedAttribute(): bool
    {
        return !$this->is_successful;
    }

    public function getIsNormalAttribute(): bool
    {
        return !$this->is_important;
    }

    public function getIsNonSensitiveAttribute(): bool
    {
        return !$this->is_sensitive;
    }

    public function getIsSuccessfulColorAttribute(): string
    {
        return $this->is_successful ? 'success' : 'danger';
    }

    public function getIsImportantColorAttribute(): string
    {
        return $this->is_important ? 'warning' : 'secondary';
    }

    public function getIsSensitiveColorAttribute(): string
    {
        return $this->is_sensitive ? 'danger' : 'secondary';
    }

    public function getIsSuccessfulDisplayAttribute(): string
    {
        return $this->is_successful ? 'Success' : 'Failed';
    }

    public function getIsImportantDisplayAttribute(): string
    {
        return $this->is_important ? 'Important' : 'Normal';
    }

    public function getIsSensitiveDisplayAttribute(): string
    {
        return $this->is_sensitive ? 'Sensitive' : 'Public';
    }

    public function getDaysSinceCreationAttribute(): int
    {
        return now()->diffInDays($this->created_at);
    }

    public function getHoursSinceCreationAttribute(): int
    {
        return now()->diffInHours($this->created_at);
    }

    public function getMinutesSinceCreationAttribute(): int
    {
        return now()->diffInMinutes($this->created_at);
    }

    public function getSecondsSinceCreationAttribute(): int
    {
        return now()->diffInSeconds($this->created_at);
    }

    public function getTimeAgoAttribute(): string
    {
        $diff = now()->diff($this->created_at);
        
        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        } elseif ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        } elseif ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'Just now';
        }
    }

    public function getResponseTimeColorAttribute(): string
    {
        if (!$this->response_time) return 'secondary';
        
        if ($this->response_time < 100) return 'success';
        if ($this->response_time < 500) return 'info';
        if ($this->response_time < 1000) return 'warning';
        
        return 'danger';
    }

    public function getResponseTimeLevelAttribute(): string
    {
        if (!$this->response_time) return 'Unknown';
        
        if ($this->response_time < 100) return 'Excellent';
        if ($this->response_time < 500) return 'Good';
        if ($this->response_time < 1000) return 'Fair';
        
        return 'Poor';
    }

    public function getDeviceTypeDisplayAttribute(): string
    {
        if (!$this->device_info || !isset($this->device_info['type'])) {
            return 'Unknown';
        }
        return ucwords($this->device_info['type']);
    }

    public function getBrowserDisplayAttribute(): string
    {
        if (!$this->device_info || !isset($this->device_info['browser'])) {
            return 'Unknown';
        }
        return $this->device_info['browser'];
    }

    public function getOperatingSystemDisplayAttribute(): string
    {
        if (!$this->device_info || !isset($this->device_info['os'])) {
            return 'Unknown';
        }
        return $this->device_info['os'];
    }

    public function getActivitySummaryAttribute(): string
    {
        $summary = $this->user->name ?? 'Unknown User';
        
        $summary .= ' - ' . $this->action_display;
        
        if ($this->subject_type) {
            $summary .= ' on ' . $this->subject_type_display;
        }
        
        if ($this->category) {
            $summary .= ' (' . $this->category_display . ')';
        }
        
        $summary .= ' - ' . $this->is_successful_display;
        
        if ($this->is_important) {
            $summary .= ' - IMPORTANT';
        }
        
        return $summary;
    }

    public function getTechnicalSummaryAttribute(): string
    {
        $summary = [];
        
        if ($this->ip_address) {
            $summary[] = 'IP: ' . $this->ip_address;
        }
        
        if ($this->location) {
            $summary[] = 'Location: ' . $this->location;
        }
        
        if ($this->session_id) {
            $summary[] = 'Session: ' . $this->session_id;
        }
        
        if ($this->request_id) {
            $summary[] = 'Request: ' . $this->request_id;
        }
        
        if ($this->response_time) {
            $summary[] = 'Response: ' . $this->response_time_display;
        }
        
        if ($this->status_code) {
            $summary[] = 'Status: ' . $this->status_code;
        }
        
        return empty($summary) ? 'No technical details' : implode(' | ', $summary);
    }

    public function getDeviceSummaryAttribute(): string
    {
        if (!$this->device_info || empty($this->device_info)) {
            return 'No device information';
        }
        
        $summary = [];
        
        if (isset($this->device_info['type'])) {
            $summary[] = 'Type: ' . $this->device_type_display;
        }
        
        if (isset($this->device_info['browser'])) {
            $summary[] = 'Browser: ' . $this->browser_display;
        }
        
        if (isset($this->device_info['os'])) {
            $summary[] = 'OS: ' . $this->operating_system_display;
        }
        
        if (isset($this->device_info['version'])) {
            $summary[] = 'Version: ' . $this->device_info['version'];
        }
        
        return implode(' | ', $summary);
    }

    public function canBeViewed(): bool
    {
        return true; // All activities can be viewed
    }

    public function canBeEdited(): bool
    {
        return false; // Activities are read-only
    }

    public function canBeDeleted(): bool
    {
        return false; // Activities are read-only
    }

    public function canBeMarkedAsImportant(): bool
    {
        return !$this->is_important;
    }

    public function canBeMarkedAsNormal(): bool
    {
        return $this->is_important;
    }

    public function canBeMarkedAsSensitive(): bool
    {
        return !$this->is_sensitive;
    }

    public function canBeMarkedAsPublic(): bool
    {
        return $this->is_sensitive;
    }

    public function markAsImportant(): void
    {
        if ($this->can_be_marked_as_important) {
            $this->is_important = true;
            $this->save();
        }
    }

    public function markAsNormal(): void
    {
        if ($this->can_be_marked_as_normal) {
            $this->is_important = false;
            $this->save();
        }
    }

    public function markAsSensitive(): void
    {
        if ($this->can_be_marked_as_sensitive) {
            $this->is_sensitive = true;
            $this->save();
        }
    }

    public function markAsPublic(): void
    {
        if ($this->can_be_marked_as_public) {
            $this->is_sensitive = false;
            $this->save();
        }
    }

    public function addNote(string $note): void
    {
        $this->notes = ($this->notes ? $this->notes . "\n" : '') . $note;
        $this->save();
    }

    public function addMetadata(string $key, $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
        $this->save();
    }

    public function removeMetadata(string $key): void
    {
        $metadata = $this->metadata ?? [];
        unset($metadata[$key]);
        $this->metadata = $metadata;
        $this->save();
    }

    public function getMetadata(string $key, $default = null)
    {
        $metadata = $this->metadata ?? [];
        return $metadata[$key] ?? $default;
    }

    public function hasMetadata(string $key): bool
    {
        $metadata = $this->metadata ?? [];
        return isset($metadata[$key]);
    }

    public function addProperty(string $key, $value): void
    {
        $properties = $this->properties ?? [];
        $properties[$key] = $value;
        $this->properties = $properties;
        $this->save();
    }

    public function removeProperty(string $key): void
    {
        $properties = $this->properties ?? [];
        unset($properties[$key]);
        $this->properties = $properties;
        $this->save();
    }

    public function getProperty(string $key, $default = null)
    {
        $properties = $this->properties ?? [];
        return $properties[$key] ?? $default;
    }

    public function hasProperty(string $key): bool
    {
        $properties = $this->properties ?? [];
        return isset($properties[$key]);
    }

    public function getActivityStatistics(): array
    {
        return [
            'type' => $this->type,
            'category' => $this->category,
            'subcategory' => $this->subcategory,
            'action' => $this->action,
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'causer_type' => $this->causer_type,
            'causer_id' => $this->causer_id,
            'properties' => $this->properties,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'location' => $this->location,
            'device_info' => $this->device_info,
            'session_id' => $this->session_id,
            'request_id' => $this->request_id,
            'response_time' => $this->response_time,
            'status_code' => $this->status_code,
            'error_message' => $this->error_message,
            'metadata' => $this->metadata,
            'is_successful' => $this->is_successful,
            'is_important' => $this->is_important,
            'is_sensitive' => $this->is_sensitive,
            'is_failed' => $this->is_failed,
            'is_normal' => $this->is_normal,
            'is_non_sensitive' => $this->is_non_sensitive,
            'is_successful_color' => $this->is_successful_color,
            'is_important_color' => $this->is_important_color,
            'is_sensitive_color' => $this->is_sensitive_color,
            'is_successful_display' => $this->is_successful_display,
            'is_important_display' => $this->is_important_display,
            'is_sensitive_display' => $this->is_sensitive_display,
            'days_since_creation' => $this->days_since_creation,
            'hours_since_creation' => $this->hours_since_creation,
            'minutes_since_creation' => $this->minutes_since_creation,
            'seconds_since_creation' => $this->seconds_since_creation,
            'time_ago' => $this->time_ago,
            'response_time_color' => $this->response_time_color,
            'response_time_level' => $this->response_time_level,
            'status_code_color' => $this->status_code_color,
            'device_type_display' => $this->device_type_display,
            'browser_display' => $this->browser_display,
            'operating_system_display' => $this->operating_system_display,
            'can_be_viewed' => $this->can_be_viewed,
            'can_be_edited' => $this->can_be_edited,
            'can_be_deleted' => $this->can_be_deleted,
            'can_be_marked_as_important' => $this->can_be_marked_as_important,
            'can_be_marked_as_normal' => $this->can_be_marked_as_normal,
            'can_be_marked_as_sensitive' => $this->can_be_marked_as_sensitive,
            'can_be_marked_as_public' => $this->can_be_marked_as_public
        ];
    }
}
