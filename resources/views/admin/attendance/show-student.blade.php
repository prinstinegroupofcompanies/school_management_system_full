@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Student Attendance Record</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.attendance.students') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Students
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Student Information -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 h-16 w-16">
                        <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center">
                            <span class="text-xl font-medium text-white">{{ substr($studentAttendance->student->user->name ?? 'ST', 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $studentAttendance->student->user->name ?? 'N/A' }}</h3>
                        <p class="mt-1 text-sm text-gray-500">Student ID: {{ $studentAttendance->student->student_id ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">Class: {{ $studentAttendance->class->name ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Attendance Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($studentAttendance->attendance_date)->format('l, F j, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($studentAttendance->status === 'present') bg-green-100 text-green-800
                            @elseif($studentAttendance->status === 'absent') bg-red-100 text-red-800
                            @elseif($studentAttendance->status === 'late') bg-yellow-100 text-yellow-800
                            @elseif($studentAttendance->status === 'excused') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($studentAttendance->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Marked By</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $studentAttendance->markedBy->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Marked At</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $studentAttendance->marked_at ? \Carbon\Carbon::parse($studentAttendance->marked_at)->format('M d, Y h:i A') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Section</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $studentAttendance->section->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Academic Year</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $studentAttendance->academic_year ?? 'N/A' }}</p>
                    </div>
                </div>
                
                @if($studentAttendance->remarks)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Remarks</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $studentAttendance->remarks }}</p>
                </div>
                @endif
                
                @if($studentAttendance->teacher_remarks)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Teacher Remarks</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $studentAttendance->teacher_remarks }}</p>
                </div>
                @endif
                
                @if($studentAttendance->parent_remarks)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Parent Remarks</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $studentAttendance->parent_remarks }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
