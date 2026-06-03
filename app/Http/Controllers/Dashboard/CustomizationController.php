<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomizationController extends Controller
{
    /**
     * Show dashboard customization page.
     */
    public function index()
    {
        $user = Auth::user();
        $settings = UserSetting::getOrCreateForUser($user->id);
        
        // Available widgets based on user role
        $availableWidgets = $this->getAvailableWidgets($user);
        
        return view('dashboard.customize', compact('settings', 'availableWidgets'));
    }

    /**
     * Update dashboard widgets.
     */
    public function updateWidgets(Request $request)
    {
        $request->validate([
            'widgets' => 'required|array',
            'widgets.*' => 'string',
        ]);

        $user = Auth::user();
        $settings = UserSetting::getOrCreateForUser($user->id);
        
        // Validate widgets against available widgets
        $availableWidgets = $this->getAvailableWidgets($user);
        $validWidgets = array_intersect($request->widgets, $availableWidgets);
        
        $settings->updateWidgets(array_values($validWidgets));

        return redirect()->route('dashboard.customize')
            ->with('success', 'Dashboard widgets updated successfully.');
    }

    /**
     * Update theme preferences.
     */
    public function updateTheme(Request $request)
    {
        $request->validate([
            'dark_mode' => 'boolean',
        ]);

        $user = Auth::user();
        $settings = UserSetting::getOrCreateForUser($user->id);
        
        $theme = $settings->theme ?? ['dark_mode' => false];
        $theme['dark_mode'] = $request->has('dark_mode') && $request->dark_mode;
        
        $settings->update(['theme' => $theme]);

        return redirect()->route('dashboard.customize')
            ->with('success', 'Theme preferences updated successfully.');
    }

    /**
     * Get available widgets based on user role.
     */
    private function getAvailableWidgets($user): array
    {
        $commonWidgets = ['attendance', 'calendar', 'notifications'];
        
        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            return array_merge($commonWidgets, [
                'statistics', 'recent_activities', 'students_summary', 'teachers_summary',
                'finance_summary', 'exams_upcoming', 'fees_overdue', 'transport_summary',
            ]);
        }
        
        if ($user->hasRole('teacher')) {
            return array_merge($commonWidgets, [
                'classes', 'students', 'homework_pending', 'exams_upcoming',
                'grades_pending', 'schedule', 'announcements',
            ]);
        }
        
        if ($user->hasRole('student')) {
            return array_merge($commonWidgets, [
                'grades', 'homework', 'exams_upcoming', 'fees', 'library',
                'schedule', 'announcements',
            ]);
        }
        
        if ($user->hasRole('finance')) {
            return array_merge($commonWidgets, [
                'payments', 'receivables', 'payables', 'income_expenditure',
                'fees_summary', 'financial_reports',
            ]);
        }
        
        if ($user->hasRole('parent')) {
            return array_merge($commonWidgets, [
                'children', 'grades', 'attendance', 'fees', 'homework',
                'announcements',
            ]);
        }
        
        return $commonWidgets;
    }

    /**
     * Reset dashboard to default.
     */
    public function reset(Request $request)
    {
        $user = Auth::user();
        $settings = UserSetting::getOrCreateForUser($user->id);
        
        $defaultWidgets = $this->getAvailableWidgets($user);
        // Take first 3 as default
        $defaultWidgets = array_slice($defaultWidgets, 0, 3);
        
        $settings->update([
            'dashboard_widgets' => $defaultWidgets,
            'theme' => ['dark_mode' => false],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Dashboard reset to default successfully.']);
        }

        return redirect()->route('dashboard.customize')
            ->with('success', 'Dashboard reset to default successfully.');
    }
}
