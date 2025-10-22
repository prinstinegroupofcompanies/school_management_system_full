@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
                <a href="{{ route('teacher.profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Information -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Personal Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Full Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">City</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->city ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Country</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->country ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->address ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Professional Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Employee ID</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $teacher ? $teacher->employee_id ?? 'Not assigned' : 'Not available' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Department</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $teacher && $teacher->department ? $teacher->department->name : 'Not assigned' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Designation</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $teacher && $teacher->designation ? $teacher->designation->name : 'Not assigned' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Experience</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $teacher ? ($teacher->experience_years ?? 0) : 0 }} years</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Joining Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $teacher && $teacher->joining_date ? \Carbon\Carbon::parse($teacher->joining_date)->format('M d, Y') : 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $teacher && $teacher->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $teacher && $teacher->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Profile Photo -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6 text-center">
                        <div class="mx-auto w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                            @if($user->profile_photo)
                                <img class="w-24 h-24 rounded-full object-cover" src="{{ Storage::url($user->profile_photo) }}" alt="Profile Photo">
                            @else
                                <span class="text-2xl font-bold text-indigo-600">{{ substr($user->name, 0, 2) }}</span>
                            @endif
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">Teacher</p>
                        <p class="text-xs text-gray-400 mt-2">Member since {{ $user->created_at->format('M Y') }}</p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Statistics</h3>
                        
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

                <!-- Recent Activities -->
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Activities</h3>
                        
                        <div class="space-y-3">
                            @foreach($recentActivities as $activity)
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-2 h-2 bg-indigo-500 rounded-full mt-2"></div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-900">{{ $activity['description'] }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection