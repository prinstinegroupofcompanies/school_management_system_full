@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Grade Details</h1>
                    <p class="mt-2 text-gray-600">View detailed information about this grade entry</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('teacher.grades.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Grades
                    </a>
                    @if($grade->status === 'pending' || $grade->status === 'rejected')
                        <a href="{{ route('teacher.grades.edit', $grade) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Grade
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Grade Status Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 rounded-full mr-4
                        @if($grade->status === 'approved') bg-green-100
                        @elseif($grade->status === 'pending') bg-yellow-100
                        @elseif($grade->status === 'rejected') bg-red-100
                        @else bg-gray-100 @endif">
                        <i class="fas 
                            @if($grade->status === 'approved') fa-check-circle text-green-600
                            @elseif($grade->status === 'pending') fa-clock text-yellow-600
                            @elseif($grade->status === 'rejected') fa-times-circle text-red-600
                            @else fa-question-circle text-gray-600 @endif text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Grade Status</h2>
                        <p class="text-sm text-gray-500">Current status of this grade entry</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($grade->status === 'approved') bg-green-100 text-green-800
                        @elseif($grade->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($grade->status === 'rejected') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($grade->status) }}
                    </span>
                    @if($grade->approved_at)
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $grade->approved_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12">
                        <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Student Name</div>
                        <div class="text-lg font-semibold text-gray-900">{{ $grade->student->user->name }}</div>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Admission Number</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $grade->student->admission_number }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Class</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $grade->class->name }}</div>
                </div>
            </div>
        </div>

        <!-- Subject Information -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Subject Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-sm font-medium text-gray-500">Subject</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $grade->subject->name }}</div>
                    @if($grade->subject->code)
                        <div class="text-sm text-gray-500">Code: {{ $grade->subject->code }}</div>
                    @endif
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Academic Year</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $grade->academic_year }}</div>
                    <div class="text-sm text-gray-500">Semester: {{ $grade->semester }}</div>
                </div>
            </div>
        </div>

        <!-- Grade Details -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Grade Details</h3>
            
            <!-- Semester 1 Grades -->
            <div class="mb-8">
                <h4 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Semester 1 Grades
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-gray-500">Period 1</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $grade->sem1_p1 ? number_format($grade->sem1_p1, 2) : 'N/A' }}
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-gray-500">Period 2</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $grade->sem1_p2 ? number_format($grade->sem1_p2, 2) : 'N/A' }}
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-gray-500">Period 3</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $grade->sem1_p3 ? number_format($grade->sem1_p3, 2) : 'N/A' }}
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-gray-500">Semester 1 Exam</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $grade->sem1_exam ? number_format($grade->sem1_exam, 2) : 'N/A' }}
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-blue-700">Semester 1 Average</div>
                        <div class="text-3xl font-bold text-blue-900">
                            {{ $grade->sem1_avg ? number_format($grade->sem1_avg, 2) . '%' : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Semester 2 Grades -->
            <div class="mb-8">
                <h4 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                    Semester 2 Grades
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-gray-500">Period 4</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $grade->sem2_p4 ? number_format($grade->sem2_p4, 2) : 'N/A' }}
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-gray-500">Period 5</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $grade->sem2_p5 ? number_format($grade->sem2_p5, 2) : 'N/A' }}
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-gray-500">Period 6</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $grade->sem2_p6 ? number_format($grade->sem2_p6, 2) : 'N/A' }}
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-gray-500">Semester 2 Exam</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $grade->sem2_exam ? number_format($grade->sem2_exam, 2) : 'N/A' }}
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-green-700">Semester 2 Average</div>
                        <div class="text-3xl font-bold text-green-900">
                            {{ $grade->sem2_avg ? number_format($grade->sem2_avg, 2) . '%' : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Year Average -->
            <div class="bg-purple-50 rounded-lg p-6">
                <h4 class="text-md font-medium text-purple-700 mb-2 flex items-center">
                    <i class="fas fa-trophy text-purple-600 mr-2"></i>
                    Year Average
                </h4>
                <div class="text-4xl font-bold text-purple-900">
                    {{ $grade->year_avg ? number_format($grade->year_avg, 2) . '%' : 'N/A' }}
                </div>
                @if($grade->year_avg)
                    <div class="mt-2">
                        @php
                            $grade_letter = '';
                            $grade_color = '';
                            if($grade->year_avg >= 90) {
                                $grade_letter = 'A+';
                                $grade_color = 'text-green-600 bg-green-100';
                            } elseif($grade->year_avg >= 80) {
                                $grade_letter = 'A';
                                $grade_color = 'text-blue-600 bg-blue-100';
                            } elseif($grade->year_avg >= 70) {
                                $grade_letter = 'B+';
                                $grade_color = 'text-yellow-600 bg-yellow-100';
                            } elseif($grade->year_avg >= 60) {
                                $grade_letter = 'B';
                                $grade_color = 'text-orange-600 bg-orange-100';
                            } elseif($grade->year_avg >= 50) {
                                $grade_letter = 'C+';
                                $grade_color = 'text-red-600 bg-red-100';
                            } else {
                                $grade_letter = 'C';
                                $grade_color = 'text-red-800 bg-red-200';
                            }
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-lg font-semibold {{ $grade_color }}">
                            {{ $grade_letter }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Approval Information -->
        @if($grade->status === 'approved' && $grade->approvedBy)
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Approval Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="text-sm font-medium text-gray-500">Approved By</div>
                        <div class="text-lg font-semibold text-gray-900">{{ $grade->approvedBy->name }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Approved On</div>
                        <div class="text-lg font-semibold text-gray-900">
                            {{ $grade->approved_at->format('M d, Y \a\t g:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Timestamps -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Record Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-sm font-medium text-gray-500">Created</div>
                    <div class="text-lg font-semibold text-gray-900">
                        {{ $grade->created_at->format('M d, Y \a\t g:i A') }}
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Last Updated</div>
                    <div class="text-lg font-semibold text-gray-900">
                        {{ $grade->updated_at->format('M d, Y \a\t g:i A') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
