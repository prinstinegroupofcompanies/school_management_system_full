<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSetting;

class DataTableService
{
    /**
     * Get saved table preferences for current user.
     */
    public static function getTablePreferences(string $tableId, Request $request): array
    {
        $user = Auth::user();
        if (!$user) {
            return self::getDefaultPreferences();
        }

        $settings = UserSetting::getOrCreateForUser($user->id);
        $preferences = $settings->table_preferences ?? [];

        if (isset($preferences[$tableId])) {
            return array_merge(self::getDefaultPreferences(), $preferences[$tableId]);
        }

        return self::getDefaultPreferences();
    }

    /**
     * Save table preferences for current user.
     */
    public static function saveTablePreferences(string $tableId, array $preferences): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $settings = UserSetting::getOrCreateForUser($user->id);
        $allPreferences = $settings->table_preferences ?? [];
        $allPreferences[$tableId] = $preferences;
        $settings->update(['table_preferences' => $allPreferences]);
    }

    /**
     * Get default DataTable preferences.
     */
    public static function getDefaultPreferences(): array
    {
        return [
            'pageLength' => 25,
            'order' => [[0, 'asc']],
            'columns' => [],
            'search' => '',
            'filters' => [],
        ];
    }

    /**
     * Build DataTable configuration array.
     */
    public static function buildConfig(array $options = []): array
    {
        $defaults = [
            'processing' => true,
            'serverSide' => false,
            'pageLength' => 25,
            'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            'order' => [[0, 'asc']],
            'language' => [
                'search' => 'Search:',
                'lengthMenu' => 'Show _MENU_ entries',
                'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
                'infoEmpty' => 'No entries found',
                'infoFiltered' => '(filtered from _MAX_ total entries)',
                'zeroRecords' => 'No matching records found',
                'emptyTable' => 'No data available in table',
            ],
            'dom' => '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            'responsive' => true,
            'autoWidth' => false,
            'deferRender' => true,
        ];

        return array_merge($defaults, $options);
    }

    /**
     * Apply filters to query builder.
     */
    public static function applyFilters($query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }
    }

    /**
     * Get saved filter preferences.
     */
    public static function getFilterPreferences(string $context): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $settings = UserSetting::getOrCreateForUser($user->id);
        $preferences = $settings->filter_preferences ?? [];

        return $preferences[$context] ?? [];
    }

    /**
     * Save filter preferences.
     */
    public static function saveFilterPreferences(string $context, array $filters): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $settings = UserSetting::getOrCreateForUser($user->id);
        $allPreferences = $settings->filter_preferences ?? [];
        $allPreferences[$context] = $filters;
        $settings->update(['filter_preferences' => $allPreferences]);
    }
}

