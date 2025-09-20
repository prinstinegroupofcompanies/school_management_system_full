@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $subject->name }}</h1>
                <p class="text-gray-600 mt-2">Subject Code: {{ $subject->code }}</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.subjects.edit', $subject) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Subject
                </a>
                <a href="{{ route('admin.subjects.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Subjects
                </a>
            </div>
        </div>

        <!-- Subject Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Subject Name:</span>
                        <p class="text-gray-900">{{ $subject->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Subject Code:</span>
                        <p class="text-gray-900">{{ $subject->code }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Credits:</span>
                        <p class="text-gray-900">{{ $subject->credits }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Hours Per Week:</span>
                        <p class="text-gray-900">{{ $subject->hours_per_week }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($subject->status === 'active') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($subject->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Classes:</span>
                        <div class="mt-1">
                            @if($subject->classes->count() > 0)
                                @foreach($subject->classes as $class)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1 mb-1">
                                        {{ $class->name }} ({{ $class->session ?? 'N/A' }})
                                    </span>
                                @endforeach
                            @else
                                <span class="text-gray-500">Not Assigned</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Teacher:</span>
                        <p class="text-gray-900">{{ $subject->teacher ? $subject->teacher->user->name : 'Not Assigned' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Teacher Email:</span>
                        <p class="text-gray-900">{{ $subject->teacher ? $subject->teacher->user->email : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Subject Type -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Subject Type</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Compulsory:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($subject->is_compulsory) bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $subject->is_compulsory ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Elective:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($subject->is_elective) bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $subject->is_elective ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Created:</span>
                        <p class="text-gray-900">{{ $subject->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Last Updated:</span>
                        <p class="text-gray-900">{{ $subject->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($subject->description)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
            <p class="text-gray-700 leading-relaxed">{{ $subject->description }}</p>
        </div>
        @endif

        <!-- Additional Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <span class="text-sm font-medium text-gray-500">Total Students:</span>
                    <p class="text-gray-900">
                        @if($subject->classes->count() > 0)
                            {{ $subject->classes->sum(function($class) { return $class->students()->count(); }) }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Subject Type:</span>
                    <div class="flex space-x-2 mt-1">
                        @if($subject->is_compulsory)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Compulsory
                            </span>
                        @endif
                        @if($subject->is_elective)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Elective
                            </span>
                        @endif
                        @if(!$subject->is_compulsory && !$subject->is_elective)
                            <span class="text-gray-500">Not specified</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
