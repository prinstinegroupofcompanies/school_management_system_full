@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
                <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Settings Navigation -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <nav class="space-y-1">
                            <a href="#password" class="settings-nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Change Password
                            </a>
                            <a href="#notifications" class="settings-nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12 7H4.828zM4.828 17L7.414 14.414a2 2 0 012.828 0L12 17H4.828z"></path>
                                </svg>
                                Notifications
                            </a>
                            <a href="#privacy" class="settings-nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Privacy
                            </a>
                            <a href="#account" class="settings-nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Account
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Account Statistics -->
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Account Statistics</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Classes Taught</span>
                                <span class="text-sm font-medium text-gray-900">{{ $stats['classes_taught'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Total Students</span>
                                <span class="text-sm font-medium text-gray-900">{{ $stats['total_students'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Subjects Taught</span>
                                <span class="text-sm font-medium text-gray-900">{{ $stats['subjects_taught'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Grades Entered</span>
                                <span class="text-sm font-medium text-gray-900">{{ $stats['total_grades'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="lg:col-span-2">
                <!-- Change Password -->
                <div id="password" class="settings-content bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Change Password</h3>
                        
                        <form method="POST" action="{{ route('teacher.settings.password') }}">
                            @csrf
                            
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    @error('current_password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <input type="password" name="password" id="password" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notification Preferences -->
                <div id="notifications" class="settings-content bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Notification Preferences</h3>
                        
                        <form method="POST" action="{{ route('teacher.settings.notifications') }}">
                            @csrf
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">Email Notifications</h4>
                                        <p class="text-sm text-gray-500">Receive notifications via email</p>
                                    </div>
                                    <input type="checkbox" name="email_notifications" value="1" {{ $teacher->email_notifications ?? true ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">SMS Notifications</h4>
                                        <p class="text-sm text-gray-500">Receive notifications via SMS</p>
                                    </div>
                                    <input type="checkbox" name="sms_notifications" value="1" {{ $teacher->sms_notifications ?? false ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">Push Notifications</h4>
                                        <p class="text-sm text-gray-500">Receive push notifications in browser</p>
                                    </div>
                                    <input type="checkbox" name="push_notifications" value="1" {{ $teacher->push_notifications ?? true ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Save Preferences
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Privacy Settings -->
                <div id="privacy" class="settings-content bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Privacy Settings</h3>
                        
                        <form method="POST" action="{{ route('teacher.settings.privacy') }}">
                            @csrf
                            
                            <div class="space-y-6">
                                <div>
                                    <label for="profile_visibility" class="block text-sm font-medium text-gray-700 mb-2">Profile Visibility</label>
                                    <select name="profile_visibility" id="profile_visibility" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="public" {{ ($teacher->profile_visibility ?? 'public') == 'public' ? 'selected' : '' }}>Public</option>
                                        <option value="private" {{ ($teacher->profile_visibility ?? 'public') == 'private' ? 'selected' : '' }}>Private</option>
                                        <option value="friends" {{ ($teacher->profile_visibility ?? 'public') == 'friends' ? 'selected' : '' }}>Friends Only</option>
                                    </select>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">Show Email Address</h4>
                                            <p class="text-sm text-gray-500">Display email address on profile</p>
                                        </div>
                                        <input type="checkbox" name="show_email" value="1" {{ $teacher->show_email ?? true ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">Show Phone Number</h4>
                                            <p class="text-sm text-gray-500">Display phone number on profile</p>
                                        </div>
                                        <input type="checkbox" name="show_phone" value="1" {{ $teacher->show_phone ?? false ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Save Privacy Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Account Management -->
                <div id="account" class="settings-content bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Account Management</h3>
                        
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-medium text-red-900 mb-4">Danger Zone</h4>
                            <p class="text-sm text-gray-500 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
                            
                            <form method="POST" action="{{ route('teacher.settings.delete-account') }}" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                                @csrf
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-2">Enter your password to confirm</label>
                                        <input type="password" name="password" id="delete_password" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    </div>
                                    
                                    <div>
                                        <label for="delete_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Type "DELETE" to confirm</label>
                                        <input type="text" name="confirmation" id="delete_confirmation" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete Account
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Settings navigation
    const navItems = document.querySelectorAll('.settings-nav-item');
    const contentSections = document.querySelectorAll('.settings-content');
    
    // Show first section by default
    if (contentSections.length > 0) {
        contentSections[0].style.display = 'block';
        navItems[0].classList.add('bg-gray-100', 'text-gray-900');
    }
    
    navItems.forEach((item, index) => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all items
            navItems.forEach(nav => {
                nav.classList.remove('bg-gray-100', 'text-gray-900');
                nav.classList.add('text-gray-700');
            });
            
            // Hide all content sections
            contentSections.forEach(section => {
                section.style.display = 'none';
            });
            
            // Add active class to clicked item
            this.classList.add('bg-gray-100', 'text-gray-900');
            this.classList.remove('text-gray-700');
            
            // Show corresponding content section
            if (contentSections[index]) {
                contentSections[index].style.display = 'block';
            }
        });
    });
});
</script>
@endsection
