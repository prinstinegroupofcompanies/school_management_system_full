@extends('layouts.app')

@section('title', 'Privacy Settings')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Privacy Settings</h1>
                    <p class="mt-2 text-gray-600">Control who can see your information</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('student.settings.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Settings
                    </a>
                </div>
            </div>
        </div>

        <!-- Privacy Settings Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form method="POST" action="{{ route('student.settings.privacy.update') }}">
                @csrf
                @method('PUT')
                
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Profile Visibility</h3>
                    <p class="mt-1 text-sm text-gray-500">Control who can see your profile information</p>
                </div>

                <div class="px-6 py-4 space-y-6">
                    <!-- Profile Visibility -->
                    <div>
                        <label for="profile_visibility" class="block text-sm font-medium text-gray-700 mb-2">Profile Visibility</label>
                        <select name="profile_visibility" id="profile_visibility" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="public" {{ $privacySettings['profile_visibility'] === 'public' ? 'selected' : '' }}>Public - Everyone can see</option>
                            <option value="classmates" {{ $privacySettings['profile_visibility'] === 'classmates' ? 'selected' : '' }}>Classmates - Only your classmates</option>
                            <option value="friends" {{ $privacySettings['profile_visibility'] === 'friends' ? 'selected' : '' }}>Friends - Only your friends</option>
                            <option value="private" {{ $privacySettings['profile_visibility'] === 'private' ? 'selected' : '' }}>Private - Only you</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose who can view your basic profile information</p>
                    </div>

                    <!-- Contact Info Visibility -->
                    <div>
                        <label for="contact_info_visibility" class="block text-sm font-medium text-gray-700 mb-2">Contact Information</label>
                        <select name="contact_info_visibility" id="contact_info_visibility" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="public" {{ $privacySettings['contact_info_visibility'] === 'public' ? 'selected' : '' }}>Public - Everyone can see</option>
                            <option value="classmates" {{ $privacySettings['contact_info_visibility'] === 'classmates' ? 'selected' : '' }}>Classmates - Only your classmates</option>
                            <option value="friends" {{ $privacySettings['contact_info_visibility'] === 'friends' ? 'selected' : '' }}>Friends - Only your friends</option>
                            <option value="private" {{ $privacySettings['contact_info_visibility'] === 'private' ? 'selected' : '' }}>Private - Only you</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose who can see your phone number and email</p>
                    </div>

                    <!-- Academic Info Visibility -->
                    <div>
                        <label for="academic_info_visibility" class="block text-sm font-medium text-gray-700 mb-2">Academic Information</label>
                        <select name="academic_info_visibility" id="academic_info_visibility" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="public" {{ $privacySettings['academic_info_visibility'] === 'public' ? 'selected' : '' }}>Public - Everyone can see</option>
                            <option value="classmates" {{ $privacySettings['academic_info_visibility'] === 'classmates' ? 'selected' : '' }}>Classmates - Only your classmates</option>
                            <option value="friends" {{ $privacySettings['academic_info_visibility'] === 'friends' ? 'selected' : '' }}>Friends - Only your friends</option>
                            <option value="private" {{ $privacySettings['academic_info_visibility'] === 'private' ? 'selected' : '' }}>Private - Only you</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose who can see your grades and academic performance</p>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Communication Settings</h3>
                    <p class="mt-1 text-sm text-gray-500">Control how others can communicate with you</p>
                </div>

                <div class="px-6 py-4 space-y-6">
                    <!-- Allow Messages -->
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">Allow Messages</h4>
                            <p class="text-sm text-gray-500">Let other students send you messages</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_messages" value="1" 
                                   {{ $privacySettings['allow_messages'] ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Show Online Status -->
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">Show Online Status</h4>
                            <p class="text-sm text-gray-500">Let others see when you're online</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="show_online_status" value="1" 
                                   {{ $privacySettings['show_online_status'] ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
