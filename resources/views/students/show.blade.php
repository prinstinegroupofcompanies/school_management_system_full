@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $student->user->name }}</h1>
                <p class="text-gray-600 mt-2">Student ID: {{ $student->student_id ?? 'N/A' }}</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('students.edit', $student) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Student
                </a>
                <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Students
                </a>
            </div>
        </div>

        <!-- Student Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Full Name:</span>
                        <p class="text-gray-900">{{ $student->user->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Email:</span>
                        <p class="text-gray-900">{{ $student->user->email }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Student ID:</span>
                        <p class="text-gray-900">{{ $student->student_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Admission Number:</span>
                        <p class="text-gray-900">{{ $student->admission_no ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($student->status === 'active') bg-green-100 text-green-800
                            @elseif($student->status === 'inactive') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($student->status ?? 'active') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Class:</span>
                        <p class="text-gray-900">{{ $student->class ? $student->class->name : 'Not Assigned' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Class Code:</span>
                        <p class="text-gray-900">{{ $student->class ? $student->class->code : 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Section:</span>
                        <p class="text-gray-900">{{ $student->section ? $student->section->name : 'Not Assigned' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Admission Date:</span>
                        <p class="text-gray-900">{{ $student->admission_date ? $student->admission_date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Date of Birth:</span>
                        <p class="text-gray-900">{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Gender:</span>
                        <p class="text-gray-900">{{ ucfirst($student->gender ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Phone:</span>
                        <p class="text-gray-900">{{ $student->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Address:</span>
                        <p class="text-gray-900">{{ $student->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guardian Information -->
        @if($student->guardian_name || $student->guardian_phone || $student->guardian_email)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Guardian Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <span class="text-sm font-medium text-gray-500">Guardian Name:</span>
                    <p class="text-gray-900">{{ $student->guardian_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Guardian Phone:</span>
                    <p class="text-gray-900">{{ $student->guardian_phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Guardian Email:</span>
                    <p class="text-gray-900">{{ $student->guardian_email ?? 'N/A' }}</p>
                </div>
            </div>
            @if($student->guardian_address)
            <div class="mt-4">
                <span class="text-sm font-medium text-gray-500">Guardian Address:</span>
                <p class="text-gray-900">{{ $student->guardian_address }}</p>
            </div>
            @endif
        </div>
        @endif

        <!-- Academic Performance -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Attendance -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Attendance</h3>
                <div class="space-y-3">
                    @if($student->attendances && $student->attendances->count() > 0)
                        @foreach($student->attendances->take(5) as $attendance)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $attendance->date ? $attendance->date->format('M d, Y') : 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $attendance->subject ?? 'General' }}</div>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($attendance->status === 'present') bg-green-100 text-green-800
                                    @elseif($attendance->status === 'absent') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-4xl mb-2">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <p class="text-gray-500">No attendance records found</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Exam Marks -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Exam Marks</h3>
                <div class="space-y-3">
                    @if($student->examMarks && $student->examMarks->count() > 0)
                        @foreach($student->examMarks->take(5) as $mark)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $mark->subject ?? 'Subject' }}</div>
                                    <div class="text-xs text-gray-500">{{ $mark->exam_type ?? 'Exam' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">{{ $mark->marks_obtained ?? 0 }}/{{ $mark->total_marks ?? 100 }}</div>
                                    <div class="text-xs text-gray-500">{{ round(($mark->marks_obtained ?? 0) / ($mark->total_marks ?? 100) * 100) }}%</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-4xl mb-2">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <p class="text-gray-500">No exam marks found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
