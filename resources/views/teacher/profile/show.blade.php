@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
                <a href="{{ route('teacher.profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center space-x-6">
                    <!-- Profile Photo -->
                    <div class="flex-shrink-0">
                        @if($teacher->user->profile_photo)
                            <img class="w-24 h-24 rounded-full object-cover" src="{{ Storage::url($teacher->user->profile_photo) }}" alt="Profile Photo">
                        @else
                            <div class="w-24 h-24 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">{{ substr($teacher->user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Basic Info -->
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $teacher->user->name }}</h2>
                        <p class="text-lg text-gray-600">{{ $teacher->user->email }}</p>
                        <p class="text-sm text-gray-500">Teacher ID: {{ $teacher->teacher_id }}</p>
                        <p class="text-sm text-gray-500">Employee ID: {{ $teacher->employee_id }}</p>
                    </div>
                </div>
                
                <!-- Personal Information -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="text-sm text-gray-900">{{ $teacher->user->phone ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Address</dt>
                                <dd class="text-sm text-gray-900">{{ $teacher->user->address ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">City</dt>
                                <dd class="text-sm text-gray-900">{{ $teacher->user->city ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">State</dt>
                                <dd class="text-sm text-gray-900">{{ $teacher->user->state ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Country</dt>
                                <dd class="text-sm text-gray-900">{{ $teacher->user->country ?? 'Not provided' }}</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <!-- Professional Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Professional Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Department</dt>
                                <dd class="text-sm text-gray-900">{{ $teacher->department->name ?? 'Not assigned' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Designation</dt>
                                <dd class="text-sm text-gray-900">{{ $teacher->designation->name ?? 'Not assigned' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Qualification</dt>
                                <dd class="text-sm text-gray-900">{{ $teacher->qualification ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Experience</dt>
                                <dd class="text-sm text-gray-900">{{ $teacher->experience ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Joining Date</dt>
                                <dd class="text-sm text-gray-900">{{ $teacher->joining_date ? $teacher->joining_date->format('M d, Y') : 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $teacher->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($teacher->status) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
