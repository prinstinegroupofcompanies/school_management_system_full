<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $school = School::first();
        
        return view('settings.index', compact('settings', 'school'));
    }

    public function general()
    {
        $settings = Setting::whereIn('key', [
            'school_name',
            'school_address',
            'school_phone',
            'school_email',
            'school_website',
            'timezone',
            'date_format',
            'time_format',
            'currency',
            'language'
        ])->pluck('value', 'key');

        $school = School::first();
        
        return view('settings.general', compact('settings', 'school'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string',
            'school_phone' => 'required|string|max:255',
            'school_email' => 'required|email|max:255',
            'school_website' => 'nullable|url|max:255',
            'timezone' => 'required|string|max:255',
            'date_format' => 'required|string|max:255',
            'time_format' => 'required|string|max:255',
            'currency' => 'required|string|max:3',
            'language' => 'required|string|max:5',
        ]);

        DB::transaction(function () use ($request) {
            // Update or create school information
            $school = School::first();
            if ($school) {
                $school->update([
                    'name' => $request->school_name,
                    'address' => $request->school_address,
                    'phone' => $request->school_phone,
                    'email' => $request->school_email,
                    'website' => $request->school_website,
                ]);
            } else {
                School::create([
                    'name' => $request->school_name,
                    'address' => $request->school_address,
                    'phone' => $request->school_phone,
                    'email' => $request->school_email,
                    'website' => $request->school_website,
                    'status' => 'active',
                ]);
            }

            // Update settings
            $this->updateSetting('timezone', $request->timezone);
            $this->updateSetting('date_format', $request->date_format);
            $this->updateSetting('time_format', $request->time_format);
            $this->updateSetting('currency', $request->currency);
            $this->updateSetting('language', $request->language);

            // Clear cache if using file cache
            try {
                Cache::forget('school_settings');
            } catch (\Exception $e) {
                // Ignore cache errors for now
            }
        });

        return redirect()->route('settings.general')
            ->with('success', 'General settings updated successfully.');
    }

    public function academic()
    {
        $settings = Setting::whereIn('key', [
            'academic_year',
            'current_semester',
            'semester_start_date',
            'semester_end_date',
            'attendance_threshold',
            'passing_percentage',
            'grade_system'
        ])->pluck('value', 'key');
        
        return view('settings.academic', compact('settings'));
    }

    public function updateAcademic(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string|max:9',
            'current_semester' => 'required|string|max:1',
            'semester_start_date' => 'required|date',
            'semester_end_date' => 'required|date|after:semester_start_date',
            'attendance_threshold' => 'required|integer|min:0|max:100',
            'passing_percentage' => 'required|integer|min:0|max:100',
            'grade_system' => 'required|in:percentage,letter,gpa',
        ]);

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            $this->updateSetting($key, $value);
        }

        try {
            Cache::forget('academic_settings');
        } catch (\Exception $e) {
            // Ignore cache errors for now
        }

        return redirect()->route('settings.academic')
            ->with('success', 'Academic settings updated successfully.');
    }

    public function maintenance()
    {
        $maintenanceMode = Setting::where('key', 'maintenance_mode')->value('value') === '1';
        $maintenanceMessage = Setting::where('key', 'maintenance_message')->value('value');
        
        return view('settings.maintenance', compact('maintenanceMode', 'maintenanceMessage'));
    }

    public function toggleMaintenance(Request $request)
    {
        $request->validate([
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string',
        ]);

        $maintenanceMode = $request->maintenance_mode ? '1' : '0';
        $this->updateSetting('maintenance_mode', $maintenanceMode);
        $this->updateSetting('maintenance_message', $request->maintenance_message);

        try {
            Cache::forget('maintenance_settings');
        } catch (\Exception $e) {
            // Ignore cache errors for now
        }

        return redirect()->route('settings.maintenance')
            ->with('success', 'Maintenance mode ' . ($maintenanceMode === '1' ? 'enabled' : 'disabled') . ' successfully.');
    }

    public function cache()
    {
        return view('settings.cache');
    }

    public function clearCache()
    {
        try {
            Cache::flush();
            
            return redirect()->route('settings.cache')
                ->with('success', 'Cache cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->route('settings.cache')
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    private function updateSetting($key, $value)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
