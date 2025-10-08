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
        // Initialize default settings
        $generalSettings = [
            'school_name' => 'School Management System',
            'school_address' => '',
            'school_phone' => '',
            'school_email' => '',
            'academic_year' => now()->year,
            'semester' => 1,
            'timezone' => 'UTC',
            'currency' => 'LRD',
        ];

        $attendanceSettings = [
            'attendance_marking_time' => '09:00',
            'late_marking_time' => '09:30',
            'attendance_grace_period' => 15,
            'auto_mark_absent' => false,
        ];

        $notificationSettings = [
            'email_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => true,
        ];

        try {
            $settings = Setting::all()->pluck('value', 'key');
            $school = School::first();
            
            // Update general settings with database values
            $generalSettings = [
                'school_name' => $settings->get('school_name', $generalSettings['school_name']),
                'school_address' => $settings->get('school_address', $generalSettings['school_address']),
                'school_phone' => $settings->get('school_phone', $generalSettings['school_phone']),
                'school_email' => $settings->get('school_email', $generalSettings['school_email']),
                'academic_year' => $settings->get('academic_year', $generalSettings['academic_year']),
                'semester' => $settings->get('semester', $generalSettings['semester']),
                'timezone' => $settings->get('timezone', $generalSettings['timezone']),
                'currency' => $settings->get('currency', $generalSettings['currency']),
            ];

            // Update attendance settings with database values
            $attendanceSettings = [
                'attendance_marking_time' => $settings->get('attendance_marking_time', $attendanceSettings['attendance_marking_time']),
                'late_marking_time' => $settings->get('late_marking_time', $attendanceSettings['late_marking_time']),
                'attendance_grace_period' => $settings->get('attendance_grace_period', $attendanceSettings['attendance_grace_period']),
                'auto_mark_absent' => $settings->get('auto_mark_absent', $attendanceSettings['auto_mark_absent']),
            ];

            // Update notification settings with database values
            $notificationSettings = [
                'email_notifications' => $settings->get('email_notifications', $notificationSettings['email_notifications']),
                'sms_notifications' => $settings->get('sms_notifications', $notificationSettings['sms_notifications']),
                'push_notifications' => $settings->get('push_notifications', $notificationSettings['push_notifications']),
            ];

        } catch (\Exception $e) {
            \Log::error('SettingController index error: ' . $e->getMessage());
            // Use default settings if database issues
        }

        return view('settings.index', compact(
            'generalSettings',
            'attendanceSettings',
            'notificationSettings'
        ));
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

    public function attendance()
    {
        try {
            $settings = Setting::whereIn('key', [
                'attendance_marking_time',
                'late_marking_time',
                'attendance_grace_period',
                'auto_mark_absent'
            ])->pluck('value', 'key');
            
            $attendanceSettings = [
                'attendance_marking_time' => $settings->get('attendance_marking_time', '09:00'),
                'late_marking_time' => $settings->get('late_marking_time', '09:30'),
                'attendance_grace_period' => $settings->get('attendance_grace_period', 15),
                'auto_mark_absent' => $settings->get('auto_mark_absent', false),
            ];

            return view('settings.attendance', compact('attendanceSettings'));
        } catch (\Exception $e) {
            \Log::error('SettingController attendance error: ' . $e->getMessage());
            
            $attendanceSettings = [
                'attendance_marking_time' => '09:00',
                'late_marking_time' => '09:30',
                'attendance_grace_period' => 15,
                'auto_mark_absent' => false,
            ];

            return view('settings.attendance', compact('attendanceSettings'));
        }
    }

    public function updateAttendance(Request $request)
    {
        try {
            $request->validate([
                'attendance_marking_time' => 'required|date_format:H:i',
                'late_marking_time' => 'required|date_format:H:i',
                'attendance_grace_period' => 'required|integer|min:0|max:60',
                'auto_mark_absent' => 'boolean',
            ]);

            $settings = [
                'attendance_marking_time' => $request->attendance_marking_time,
                'late_marking_time' => $request->late_marking_time,
                'attendance_grace_period' => $request->attendance_grace_period,
                'auto_mark_absent' => $request->boolean('auto_mark_absent'),
            ];

            foreach ($settings as $key => $value) {
                $this->updateSetting($key, $value);
            }

            return redirect()->route('settings.index')->with('success', 'Attendance settings updated successfully.');
        } catch (\Exception $e) {
            \Log::error('SettingController updateAttendance error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update attendance settings. Please try again.');
        }
    }

    public function notifications()
    {
        try {
            $settings = Setting::whereIn('key', [
                'email_notifications',
                'sms_notifications',
                'push_notifications'
            ])->pluck('value', 'key');
            
            $notificationSettings = [
                'email_notifications' => $settings->get('email_notifications', true),
                'sms_notifications' => $settings->get('sms_notifications', false),
                'push_notifications' => $settings->get('push_notifications', true),
            ];

            return view('settings.notifications', compact('notificationSettings'));
        } catch (\Exception $e) {
            \Log::error('SettingController notifications error: ' . $e->getMessage());
            
            $notificationSettings = [
                'email_notifications' => true,
                'sms_notifications' => false,
                'push_notifications' => true,
            ];

            return view('settings.notifications', compact('notificationSettings'));
        }
    }

    public function updateNotifications(Request $request)
    {
        try {
            $request->validate([
                'email_notifications' => 'boolean',
                'sms_notifications' => 'boolean',
                'push_notifications' => 'boolean',
            ]);

            $settings = [
                'email_notifications' => $request->boolean('email_notifications'),
                'sms_notifications' => $request->boolean('sms_notifications'),
                'push_notifications' => $request->boolean('push_notifications'),
            ];

            foreach ($settings as $key => $value) {
                $this->updateSetting($key, $value);
            }

            return redirect()->route('settings.index')->with('success', 'Notification settings updated successfully.');
        } catch (\Exception $e) {
            \Log::error('SettingController updateNotifications error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update notification settings. Please try again.');
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
