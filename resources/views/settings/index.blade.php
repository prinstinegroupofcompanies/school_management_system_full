@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Settings</h1>
        <!-- Settings Tabs -->
        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button class="settings-tab active" data-tab="general">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        General
                    </button>
                    <button class="settings-tab" data-tab="attendance">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Attendance
                    </button>
                    <button class="settings-tab" data-tab="notifications">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12 7H4.828z"></path>
                        </svg>
                        Notifications
                    </button>
                </nav>
            </div>

            <!-- General Settings -->
            <div id="general-tab" class="settings-content active">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">General Settings</h3>
                    <form method="POST" action="{{ route('settings.general.update') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="school_name" class="block text-sm font-medium text-gray-700">School Name</label>
                                <input type="text" name="school_name" id="school_name" value="{{ $generalSettings['school_name'] }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="school_email" class="block text-sm font-medium text-gray-700">School Email</label>
                                <input type="email" name="school_email" id="school_email" value="{{ $generalSettings['school_email'] }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="academic_year" class="block text-sm font-medium text-gray-700">Academic Year</label>
                                <input type="number" name="academic_year" id="academic_year" value="{{ $generalSettings['academic_year'] }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                                <select name="semester" id="semester" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="1" {{ $generalSettings['semester'] == 1 ? 'selected' : '' }}>Semester 1</option>
                                    <option value="2" {{ $generalSettings['semester'] == 2 ? 'selected' : '' }}>Semester 2</option>
                                    <option value="3" {{ $generalSettings['semester'] == 3 ? 'selected' : '' }}>Semester 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Save General Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attendance Settings -->
            <div id="attendance-tab" class="settings-content">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Attendance Settings</h3>
                    <form method="POST" action="{{ route('settings.attendance.update') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="attendance_marking_time" class="block text-sm font-medium text-gray-700">Attendance Marking Time</label>
                                <input type="time" name="attendance_marking_time" id="attendance_marking_time" value="{{ $attendanceSettings['attendance_marking_time'] }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="late_marking_time" class="block text-sm font-medium text-gray-700">Late Marking Time</label>
                                <input type="time" name="late_marking_time" id="late_marking_time" value="{{ $attendanceSettings['late_marking_time'] }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="attendance_grace_period" class="block text-sm font-medium text-gray-700">Grace Period (minutes)</label>
                                <input type="number" name="attendance_grace_period" id="attendance_grace_period" value="{{ $attendanceSettings['attendance_grace_period'] }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="auto_mark_absent" id="auto_mark_absent" value="1" {{ $attendanceSettings['auto_mark_absent'] ? 'checked' : '' }} 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="auto_mark_absent" class="ml-2 block text-sm text-gray-900">Auto Mark Absent</label>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Save Attendance Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notification Settings -->
            <div id="notifications-tab" class="settings-content">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Settings</h3>
                    <form method="POST" action="{{ route('settings.notifications.update') }}">
                        @csrf
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="email_notifications" id="email_notifications" value="1" {{ $notificationSettings['email_notifications'] ? 'checked' : '' }} 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="email_notifications" class="ml-2 block text-sm text-gray-900">Email Notifications</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="sms_notifications" id="sms_notifications" value="1" {{ $notificationSettings['sms_notifications'] ? 'checked' : '' }} 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="sms_notifications" class="ml-2 block text-sm text-gray-900">SMS Notifications</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="push_notifications" id="push_notifications" value="1" {{ $notificationSettings['push_notifications'] ? 'checked' : '' }} 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="push_notifications" class="ml-2 block text-sm text-gray-900">Push Notifications</label>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Save Notification Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabs = document.querySelectorAll('.settings-tab');
                const contents = document.querySelectorAll('.settings-content');

                tabs.forEach(tab => {
                    tab.addEventListener('click', function() {
                        const targetTab = this.getAttribute('data-tab');
                        
                        // Remove active class from all tabs and contents
                        tabs.forEach(t => t.classList.remove('active'));
                        contents.forEach(c => c.classList.remove('active'));
                        
                        // Add active class to clicked tab and corresponding content
                        this.classList.add('active');
                        document.getElementById(targetTab + '-tab').classList.add('active');
                    });
                });
            });
        </script>

        <style>
            .settings-tab {
                @apply whitespace-nowrap py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300;
            }
            .settings-tab.active {
                @apply border-indigo-500 text-indigo-600;
            }
            .settings-content {
                @apply hidden;
            }
            .settings-content.active {
                @apply block;
            }
        </style>
    </div>
</div>
@endsection