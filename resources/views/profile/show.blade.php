@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
        <div class="flex space-x-3">
            <a href="{{ route('profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Profile
            </a>
            <a href="{{ route('profile.change-password') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z" />
                </svg>
                Change Password
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="text-center">
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" 
                             alt="{{ $user->name }}" 
                             class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-gray-200">
                    @else
                        <div class="w-32 h-32 rounded-full mx-auto mb-4 bg-gray-200 flex items-center justify-center border-4 border-gray-300">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    @endif
                    
                    <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $user->name }}</h2>
                    <p class="text-gray-600 mb-2">{{ $user->email }}</p>
                    
                    <!-- Role Badge -->
                    <div class="mb-4">
                        @if($user->user_type === 'admin')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Administrator
                            </span>
                        @elseif($user->user_type === 'teacher')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                                Teacher
                            </span>
                        @elseif($user->user_type === 'student')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                Student
                            </span>
                        @elseif($user->user_type === 'finance')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                Finance Officer
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($user->user_type) }}
                            </span>
                        @endif
                    </div>

                    <!-- Status Badge -->
                    <div class="mb-4">
                        @if($user->status === 'active')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Active
                            </span>
                        @elseif($user->status === 'inactive')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                </svg>
                                Inactive
                            </span>
                        @elseif($user->status === 'suspended')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                </svg>
                                Suspended
                            </span>
                        @endif
                    </div>

                    <!-- Member Since -->
                    <p class="text-sm text-gray-500">
                        Member since {{ $user->created_at->format('M Y') }}
                    </p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Last Login</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $user->last_login_at ? $user->last_login_at->format('M d, Y') : 'Never' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Email Verified</span>
                        <span class="text-sm font-medium {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->email_verified_at ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    @if($user->phone)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Phone</span>
                        <span class="text-sm font-medium text-gray-900">{{ $user->phone }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="lg:col-span-2">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Full Name</label>
                        <p class="text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>
                    @if($user->phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Phone Number</label>
                        <p class="text-gray-900">{{ $user->phone }}</p>
                    </div>
                    @endif
                    @if($user->date_of_birth)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Date of Birth</label>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($user->date_of_birth)->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($user->gender)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Gender</label>
                        <p class="text-gray-900">{{ ucfirst($user->gender) }}</p>
                    </div>
                    @endif
                    @if($user->address)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                        <p class="text-gray-900">{{ $user->address }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Role-Specific Information -->
            @if($user->student)
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($user->student->student_id)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Student ID</label>
                        <p class="text-gray-900">{{ $user->student->student_id }}</p>
                    </div>
                    @endif
                    @if($user->student->class)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Class</label>
                        <p class="text-gray-900">{{ $user->student->class->name }}</p>
                    </div>
                    @endif
                    @if($user->student->section)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Section</label>
                        <p class="text-gray-900">{{ $user->student->section->name }}</p>
                    </div>
                    @endif
                    @if($user->student->academic_year)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Academic Year</label>
                        <p class="text-gray-900">{{ $user->student->academic_year }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @elseif($user->teacher)
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Teacher Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($user->teacher->employee_id)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Employee ID</label>
                        <p class="text-gray-900">{{ $user->teacher->employee_id }}</p>
                    </div>
                    @endif
                    @if($user->teacher->department)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Department</label>
                        <p class="text-gray-900">{{ $user->teacher->department }}</p>
                    </div>
                    @endif
                    @if($user->teacher->qualification)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Qualification</label>
                        <p class="text-gray-900">{{ $user->teacher->qualification }}</p>
                    </div>
                    @endif
                    @if($user->teacher->experience_years)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Experience</label>
                        <p class="text-gray-900">{{ $user->teacher->experience_years }} years</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Account Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">User Type</label>
                        <p class="text-gray-900">{{ ucfirst($user->user_type) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <p class="text-gray-900">{{ ucfirst($user->status) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                        <p class="text-gray-900">{{ $user->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-gray-900">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
