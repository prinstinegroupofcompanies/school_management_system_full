<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
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
            // Get system settings
            $systemSettings = SystemSetting::all()->keyBy('key');
            
            // Update general settings with database values
            $generalSettings = [
                'school_name' => $systemSettings->get('school_name', $generalSettings['school_name']),
                'school_address' => $systemSettings->get('school_address', $generalSettings['school_address']),
                'school_phone' => $systemSettings->get('school_phone', $generalSettings['school_phone']),
                'school_email' => $systemSettings->get('school_email', $generalSettings['school_email']),
                'academic_year' => $systemSettings->get('academic_year', $generalSettings['academic_year']),
                'semester' => $systemSettings->get('semester', $generalSettings['semester']),
                'timezone' => $systemSettings->get('timezone', $generalSettings['timezone']),
                'currency' => $systemSettings->get('currency', $generalSettings['currency']),
            ];

            // Update attendance settings with database values
            $attendanceSettings = [
                'attendance_marking_time' => $systemSettings->get('attendance_marking_time', $attendanceSettings['attendance_marking_time']),
                'late_marking_time' => $systemSettings->get('late_marking_time', $attendanceSettings['late_marking_time']),
                'attendance_grace_period' => $systemSettings->get('attendance_grace_period', $attendanceSettings['attendance_grace_period']),
                'auto_mark_absent' => $systemSettings->get('auto_mark_absent', $attendanceSettings['auto_mark_absent']),
            ];

            // Update notification settings with database values
            $notificationSettings = [
                'email_notifications' => $systemSettings->get('email_notifications', $notificationSettings['email_notifications']),
                'sms_notifications' => $systemSettings->get('sms_notifications', $notificationSettings['sms_notifications']),
                'push_notifications' => $systemSettings->get('push_notifications', $notificationSettings['push_notifications']),
            ];

        } catch (\Exception $e) {
            \Log::error('SettingsController index error: ' . $e->getMessage());
            // Use default settings if database issues
        }

        return view('settings.index', compact(
            'generalSettings',
            'attendanceSettings',
            'notificationSettings'
        ));
    }

    public function attendance()
    {
        try {
            // Get system settings
            $systemSettings = SystemSetting::all()->keyBy('key');
            
            // Get attendance settings
            $attendanceSettings = [
                'attendance_marking_time' => $systemSettings->get('attendance_marking_time', '09:00'),
                'late_marking_time' => $systemSettings->get('late_marking_time', '09:30'),
                'attendance_grace_period' => $systemSettings->get('attendance_grace_period', 15),
                'auto_mark_absent' => $systemSettings->get('auto_mark_absent', false),
            ];

            return view('settings.attendance', compact('attendanceSettings'));
        } catch (\Exception $e) {
            \Log::error('SettingsController attendance error: ' . $e->getMessage());
            
            // Fallback settings if database issues
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
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            return redirect()->route('settings.index')->with('success', 'Attendance settings updated successfully.');
        } catch (\Exception $e) {
            \Log::error('SettingsController updateAttendance error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update attendance settings. Please try again.');
        }
    }

    public function updateGeneral(Request $request)
    {
        try {
            $request->validate([
                'school_name' => 'required|string|max:255',
                'school_address' => 'nullable|string',
                'school_phone' => 'nullable|string|max:20',
                'school_email' => 'nullable|email|max:255',
                'academic_year' => 'required|integer|min:2020|max:2030',
                'semester' => 'required|integer|min:1|max:3',
                'timezone' => 'required|string|max:50',
                'currency' => 'required|string|max:3',
            ]);

            $settings = [
                'school_name' => $request->school_name,
                'school_address' => $request->school_address,
                'school_phone' => $request->school_phone,
                'school_email' => $request->school_email,
                'academic_year' => $request->academic_year,
                'semester' => $request->semester,
                'timezone' => $request->timezone,
                'currency' => $request->currency,
            ];

            foreach ($settings as $key => $value) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            return redirect()->route('settings.index')->with('success', 'General settings updated successfully.');
        } catch (\Exception $e) {
            \Log::error('SettingsController updateGeneral error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update general settings. Please try again.');
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
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            return redirect()->route('settings.index')->with('success', 'Notification settings updated successfully.');
        } catch (\Exception $e) {
            \Log::error('SettingsController updateNotifications error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update notification settings. Please try again.');
        }
    }
}
