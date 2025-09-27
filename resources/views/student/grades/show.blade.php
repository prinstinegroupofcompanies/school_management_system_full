@extends('layouts.app')

@section('title', 'Grade Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Grade Details</h1>
                    <p class="mt-2 text-gray-600">Detailed view of your grade information</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        Student
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Grade Information
                    </h3>
                    <a href="{{ route('student.grades.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Grades
                    </a>
                </div>

                    @php
                        $grade = \App\Models\Grade::with(['subject', 'class', 'teacher.user'])->find($id);
                    @endphp

                @if($grade)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Subject Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-medium text-blue-600 mb-4">Subject Information</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Subject:</span>
                                    <span class="text-sm text-gray-900">{{ $grade->subject->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Class:</span>
                                    <span class="text-sm text-gray-900">{{ $grade->class->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Teacher:</span>
                                    <span class="text-sm text-gray-900">{{ $grade->teacher->user->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Academic Year:</span>
                                    <span class="text-sm text-gray-900">{{ $grade->academic_year }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Semester:</span>
                                    <span class="text-sm text-gray-900">{{ $grade->semester }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Grade Details -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-medium text-green-600 mb-4">Grade Details</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Year Average:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $grade->year_avg >= 80 ? 'bg-green-100 text-green-800' : ($grade->year_avg >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($grade->year_avg, 2) }}%
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Grade Letter:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $grade->year_avg >= 80 ? 'bg-green-100 text-green-800' : ($grade->year_avg >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        @if($grade->year_avg >= 90) A
                                        @elseif($grade->year_avg >= 80) B
                                        @elseif($grade->year_avg >= 70) C
                                        @elseif($grade->year_avg >= 60) D
                                        @else F
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Status:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $grade->status === 'approved' ? 'bg-green-100 text-green-800' : ($grade->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($grade->status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Created:</span>
                                    <span class="text-sm text-gray-900">{{ $grade->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Updated:</span>
                                    <span class="text-sm text-gray-900">{{ $grade->updated_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grade Breakdown -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h4 class="text-lg font-medium text-indigo-600 mb-4">Grade Breakdown</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $grade->first_test ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">First Test</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $grade->second_test ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">Second Test</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $grade->third_test ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">Third Test</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600 mb-2">{{ $grade->year_avg ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">Year Average</div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Analysis -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="text-lg font-medium text-yellow-600 mb-4">Performance Analysis</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold {{ $grade->year_avg >= 80 ? 'text-green-600' : ($grade->year_avg >= 60 ? 'text-yellow-600' : 'text-red-600') }} mb-2">
                                    {{ $grade->year_avg >= 80 ? 'Excellent' : ($grade->year_avg >= 60 ? 'Good' : 'Needs Improvement') }}
                                </div>
                                <div class="text-sm text-gray-500">Performance Level</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold {{ $grade->year_avg >= 50 ? 'text-green-600' : 'text-red-600' }} mb-2">
                                    {{ $grade->year_avg >= 50 ? 'Passed' : 'Failed' }}
                                </div>
                                <div class="text-sm text-gray-500">Result</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600 mb-2">
                                    {{ $grade->year_avg >= 90 ? 'A+' : ($grade->year_avg >= 80 ? 'A' : ($grade->year_avg >= 70 ? 'B' : ($grade->year_avg >= 60 ? 'C' : 'F'))) }}
                                </div>
                                <div class="text-sm text-gray-500">Grade Point</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="mb-4">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Grade Not Found</h3>
                        <p class="text-gray-500 mb-6">The requested grade record could not be found.</p>
                        <a href="{{ route('student.grades.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Grades
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
