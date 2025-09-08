<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class SchoolService
{
    /**
     * Get school configuration value.
     */
    public function config(string $key, mixed $default = null): mixed
    {
        return config("school.{$key}", $default);
    }

    /**
     * Get school currency.
     */
    public function getCurrency(): string
    {
        return $this->config('currency', 'LRD');
    }

    /**
     * Get school currency symbol.
     */
    public function getCurrencySymbol(): string
    {
        return $this->config('currency_symbol', 'L$');
    }

    /**
     * Get current academic year.
     */
    public function getAcademicYear(): string
    {
        return $this->config('academic_year', date('Y') . '-' . (date('Y') + 1));
    }

    /**
     * Get maximum file size.
     */
    public function getMaxFileSize(): int
    {
        return $this->config('max_file_size', 10240); // 10MB default
    }

    /**
     * Get allowed file types.
     */
    public function getAllowedFileTypes(): array
    {
        return $this->config('allowed_file_types', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);
    }

    /**
     * Check if backup is enabled.
     */
    public function isBackupEnabled(): bool
    {
        return $this->config('backup_enabled', true);
    }

    /**
     * Get backup retention days.
     */
    public function getBackupRetentionDays(): int
    {
        return $this->config('backup_retention_days', 30);
    }

    /**
     * Get a configuration value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("school_config_{$key}", 3600, function () use ($key, $default) {
            return $this->config($key, $default);
        });
    }

    /**
     * Set a configuration value.
     */
    public function set(string $key, mixed $value): void
    {
        Cache::put("school_config_{$key}", $value, 3600);
    }

    /**
     * Check if a configuration key exists.
     */
    public function has(string $key): bool
    {
        return $this->config($key) !== null;
    }

    /**
     * Get all configuration values.
     */
    public function all(): array
    {
        return config('school', []);
    }

    /**
     * Forget a configuration value.
     */
    public function forget(string $key): void
    {
        Cache::forget("school_config_{$key}");
    }

    /**
     * Flush all cached configuration values.
     */
    public function flush(): void
    {
        Cache::flush();
    }

    /**
     * Get school statistics.
     */
    public function getStatistics(): array
    {
        return Cache::remember('school_statistics', 3600, function () {
            return [
                'total_students' => \App\Models\Student::count(),
                'total_teachers' => \App\Models\Teacher::count(),
                'total_staff' => \App\Models\Staff::count(),
                'total_classes' => \App\Models\ClassRoom::count(),
                'total_subjects' => \App\Models\Subject::count(),
                'academic_year' => $this->getAcademicYear(),
                'currency' => $this->getCurrency(),
                'currency_symbol' => $this->getCurrencySymbol(),
            ];
        });
    }

    /**
     * Get school information.
     */
    public function getSchoolInfo(): array
    {
        return [
            'name' => config('app.name'),
            'description' => 'Comprehensive School Management System',
            'version' => '1.0.0',
            'currency' => $this->getCurrency(),
            'currency_symbol' => $this->getCurrencySymbol(),
            'academic_year' => $this->getAcademicYear(),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ];
    }
}
