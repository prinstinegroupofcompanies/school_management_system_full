@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">Welcome, {{ auth()->user()->name }}</span>
                    @if(isset($session))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        Session: {{ $session['academic_year'] }} • Sem {{ $session['semester'] }}
                    </span>
                    @endif
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Administrator
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_students']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Teachers -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Teachers</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_teachers']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

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
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Classes</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_classes']) }}</dd>
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
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_subjects']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Collection Today -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Fees Today</dt>
                                <dd class="text-lg font-medium text-gray-900">${{ number_format($feeStats['collected_today'], 2) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Student Attendance -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">👥 Student Attendance Today</h3>
                        <a href="{{ route('attendance.student') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                    </div>
                    
                    <!-- Student Attendance Stats -->
                    <div class="grid grid-cols-4 gap-3 mb-4">
                        <div class="text-center">
                            <div class="text-xl font-bold text-green-600">{{ $attendanceStats['students']['present'] }}</div>
                            <div class="text-xs text-gray-500">Present</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-red-600">{{ $attendanceStats['students']['absent'] }}</div>
                            <div class="text-xs text-gray-500">Absent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-yellow-600">{{ $attendanceStats['students']['late'] }}</div>
                            <div class="text-xs text-gray-500">Late</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-blue-600">{{ $attendanceStats['students']['excused'] }}</div>
                            <div class="text-xs text-gray-500">Excused</div>
                        </div>
                    </div>
                    
                    <!-- Recent Student Attendance Records -->
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Recent Records</h4>
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
                            <p class="text-gray-500 text-sm text-center py-2">No attendance records for today</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teacher Attendance -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">👨‍🏫 Teacher Attendance Today</h3>
                        <a href="{{ route('attendance.teacher') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                    </div>
                    
                    <!-- Teacher Attendance Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-xl font-bold text-green-600">{{ $attendanceStats['teachers']['present'] }}</div>
                            <div class="text-xs text-gray-500">Present</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-red-600">{{ $attendanceStats['teachers']['absent'] }}</div>
                            <div class="text-xs text-gray-500">Absent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-yellow-600">{{ $attendanceStats['teachers']['late'] }}</div>
                            <div class="text-xs text-gray-500">Late</div>
                        </div>
                    </div>
                    
                    <!-- Recent Teacher Attendance Records -->
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
                                    <span class="text-gray-900">{{ $attendance->teacher->user->name }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-gray-500">{{ $attendance->teacher->employee_id }}</span>
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
                            <p class="text-gray-500 text-sm text-center py-2">No teacher attendance records for today</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Summary -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">📊 Attendance Summary</h3>
                    <div class="flex space-x-2">
                        <a href="{{ route('attendance.reports') }}" class="text-sm text-blue-600 hover:text-blue-800">Detailed Reports</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('attendance.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Take Attendance</a>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Student Summary -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Student Attendance</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-blue-700">Total Students Today:</span>
                                <span class="text-sm font-medium text-blue-900">{{ $attendanceStats['students']['total'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-green-700">Present:</span>
                                <span class="text-sm font-medium text-green-900">{{ $attendanceStats['students']['present'] }} ({{ $attendanceStats['students']['total'] > 0 ? round(($attendanceStats['students']['present'] / $attendanceStats['students']['total']) * 100, 1) : 0 }}%)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-red-700">Absent:</span>
                                <span class="text-sm font-medium text-red-900">{{ $attendanceStats['students']['absent'] }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Teacher Summary -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-green-900 mb-2">Teacher Attendance</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-green-700">Total Teachers Today:</span>
                                <span class="text-sm font-medium text-green-900">{{ $attendanceStats['teachers']['total'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-green-700">Present:</span>
                                <span class="text-sm font-medium text-green-900">{{ $attendanceStats['teachers']['present'] }} ({{ $attendanceStats['teachers']['total'] > 0 ? round(($attendanceStats['teachers']['present'] / $attendanceStats['teachers']['total']) * 100, 1) : 0 }}%)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-red-700">Absent:</span>
                                <span class="text-sm font-medium text-red-900">{{ $attendanceStats['teachers']['absent'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Overview Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Fee Overview -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Fee Overview</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Total Fees:</span>
                            <span class="text-sm font-medium text-gray-900">${{ number_format($feeStats['pending'] + $feeStats['collected_today'], 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Collected Today:</span>
                            <span class="text-sm font-medium text-green-600">${{ number_format($feeStats['collected_today'], 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Pending:</span>
                            <span class="text-sm font-medium text-red-600">${{ number_format($feeStats['pending'], 2) }}</span>
                        </div>
                        @if(isset($feeStats['pending_approvals']) && $feeStats['pending_approvals'] > 0)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-yellow-600 font-medium">Pending Approvals:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $feeStats['pending_approvals'] }} payments
                                </span>
                            </div>
                        </div>
                        @endif
                        <div class="mt-4">
                            <a href="{{ route('admin.finance.analytics') }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                View Financial Analytics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Reports Section -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">💰 Financial Reports</h3>
                    <p class="text-sm text-gray-500">Comprehensive financial analytics and revenue insights</p>
                    <span class="text-xs text-gray-400">Last updated: {{ now()->format('M d, Y H:i') }}</span>
                </div>
                
                <!-- Export Buttons -->
                <div class="flex space-x-2 mb-6">
                    <button class="inline-flex items-center px-3 py-1 border border-blue-300 text-xs font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100">
                        📄 Export PDF
                    </button>
                    <button class="inline-flex items-center px-3 py-1 border border-green-300 text-xs font-medium rounded text-green-700 bg-green-50 hover:bg-green-100">
                        📊 Export Excel
                    </button>
                    <button class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-gray-50 hover:bg-gray-100">
                        🖨️ Print Report
                    </button>
                </div>

                <!-- Financial Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <!-- Total Revenue -->
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center mr-3">
                                <span class="text-white text-sm">💰</span>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-green-600 uppercase">Total Revenue</p>
                                <p class="text-lg font-bold text-gray-900">${{ number_format($feeStats['total_revenue'], 2) }}</p>
                                <p class="text-xs text-green-600">+12.5% from last month</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Expenses -->
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center mr-3">
                                <span class="text-white text-sm">📉</span>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-red-600 uppercase">Total Expenses</p>
                                <p class="text-lg font-bold text-gray-900">${{ number_format($feeStats['total_expenses'], 2) }}</p>
                                <p class="text-xs text-red-600">+8.2% from last month</p>
                            </div>
                        </div>
                    </div>

                    <!-- Net Profit -->
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center mr-3">
                                <span class="text-white text-sm">📈</span>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-blue-600 uppercase">Net Profit</p>
                                <p class="text-lg font-bold text-gray-900">
                                    @if($feeStats['net_profit'] >= 0)
                                        <span class="text-green-600">${{ number_format($feeStats['net_profit'], 2) }}</span>
                                    @else
                                        <span class="text-red-600">-${{ number_format(abs($feeStats['net_profit']), 2) }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-blue-600">+15.3% from last month</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Payments -->
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center mr-3">
                                <span class="text-white text-sm">⏳</span>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-yellow-600 uppercase">Pending Payments</p>
                                <p class="text-lg font-bold text-gray-900">${{ number_format($feeStats['pending'], 2) }}</p>
                                <p class="text-xs text-yellow-600">{{ $feeStats['pending_approvals'] }} outstanding</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Placeholder -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Revenue Trends -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Revenue Trends</h4>
                        <div class="h-32 bg-white rounded border flex items-center justify-center">
                            <p class="text-gray-500 text-sm">Chart visualization available in full analytics view</p>
                        </div>
                    </div>

                    <!-- Expense Breakdown -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Expense Breakdown</h4>
                        <div class="h-32 bg-white rounded border flex items-center justify-center">
                            <p class="text-gray-500 text-sm">Chart visualization available in full analytics view</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Modules -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Staff Management -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Staff Management</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.staff.index') }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Staff Records</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <a href="{{ route('admin.staff.performance') }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Performance Tracking</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <a href="{{ route('admin.staff.payroll') }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Payroll Management</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Financial Management -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Financial Management</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.finance.analytics') }}" class="flex items-center justify-between p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Financial Analytics</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <a href="{{ route('admin.finance.payments') }}" class="flex items-center justify-between p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Payment Management</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <a href="{{ route('admin.finance.reports') }}" class="flex items-center justify-between p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Fee Reports</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <a href="{{ route('admin.finance.income') }}" class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Income Analysis</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notifications & Reports -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Notifications & Reports</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.notifications.index') }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12 7M4.828 7H3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-1.828M4.828 7l2.586-2.586a2 2 0 012.828 0L12 7m8 0v10a2 2 0 01-2 2H7m5-5h5m-5 0l-2.5-2.5M12 12l2.5 2.5"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">SMS/Email Alerts</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Comprehensive Reports</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <a href="{{ route('admin.staff.schedules') }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Staff Schedules</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <a href="{{ route('admin.students.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Student
                    </a>
                    <a href="{{ route('admin.teachers.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Teacher
                    </a>
                    <a href="{{ route('admin.classes.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Class
                    </a>
                    <a href="{{ route('admin.subjects.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Subject
                    </a>
                    <a href="{{ route('admin.transport.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Transport
                    </a>
                    <a href="{{ route('admin.hostel.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Hostel
                    </a>
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
                                            <time>{{ $activity['created_at']->diffForHumans() }}</time>
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
