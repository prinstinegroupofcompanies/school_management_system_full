@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $class->name }}</h1>
                <p class="text-gray-600 mt-2">Class Code: {{ $class->code }}</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('classes.edit', $class) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Class
                </a>
                <a href="{{ route('admin.classes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Classes
                </a>
            </div>
        </div>

        <!-- Class Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Class Name:</span>
                        <p class="text-gray-900">{{ $class->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Class Code:</span>
                        <p class="text-gray-900">{{ $class->code }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Capacity:</span>
                        <p class="text-gray-900">{{ $class->capacity }} students</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($class->status === 'active') bg-green-100 text-green-800
                            @elseif($class->status === 'inactive') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($class->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Class Teacher -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Class Teacher</h3>
                @if($class->classTeacher)
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <div class="h-12 w-12 rounded-full bg-green-500 flex items-center justify-center">
                                <span class="text-sm font-medium text-white">
                                    {{ substr($class->classTeacher->name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $class->classTeacher->name }}</div>
                            <div class="text-sm text-gray-500">{{ $class->classTeacher->email }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="text-gray-400 text-4xl mb-2">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <p class="text-gray-500">No class teacher assigned</p>
                    </div>
                @endif
            </div>

            <!-- Location Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Location</h3>
                @if($class->room_number || $class->building)
                    <div class="space-y-2">
                        @if($class->room_number)
                            <div>
                                <span class="text-sm font-medium text-gray-500">Room:</span>
                                <p class="text-gray-900">{{ $class->room_number }}</p>
                            </div>
                        @endif
                        @if($class->building)
                            <div>
                                <span class="text-sm font-medium text-gray-500">Building:</span>
                                <p class="text-gray-900">{{ $class->building }}</p>
                            </div>
                        @endif
                        @if($class->floor)
                            <div>
                                <span class="text-sm font-medium text-gray-500">Floor:</span>
                                <p class="text-gray-900">{{ $class->floor }}</p>
                            </div>
                        @endif
                        @if($class->wing)
                            <div>
                                <span class="text-sm font-medium text-gray-500">Wing:</span>
                                <p class="text-gray-900">{{ $class->wing }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="text-gray-400 text-4xl mb-2">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <p class="text-gray-500">Location not specified</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Students and Subjects -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Students -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Students ({{ $class->students->count() }})</h3>
                    <a href="{{ route('students.create') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-plus mr-1"></i>Add Student
                    </a>
                </div>
                @if($class->students->count() > 0)
                    <div class="space-y-3">
                        @foreach($class->students->take(5) as $student)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                            <span class="text-xs font-medium text-white">
                                                {{ substr($student->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $student->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $student->user->email }}</div>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $student->student_id }}</span>
                            </div>
                        @endforeach
                        @if($class->students->count() > 5)
                            <div class="text-center">
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                                    View all {{ $class->students->count() }} students
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-2">
                            <i class="fas fa-users"></i>
                        </div>
                        <p class="text-gray-500">No students enrolled</p>
                    </div>
                @endif
            </div>

            <!-- Subjects -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Subjects ({{ $class->subjects->count() }})</h3>
                    <a href="{{ route('subjects.create') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-plus mr-1"></i>Add Subject
                    </a>
                </div>
                @if($class->subjects->count() > 0)
                    <div class="space-y-3">
                        @foreach($class->subjects->take(5) as $subject)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $subject->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $subject->code }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">{{ $subject->credits }} credits</div>
                                    <div class="text-xs text-gray-500">{{ $subject->hours_per_week }}h/week</div>
                                </div>
                            </div>
                        @endforeach
                        @if($class->subjects->count() > 5)
                            <div class="text-center">
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                                    View all {{ $class->subjects->count() }} subjects
                                </a>
                            </div>
                        @endif
                    </div>
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

        <!-- Description -->
        @if($class->description)
            <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
                <p class="text-gray-700">{{ $class->description }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
