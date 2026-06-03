@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Student Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">Welcome, {{ $user->name ?? $currentUser->name ?? 'Student' }}</span>
                    @if(isset($session))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        Session: {{ $session['academic_year'] }} • Sem {{ $session['semester'] }}
                    </span>
                    @endif
                    @if(isset($class) && $class)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        Class: {{ $class->name }}
                    </span>
                    @endif
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        Student
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Student Info -->
        @if($user)
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">Student ID: {{ $user->id }}</p>
                        <p class="text-sm text-gray-500">Email: {{ $user->email }}</p>
                        @if(isset($class) && $class)
                        <p class="text-sm text-gray-500">Class: {{ $class->name }}</p>
                        @endif
                        <p class="text-sm text-gray-500">Status: Active</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Today's Attendance -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ $attendanceStats['today_status'] === 'present' ? 'bg-green-500' : ($attendanceStats['today_status'] === 'absent' ? 'bg-red-500' : ($attendanceStats['today_status'] === 'late' ? 'bg-yellow-500' : 'bg-gray-500')) }} rounded-md flex items-center justify-center">
                                @if($attendanceStats['today_status'] === 'present')
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                @elseif($attendanceStats['today_status'] === 'absent')
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                @elseif($attendanceStats['today_status'] === 'late')
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                @else
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                @endif
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Today's Status</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $attendanceStats['today_status'] !== 'not_recorded' ? ucfirst($attendanceStats['today_status']) : 'Not Marked' }}
                                </dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Subjects</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $subjects->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Status -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ $feeStatus && $feeStatus['percentage_paid'] >= 80 ? 'bg-green-500' : ($feeStatus && $feeStatus['percentage_paid'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }} rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Fee Status</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $feeStatus ? $feeStatus['percentage_paid'] . '%' : 'N/A' }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Overview -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">📅 My Attendance Records</h3>
                    <a href="{{ route('student.attendance.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View Full History</a>
                </div>
                
                <!-- Attendance Statistics -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-green-50 p-3 rounded-lg text-center">
                        <div class="text-lg font-bold text-green-600">{{ $attendanceStats['present_days'] }}</div>
                        <div class="text-xs text-green-600">Present Days</div>
                    </div>
                    <div class="bg-red-50 p-3 rounded-lg text-center">
                        <div class="text-lg font-bold text-red-600">{{ $attendanceStats['absent_days'] }}</div>
                        <div class="text-xs text-red-600">Absent Days</div>
                    </div>
                    <div class="bg-yellow-50 p-3 rounded-lg text-center">
                        <div class="text-lg font-bold text-yellow-600">{{ $attendanceStats['late_days'] }}</div>
                        <div class="text-xs text-yellow-600">Late Days</div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg text-center">
                        <div class="text-lg font-bold text-blue-600">{{ $attendanceStats['excused_days'] }}</div>
                        <div class="text-xs text-blue-600">Excused Days</div>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg text-center">
                        <div class="text-lg font-bold text-purple-600">{{ $attendanceStats['attendance_rate'] }}%</div>
                        <div class="text-xs text-purple-600">Attendance Rate</div>
                    </div>
                </div>
                
                <!-- Recent Attendance Records -->
                <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Recent Attendance Records</h4>
                    <div class="space-y-3">
                        @forelse($recentAttendance as $record)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full mr-3 
                                    @if($record->status === 'present') bg-green-500
                                    @elseif($record->status === 'absent') bg-red-500
                                    @elseif($record->status === 'late') bg-yellow-500
                                    @elseif($record->status === 'excused') bg-blue-500
                                    @else bg-gray-500 @endif"></span>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($record->attendance_date)->format('l, M d, Y') }}
                                    </div>
                                    @if($record->remarks)
                                    <div class="text-xs text-gray-500">{{ $record->remarks }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    @if($record->status === 'present') bg-green-100 text-green-800
                                    @elseif($record->status === 'absent') bg-red-100 text-red-800
                                    @elseif($record->status === 'late') bg-yellow-100 text-yellow-800
                                    @elseif($record->status === 'excused') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($record->status) }}
                                </span>
                                @if($record->marked_by)
                                <span class="text-xs text-gray-400">
                                    by {{ \App\Models\User::find($record->marked_by)->name ?? 'Teacher' }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No attendance records</h3>
                            <p class="mt-1 text-sm text-gray-500">Your teachers haven't recorded your attendance yet.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- This Month Summary -->
                @if($thisMonthAttendance->count() > 0)
                <div class="border-t pt-4 mt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">This Month Summary</h4>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <div class="text-lg font-bold text-blue-600">{{ $attendanceStats['this_month_total'] }}</div>
                                <div class="text-xs text-blue-600">Total Days</div>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-green-600">{{ $attendanceStats['this_month_present'] }}</div>
                                <div class="text-xs text-green-600">Present</div>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-red-600">{{ $thisMonthAttendance->where('status', 'absent')->count() }}</div>
                                <div class="text-xs text-red-600">Absent</div>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-purple-600">
                                    {{ $attendanceStats['this_month_total'] > 0 ? round(($attendanceStats['this_month_present'] / $attendanceStats['this_month_total']) * 100, 1) : 0 }}%
                                </div>
                                <div class="text-xs text-purple-600">Monthly Rate</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Fee Details -->
        @if($feeStatus)
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Fee Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-gray-900">${{ number_format($feeStatus['total_fees'], 2) }}</div>
                        <div class="text-sm text-gray-500">Total Fees</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">${{ number_format($feeStatus['total_paid'], 2) }}</div>
                        <div class="text-sm text-gray-500">Paid</div>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="text-2xl font-bold text-red-600">${{ number_format($feeStatus['pending'], 2) }}</div>
                        <div class="text-sm text-gray-500">Pending</div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $feeStatus['percentage_paid'] }}%"></div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2 text-center">{{ $feeStatus['percentage_paid'] }}% of fees paid</p>
                </div>
            </div>
        </div>
        @endif

        <!-- My Subjects -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">My Subjects</h3>
                @if($subjects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($subjects as $subject)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-lg font-medium text-gray-900">{{ $subject->name }}</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Active
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-2">{{ $subject->code }}</p>
                        <p class="text-sm text-gray-600 mb-3">{{ $subject->description ?? 'No description available' }}</p>
                        <div class="flex space-x-2">
                            <a href="{{ route('student.subjects.show', $subject->id) }}" class="text-sm text-blue-600 hover:text-blue-800">View Details</a>
                            <a href="{{ route('study-materials.index', ['subject' => $subject->id]) }}" class="text-sm text-green-600 hover:text-green-800">Materials</a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-8">No subjects assigned yet.</p>
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
                            <p class="text-sm text-gray-500">{{ $exam->subject->name ?? 'Subject' }}</p>
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

        <!-- Academic Performance -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Academic Performance</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('student.grades.index') }}" class="flex items-center justify-between p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">View Grades</h4>
                                <p class="text-xs text-gray-500">Check your academic performance</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('student.attendance.index') }}" class="flex items-center justify-between p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">My Attendance</h4>
                                <p class="text-xs text-gray-500">Track your attendance record</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('student.finance.index') }}" class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Fee Status</h4>
                                <p class="text-xs text-gray-500">Check payment status</p>
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
                    <a href="{{ route('study-materials.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Study Materials
                    </a>
                    <a href="{{ route('homework.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 19 16.5 19c-1.746 0-3.332-.477-4.5-1.253"></path>
                        </svg>
                        Homework
                    </a>
                    <a href="{{ route('student.grades.grade-sheet', ['year' => $session['academic_year'] ?? date('Y'), 'semester' => $session['semester'] ?? 1]) }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Grade Sheet
                    </a>
                    <a href="{{ route('student.exams.marks') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        My Results
                    </a>
                    <a href="{{ route('student.attendance.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        My Attendance
                    </a>
                    <a href="{{ route('student.profile') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        My Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Library, Transport & Hostel Services -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Campus Services</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Library Section -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 19 16.5 19c-1.746 0-3.332-.477-4.5-1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">Library</h4>
                                <p class="text-sm text-gray-500">Access books and resources</p>
                            </div>
                        </div>
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Books Available:</span>
                                <span class="font-medium">{{ $libraryStats['available_books'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">My Borrowed:</span>
                                <span class="font-medium">{{ $libraryStats['my_borrowed'] ?? 0 }}</span>
                            </div>
                        </div>
                        <a href="{{ route('student.library.index') }}" class="w-full bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 text-center block transition-colors">
                            Visit Library
                        </a>
                    </div>

                    <!-- Transport Section -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">Transport</h4>
                                <p class="text-sm text-gray-500">Bus routes and schedules</p>
                            </div>
                        </div>
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Active Routes:</span>
                                <span class="font-medium">{{ $transportStats['active_routes'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">My Route:</span>
                                <span class="font-medium">{{ $myRoute ? 'Assigned' : 'Not Assigned' }}</span>
                            </div>
                        </div>
                        <a href="{{ route('student.transport.index') }}" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-center block transition-colors">
                            View Transport
                        </a>
                    </div>

                    <!-- Hostel Section -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">Hostel</h4>
                                <p class="text-sm text-gray-500">Accommodation services</p>
                            </div>
                        </div>
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Available Rooms:</span>
                                <span class="font-medium">{{ $hostelStats['total_rooms'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">My Room:</span>
                                <span class="font-medium">{{ $myRoom ? 'Assigned' : 'Not Assigned' }}</span>
                            </div>
                        </div>
                        <a href="{{ route('student.hostel.index') }}" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-center block transition-colors">
                            View Hostel
                        </a>
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
                                            <time>{{ $activity['created_at'] ? $activity['created_at']->diffForHumans() : 'Recently' }}</time>
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
