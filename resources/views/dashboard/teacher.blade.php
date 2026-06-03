@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Teacher Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">Welcome, {{ $user->name ?? $currentUser->name ?? 'Teacher' }}</span>
                    @if(isset($session))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        Session: {{ $session['academic_year'] }} • Sem {{ $session['semester'] }}
                    </span>
                    @endif
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        Teacher
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Teacher Info -->
        @if($user)
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @php($photo = $user->profile_photo ?? null)
                        @if($photo)
                            <img class="w-16 h-16 rounded-full object-cover" src="{{ Storage::url($photo) }}" alt="Profile Photo">
                        @else
                            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">Teacher ID: {{ $user->id }}</p>
                        <p class="text-sm text-gray-500">Email: {{ $user->email }}</p>
                        <p class="text-sm text-gray-500">Status: Active</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Classes -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">My Classes</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $classes->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Students -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">My Students</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $students->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Subjects -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 19 16.5 19c-1.746 0-3.332-.477-4.5-1.253"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">My Subjects</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $subjects->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lesson Plans Statistics -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Lesson Plans</h3>
                    <a href="{{ route('teacher.lesson-plans.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $lessonPlanStats['total'] }}</div>
                        <div class="text-xs text-gray-500">Total</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-600">{{ $lessonPlanStats['draft'] }}</div>
                        <div class="text-xs text-gray-500">Draft</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ $lessonPlanStats['submitted'] }}</div>
                        <div class="text-xs text-gray-500">Submitted</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $lessonPlanStats['approved'] }}</div>
                        <div class="text-xs text-gray-500">Approved</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $lessonPlanStats['rejected'] }}</div>
                        <div class="text-xs text-gray-500">Rejected</div>
                    </div>
                </div>

                <!-- Recent Lesson Plans -->
                @if($recentLessonPlans->count() > 0)
                <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Recent Lesson Plans</h4>
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                        @foreach($recentLessonPlans as $lessonPlan)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full mr-2 
                                    @if($lessonPlan->status === 'draft') bg-gray-500
                                    @elseif($lessonPlan->status === 'submitted') bg-blue-500
                                    @elseif($lessonPlan->status === 'first_level_approved') bg-yellow-500
                                    @elseif($lessonPlan->status === 'second_level_approved') bg-green-500
                                    @elseif($lessonPlan->status === 'rejected') bg-red-500
                                    @else bg-gray-500 @endif"></span>
                                <span class="text-gray-900">{{ $lessonPlan->title }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-500">{{ $lessonPlan->subject->name ?? 'N/A' }}</span>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($lessonPlan->status === 'draft') bg-gray-100 text-gray-800
                                    @elseif($lessonPlan->status === 'submitted') bg-blue-100 text-blue-800
                                    @elseif($lessonPlan->status === 'first_level_approved') bg-yellow-100 text-yellow-800
                                    @elseif($lessonPlan->status === 'second_level_approved') bg-green-100 text-green-800
                                    @elseif($lessonPlan->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $lessonPlan->status)) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-gray-500 text-sm">No lesson plans created yet.</p>
                    <a href="{{ route('teacher.lesson-plans.create') }}" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                        Create Your First Lesson Plan
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- My Classes -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">My Classes</h3>
                @if($classes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($classes as $class)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-lg font-medium text-gray-900">{{ $class->name }}</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-2">{{ $class->code }}</p>
                        <p class="text-sm text-gray-600 mb-3">{{ $class->students->count() }} students</p>
                        <div class="flex space-x-2">
                            <a href="{{ route('teacher.classes.show', $class->id) }}" class="text-sm text-blue-600 hover:text-blue-800">View Details</a>
                            <a href="{{ route('attendance.student', ['class_id' => $class->id, 'date' => now()->format('Y-m-d')]) }}" class="text-sm text-green-600 hover:text-green-800">Take Attendance</a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-8">No classes assigned yet.</p>
                @endif
            </div>
        </div>

        <!-- Upcoming Exams -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Upcoming Exams</h3>
                @if($upcomingExams->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingExams as $exam)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">{{ $exam->examType->name ?? 'Exam' }}</h4>
                            <p class="text-sm text-gray-500">{{ $exam->subject->name ?? 'Subject' }} - {{ $exam->class->name ?? 'Class' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') : 'TBD' }}</p>
                            <p class="text-xs text-gray-500">{{ $exam->start_time ?? 'Time TBD' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-8">No upcoming exams.</p>
                @endif
            </div>
        </div>

        <!-- Grade Management -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Grade Management</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('teacher.grades.index') }}" class="flex items-center justify-between p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">View Grades</h4>
                                <p class="text-xs text-gray-500">Manage student grades</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('teacher.grades.create') }}" class="flex items-center justify-between p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Add Grade</h4>
                                <p class="text-xs text-gray-500">Record new grades</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('teacher.grades.bulk-create') }}" class="flex items-center justify-between p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Bulk Entry</h4>
                                <p class="text-xs text-gray-500">Enter multiple grades</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('attendance.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Take Attendance
                    </a>
                    <a href="{{ route('teacher.grades.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Record Marks
                    </a>
                    <a href="{{ route('homework.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 19 16.5 19c-1.746 0-3.332-.477-4.5-1.253"></path>
                        </svg>
                        Assign Homework
                    </a>
                    <a href="{{ route('study-materials.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Upload Material
                    </a>
                    <a href="{{ route('attendance.history.students') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z"></path>
                        </svg>
                        My Students' Attendance
                    </a>
                    <a href="{{ route('attendance.history.teacher') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        My Attendance
                    </a>
                </div>
            </div>
        </div>

        <!-- Attendance Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- My Attendance -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">📅 My Attendance</h3>
                        <a href="{{ route('attendance.teacher', ['date' => now()->format('Y-m-d')]) }}" class="text-sm text-blue-600 hover:text-blue-800">Record Today</a>
                    </div>
                    
                    <!-- Teacher Attendance Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-xl font-bold text-green-600">{{ $teacherAttendanceStats['present_days'] }}</div>
                            <div class="text-xs text-gray-500">Present</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-red-600">{{ $teacherAttendanceStats['absent_days'] }}</div>
                            <div class="text-xs text-gray-500">Absent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-yellow-600">{{ $teacherAttendanceStats['late_days'] }}</div>
                            <div class="text-xs text-gray-500">Late</div>
                        </div>
                    </div>
                    
                    <!-- Recent Teacher Attendance -->
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Recent Records</h4>
                        <div class="space-y-2 max-h-32 overflow-y-auto">
                            @forelse($recentTeacherAttendance as $attendance)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center">
                                    <span class="w-2 h-2 rounded-full mr-2 
                                        @if($attendance->status === 'present') bg-green-500
                                        @elseif($attendance->status === 'absent') bg-red-500
                                        @elseif($attendance->status === 'late') bg-yellow-500
                                        @else bg-blue-500 @endif"></span>
                                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</span>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($attendance->status === 'present') bg-green-100 text-green-800
                                    @elseif($attendance->status === 'absent') bg-red-100 text-red-800
                                    @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </div>
                            @empty
                            <p class="text-gray-500 text-sm text-center py-2">No attendance records yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Attendance -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">👥 Student Attendance Today</h3>
                        <a href="{{ route('attendance.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Take Attendance</a>
                    </div>
                    
                    <!-- Student Attendance Stats -->
                    <div class="grid grid-cols-4 gap-3 mb-4">
                        <div class="text-center">
                            <div class="text-lg font-bold text-green-600">{{ $studentAttendanceStats['present_today'] }}</div>
                            <div class="text-xs text-gray-500">Present</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-red-600">{{ $studentAttendanceStats['absent_today'] }}</div>
                            <div class="text-xs text-gray-500">Absent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-yellow-600">{{ $studentAttendanceStats['late_today'] }}</div>
                            <div class="text-xs text-gray-500">Late</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-600">{{ $studentAttendanceStats['total_today'] }}</div>
                            <div class="text-xs text-gray-500">Total</div>
                        </div>
                    </div>
                    
                    <!-- Recent Student Attendance Records -->
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Recently Recorded</h4>
                        <div class="space-y-2 max-h-32 overflow-y-auto">
                            @forelse($recentStudentAttendance as $attendance)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center">
                                    <span class="w-2 h-2 rounded-full mr-2 
                                        @if($attendance->status === 'present') bg-green-500
                                        @elseif($attendance->status === 'absent') bg-red-500
                                        @elseif($attendance->status === 'late') bg-yellow-500
                                        @else bg-blue-500 @endif"></span>
                                    <span class="text-gray-900">{{ $attendance->student->user->name }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-gray-500">{{ $attendance->student->classRoom->name ?? 'N/A' }}</span>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($attendance->status === 'present') bg-green-100 text-green-800
                                        @elseif($attendance->status === 'absent') bg-red-100 text-red-800
                                        @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-sm text-center py-2">No student attendance recorded yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Activities</h3>
                <div class="flow-root">
                    <ul class="-mb-8">
                        @foreach($recentActivities as $activity)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">{{ $activity['description'] }}</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            @if(!empty($activity['created_at']))
                                                <time>{{ \Illuminate\Support\Carbon::parse($activity['created_at'])->diffForHumans() }}</time>
                                            @else
                                                <time>-</time>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
