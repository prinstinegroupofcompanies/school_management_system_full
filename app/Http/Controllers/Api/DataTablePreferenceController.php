<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataTablePreferenceController extends Controller
{
    /**
     * Save table preferences.
     */
    public function save(Request $request)
    {
        $request->validate([
            'table_id' => 'required|string',
            'preferences' => 'required|array',
        ]);

        DataTableService::saveTablePreferences(
            $request->table_id,
            $request->preferences
        );

        return response()->json(['message' => 'Preferences saved successfully']);
    }

    /**
     * Get table preferences.
     */
    public function get(Request $request, string $tableId)
    {
        $preferences = DataTableService::getTablePreferences($tableId, $request);
        return response()->json($preferences);
    }

    /**
     * Save filter preferences.
     */
    public function saveFilters(Request $request)
    {
        $request->validate([
            'context' => 'required|string',
            'filters' => 'required|array',
        ]);

        DataTableService::saveFilterPreferences(
            $request->context,
            $request->filters
        );

        return response()->json(['message' => 'Filter preferences saved successfully']);
    }

    /**
     * Get filter preferences.
     */
    public function getFilters(Request $request, string $context)
    {
        $filters = DataTableService::getFilterPreferences($context);
        return response()->json($filters);
    }
}
