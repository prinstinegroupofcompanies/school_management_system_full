@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">📊 Attendance Reports</h1>
                        <p class="mt-1 text-sm text-gray-600">Comprehensive attendance analytics and reporting dashboard</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('attendance.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Take Attendance
                        </a>
                        <button class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            Export Reports
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Student Attendance Today -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">👥 Student Attendance Today</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $studentStats['total_today'] }}</div>
                            <div class="text-sm text-gray-500">Total</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $studentStats['present_today'] }}</div>
                            <div class="text-sm text-gray-500">Present</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $studentStats['absent_today'] }}</div>
                            <div class="text-sm text-gray-500">Absent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $studentStats['late_today'] }}</div>
                            <div class="text-sm text-gray-500">Late</div>
                        </div>
                    </div>
                    
                    <!-- Attendance Rate -->
                    <div class="mt-4">
                        @php
                            $studentRate = $studentStats['total_today'] > 0 ? round(($studentStats['present_today'] / $studentStats['total_today']) * 100, 1) : 0;
                        @endphp
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Attendance Rate</span>
                            <span>{{ $studentRate }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full" style="width: {{ $studentRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teacher Attendance Today -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">👨‍🏫 Teacher Attendance Today</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $teacherStats['total_today'] }}</div>
                            <div class="text-sm text-gray-500">Total</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $teacherStats['present_today'] }}</div>
                            <div class="text-sm text-gray-500">Present</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $teacherStats['absent_today'] }}</div>
                            <div class="text-sm text-gray-500">Absent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $teacherStats['late_today'] }}</div>
                            <div class="text-sm text-gray-500">Late</div>
                        </div>
                    </div>
                    
                    <!-- Attendance Rate -->
                    <div class="mt-4">
                        @php
                            $teacherRate = $teacherStats['total_today'] > 0 ? round(($teacherStats['present_today'] / $teacherStats['total_today']) * 100, 1) : 0;
                        @endphp
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Attendance Rate</span>
                            <span>{{ $teacherRate }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full" style="width: {{ $teacherRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Statistics -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-6">📅 Monthly Statistics ({{ \Carbon\Carbon::now()->format('F Y') }})</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Monthly Student Stats -->
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h4 class="text-md font-medium text-blue-900 mb-4">Student Attendance</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-blue-700">Total Records:</span>
                                <span class="text-sm font-medium text-blue-900">{{ $monthlyStudentStats['total'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-green-700">Present:</span>
                                <span class="text-sm font-medium text-green-900">{{ $monthlyStudentStats['present'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-red-700">Absent:</span>
                                <span class="text-sm font-medium text-red-900">{{ $monthlyStudentStats['absent'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-purple-700">Attendance Rate:</span>
                                <span class="text-sm font-medium text-purple-900">
                                    {{ $monthlyStudentStats['total'] > 0 ? round(($monthlyStudentStats['present'] / $monthlyStudentStats['total']) * 100, 1) : 0 }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Teacher Stats -->
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h4 class="text-md font-medium text-green-900 mb-4">Teacher Attendance</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-green-700">Total Records:</span>
                                <span class="text-sm font-medium text-green-900">{{ $monthlyTeacherStats['total'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-green-700">Present:</span>
                                <span class="text-sm font-medium text-green-900">{{ $monthlyTeacherStats['present'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-red-700">Absent:</span>
                                <span class="text-sm font-medium text-red-900">{{ $monthlyTeacherStats['absent'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-purple-700">Attendance Rate:</span>
                                <span class="text-sm font-medium text-purple-900">
                                    {{ $monthlyTeacherStats['total'] > 0 ? round(($monthlyTeacherStats['present'] / $monthlyTeacherStats['total']) * 100, 1) : 0 }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Generation Tools -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-6">🔧 Generate Reports</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Daily Report -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Daily Report</h4>
                        <p class="text-sm text-gray-600 mb-4">Generate attendance report for a specific date</p>
                        <form action="{{ route('attendance.reports') }}" method="GET" class="space-y-3">
                            <input type="hidden" name="report_type" value="daily">
                            <div>
                                <label for="daily_date" class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="date" id="daily_date" name="date" value="{{ today()->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Generate Daily Report
                            </button>
                        </form>
                    </div>

                    <!-- Monthly Report -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Monthly Report</h4>
                        <p class="text-sm text-gray-600 mb-4">Generate attendance report for a specific month</p>
                        <form action="{{ route('attendance.reports') }}" method="GET" class="space-y-3">
                            <input type="hidden" name="report_type" value="monthly">
                            <div>
                                <label for="monthly_date" class="block text-sm font-medium text-gray-700">Month</label>
                                <input type="month" id="monthly_date" name="month" value="{{ $currentMonth }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                Generate Monthly Report
                            </button>
                        </form>
                    </div>

                    <!-- Custom Range Report -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Custom Range</h4>
                        <p class="text-sm text-gray-600 mb-4">Generate report for a custom date range</p>
                        <form action="{{ route('attendance.reports') }}" method="GET" class="space-y-3">
                            <input type="hidden" name="report_type" value="custom">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="{{ today()->subDays(30)->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ today()->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                                Generate Custom Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance Records -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Recent Student Attendance -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">🎓 Recent Student Attendance</h3>
                    @if($recentStudentAttendance->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentStudentAttendance as $record)
                        @if($record->student && $record->student->user)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full mr-3 
                                    @if($record->status === 'present') bg-green-500
                                    @elseif($record->status === 'absent') bg-red-500
                                    @elseif($record->status === 'late') bg-yellow-500
                                    @else bg-blue-500 @endif"></span>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $record->student->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $record->student->classRoom->name ?? 'N/A' }} • {{ \Carbon\Carbon::parse($record->attendance_date)->format('M d, Y') }}</div>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($record->status === 'present') bg-green-100 text-green-800
                                @elseif($record->status === 'absent') bg-red-100 text-red-800
                                @elseif($record->status === 'late') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst($record->status) }}
                            </span>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No student attendance records</h3>
                        <p class="mt-1 text-sm text-gray-500">No student attendance has been recorded yet.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Teacher Attendance -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">👨‍🏫 Recent Teacher Attendance</h3>
                    @if($recentTeacherAttendance->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentTeacherAttendance as $record)
                        @if($record->teacher && $record->teacher->user)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full mr-3 
                                    @if($record->status === 'present') bg-green-500
                                    @elseif($record->status === 'absent') bg-red-500
                                    @elseif($record->status === 'late') bg-yellow-500
                                    @else bg-blue-500 @endif"></span>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $record->teacher->user->name }}</div>
                                    <div class="text-xs text-gray-500">Teacher • {{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</div>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($record->status === 'present') bg-green-100 text-green-800
                                @elseif($record->status === 'absent') bg-red-100 text-red-800
                                @elseif($record->status === 'late') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst($record->status) }}
                            </span>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No teacher attendance records</h3>
                        <p class="mt-1 text-sm text-gray-500">No teacher attendance has been recorded yet.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
