@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $teacher->user->name }}</h1>
                <p class="text-gray-600 mt-2">Employee ID: {{ $teacher->employee_id ?? 'N/A' }}</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('teachers.edit', $teacher) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Teacher
                </a>
                <a href="{{ route('teachers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Teachers
                </a>
            </div>
        </div>

        <!-- Teacher Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Full Name:</span>
                        <p class="text-gray-900">{{ $teacher->user->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Email:</span>
                        <p class="text-gray-900">{{ $teacher->user->email }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Phone:</span>
                        <p class="text-gray-900">{{ $teacher->user->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Employee ID:</span>
                        <p class="text-gray-900">{{ $teacher->employee_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($teacher->status === 'active') bg-green-100 text-green-800
                            @elseif($teacher->status === 'inactive') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($teacher->status ?? 'active') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Professional Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Department:</span>
                        <p class="text-gray-900">{{ $teacher->department ? $teacher->department->name : 'Not Assigned' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Designation:</span>
                        <p class="text-gray-900">{{ $teacher->designation ? $teacher->designation->name : 'Not Assigned' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Qualification:</span>
                        <p class="text-gray-900">{{ $teacher->qualification ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Experience:</span>
                        <p class="text-gray-900">{{ $teacher->experience ?? 0 }} years</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Employment Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($teacher->employment_status === 'active') bg-green-100 text-green-800
                            @elseif($teacher->employment_status === 'inactive') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($teacher->employment_status ?? 'active') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Employment Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Employment Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Joining Date:</span>
                        <p class="text-gray-900">{{ $teacher->joining_date ? $teacher->joining_date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Basic Salary:</span>
                        <p class="text-gray-900">${{ number_format($teacher->basic_salary ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Salary:</span>
                        <p class="text-gray-900">${{ number_format($teacher->salary ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Created:</span>
                        <p class="text-gray-900">{{ $teacher->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Last Updated:</span>
                        <p class="text-gray-900">{{ $teacher->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        @if($teacher->user->address)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Address Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <span class="text-sm font-medium text-gray-500">Address:</span>
                    <p class="text-gray-900">{{ $teacher->user->address }}</p>
                </div>
                @if($teacher->user->city)
                <div>
                    <span class="text-sm font-medium text-gray-500">City:</span>
                    <p class="text-gray-900">{{ $teacher->user->city }}</p>
                </div>
                @endif
                @if($teacher->user->state)
                <div>
                    <span class="text-sm font-medium text-gray-500">State:</span>
                    <p class="text-gray-900">{{ $teacher->user->state }}</p>
                </div>
                @endif
                @if($teacher->user->country)
                <div>
                    <span class="text-sm font-medium text-gray-500">Country:</span>
                    <p class="text-gray-900">{{ $teacher->user->country }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Teaching Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Assigned Classes -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Classes</h3>
                <div class="space-y-3">
                    @if($teacher->classes && $teacher->classes->count() > 0)
                        @foreach($teacher->classes as $class)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $class->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $class->code }}</div>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Class Teacher
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-4xl mb-2">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <p class="text-gray-500">No classes assigned</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assigned Subjects -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Subjects</h3>
                <div class="space-y-3">
                    @if($teacher->subjects && $teacher->subjects->count() > 0)
                        @foreach($teacher->subjects as $subject)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $subject->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $subject->code }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">{{ $subject->credits }} credits</div>
                                    <div class="text-xs text-gray-500">{{ $subject->hours_per_week }} hrs/week</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-4xl mb-2">
                                <i class="fas fa-book"></i>
                            </div>
                            <p class="text-gray-500">No subjects assigned</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
