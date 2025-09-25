@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
                <div class="flex items-center space-x-4">
                    <button onclick="saveAllSettings()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save All Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Settings Tabs -->
        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button onclick="showTab('school')" class="tab-button border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600" id="tab-school">
                        School Information
                    </button>
                    <button onclick="showTab('academic')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" id="tab-academic">
                        Academic Settings
                    </button>
                    <button onclick="showTab('system')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" id="tab-system">
                        System Preferences
                    </button>
                    <button onclick="showTab('notifications')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" id="tab-notifications">
                        Notifications
                    </button>
                    <button onclick="showTab('signature')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" id="tab-signature">
                        Signature Settings
                    </button>
                </nav>
            </div>

            <!-- School Information Tab -->
            <div id="tab-content-school" class="tab-content p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">School Information</h3>
                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="school">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="school_name" class="block text-sm font-medium text-gray-700">School Name</label>
                            <input type="text" name="school_name" id="school_name" value="{{ $settings['school_name'] ?? '' }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="school_code" class="block text-sm font-medium text-gray-700">School Code</label>
                            <input type="text" name="school_code" id="school_code" value="{{ $settings['school_code'] ?? '' }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="school_address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="school_address" id="school_address" rows="3" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ $settings['school_address'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label for="school_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="school_phone" id="school_phone" value="{{ $settings['school_phone'] ?? '' }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="school_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="school_email" id="school_email" value="{{ $settings['school_email'] ?? '' }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="school_website" class="block text-sm font-medium text-gray-700">Website</label>
                            <input type="url" name="school_website" id="school_website" value="{{ $settings['school_website'] ?? '' }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Save School Information
                        </button>
                    </div>
                </form>
            </div>

            <!-- Academic Settings Tab -->
            <div id="tab-content-academic" class="tab-content hidden p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Academic Settings</h3>
                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="academic">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="academic_year" class="block text-sm font-medium text-gray-700">Current Academic Year</label>
                            <input type="text" name="academic_year" id="academic_year" value="{{ $settings['academic_year'] ?? '' }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="e.g., 2024-2025">
                        </div>
                        <div>
                            <label for="semester" class="block text-sm font-medium text-gray-700">Current Semester</label>
                            <select name="semester" id="semester" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="1" {{ ($settings['semester'] ?? '') == '1' ? 'selected' : '' }}>First Semester</option>
                                <option value="2" {{ ($settings['semester'] ?? '') == '2' ? 'selected' : '' }}>Second Semester</option>
                                <option value="3" {{ ($settings['semester'] ?? '') == '3' ? 'selected' : '' }}>Third Semester</option>
                            </select>
                        </div>
                        <div>
                            <label for="grading_system" class="block text-sm font-medium text-gray-700">Grading System</label>
                            <select name="grading_system" id="grading_system" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="percentage" {{ ($settings['grading_system'] ?? '') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                <option value="letter" {{ ($settings['grading_system'] ?? '') == 'letter' ? 'selected' : '' }}>Letter Grades</option>
                                <option value="gpa" {{ ($settings['grading_system'] ?? '') == 'gpa' ? 'selected' : '' }}>GPA</option>
                            </select>
                        </div>
                        <div>
                            <label for="passing_grade" class="block text-sm font-medium text-gray-700">Passing Grade</label>
                            <input type="number" name="passing_grade" id="passing_grade" value="{{ $settings['passing_grade'] ?? '50' }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   min="0" max="100">
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Save Academic Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- System Preferences Tab -->
            <div id="tab-content-system" class="tab-content hidden p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">System Preferences</h3>
                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="system">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                            <select name="timezone" id="timezone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="UTC" {{ ($settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ ($settings['timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                <option value="America/Chicago" {{ ($settings['timezone'] ?? '') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                <option value="America/Denver" {{ ($settings['timezone'] ?? '') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                <option value="America/Los_Angeles" {{ ($settings['timezone'] ?? '') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                            </select>
                        </div>
                        <div>
                            <label for="date_format" class="block text-sm font-medium text-gray-700">Date Format</label>
                            <select name="date_format" id="date_format" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="Y-m-d" {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                <option value="d/m/Y" {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                            </select>
                        </div>
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
                            <select name="language" id="language" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="en" {{ ($settings['language'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ ($settings['language'] ?? '') == 'es' ? 'selected' : '' }}>Spanish</option>
                                <option value="fr" {{ ($settings['language'] ?? '') == 'fr' ? 'selected' : '' }}>French</option>
                            </select>
                        </div>
                        <div>
                            <label for="pagination_limit" class="block text-sm font-medium text-gray-700">Items Per Page</label>
                            <input type="number" name="pagination_limit" id="pagination_limit" value="{{ $settings['pagination_limit'] ?? '20' }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   min="10" max="100">
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Save System Preferences
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notifications Tab -->
            <div id="tab-content-notifications" class="tab-content hidden p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Notification Settings</h3>
                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="notifications">
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Email Notifications</h4>
                                <p class="text-sm text-gray-500">Send notifications via email</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_notifications" value="1" 
                                       {{ ($settings['email_notifications'] ?? false) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">SMS Notifications</h4>
                                <p class="text-sm text-gray-500">Send notifications via SMS</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="sms_notifications" value="1" 
                                       {{ ($settings['sms_notifications'] ?? false) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Push Notifications</h4>
                                <p class="text-sm text-gray-500">Send browser push notifications</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="push_notifications" value="1" 
                                       {{ ($settings['push_notifications'] ?? false) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Save Notification Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Signature Settings Tab -->
            <div id="tab-content-signature" class="tab-content hidden p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Signature Settings</h3>
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Digital Signature Management</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Upload and manage your digital signature that will appear on all grade sheets and official documents. This signature will be automatically applied to approved grades.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <a href="{{ route('admin.settings.signature') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Manage Digital Signature
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => content.classList.add('hidden'));
    
    // Remove active state from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('border-indigo-500', 'text-indigo-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(`tab-content-${tabName}`).classList.remove('hidden');
    
    // Activate selected tab button
    document.getElementById(`tab-${tabName}`).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(`tab-${tabName}`).classList.add('border-indigo-500', 'text-indigo-600');
}

function saveAllSettings() {
    // Submit all forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
    });
    
    // Show success message
    alert('All settings have been saved successfully!');
}

// Initialize with school tab
document.addEventListener('DOMContentLoaded', function() {
    showTab('school');
});
</script>
@endsection
