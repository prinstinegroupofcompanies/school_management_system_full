@extends('layouts.app')

@section('title', 'Attendance Summary')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">📊 Attendance Summary</h1>
                        <p class="mt-1 text-sm text-gray-600">Overview of your attendance performance and trends</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('student.attendance.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Overview
                        </a>
                        <a href="{{ route('student.attendance.history') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            History
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yearly Summary -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">📅 This Year ({{ date('Y') }})</h3>
                <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                    <!-- Total Days -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">{{ $yearlyStats['total'] }}</div>
                        <div class="text-sm text-gray-500">Total Days</div>
                    </div>
                    
                    <!-- Present Days -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">{{ $yearlyStats['present'] }}</div>
                        <div class="text-sm text-gray-500">Present</div>
                    </div>
                    
                    <!-- Absent Days -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-red-600">{{ $yearlyStats['absent'] }}</div>
                        <div class="text-sm text-gray-500">Absent</div>
                    </div>
                    
                    <!-- Late Days -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-600">{{ $yearlyStats['late'] }}</div>
                        <div class="text-sm text-gray-500">Late</div>
                    </div>
                    
                    <!-- Excused Days -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $yearlyStats['excused'] }}</div>
                        <div class="text-sm text-gray-500">Excused</div>
                    </div>
                    
                    <!-- Attendance Rate -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ $yearlyStats['percentage'] }}%</div>
                        <div class="text-sm text-gray-500">Attendance Rate</div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Attendance Progress</span>
                        <span>{{ $yearlyStats['percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full" style="width: {{ $yearlyStats['percentage'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Summary -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">📅 This Month ({{ \Carbon\Carbon::now()->format('F Y') }})</h3>
                <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                    <!-- Total Days -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">{{ $monthlyStats['total'] }}</div>
                        <div class="text-sm text-gray-500">Total Days</div>
                    </div>
                    
                    <!-- Present Days -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">{{ $monthlyStats['present'] }}</div>
                        <div class="text-sm text-gray-500">Present</div>
                    </div>
                    
                    <!-- Absent Days -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-red-600">{{ $monthlyStats['absent'] }}</div>
                        <div class="text-sm text-gray-500">Absent</div>
                    </div>
                    
                    <!-- Late Days -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-600">{{ $monthlyStats['late'] }}</div>
                        <div class="text-sm text-gray-500">Late</div>
                    </div>
                    
                    <!-- Excused Days -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $monthlyStats['excused'] }}</div>
                        <div class="text-sm text-gray-500">Excused</div>
                    </div>
                    
                    <!-- Attendance Rate -->
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ $monthlyStats['percentage'] }}%</div>
                        <div class="text-sm text-gray-500">Attendance Rate</div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Monthly Progress</span>
                        <span>{{ $monthlyStats['percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full" style="width: {{ $monthlyStats['percentage'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Analysis -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Attendance Trends -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📈 Performance Analysis</h3>
                    <div class="space-y-4">
                        <!-- Yearly vs Monthly Comparison -->
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Yearly Attendance</span>
                            <span class="text-sm font-bold {{ $yearlyStats['percentage'] >= 75 ? 'text-green-600' : ($yearlyStats['percentage'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $yearlyStats['percentage'] }}%
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Monthly Attendance</span>
                            <span class="text-sm font-bold {{ $monthlyStats['percentage'] >= 75 ? 'text-green-600' : ($monthlyStats['percentage'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $monthlyStats['percentage'] }}%
                            </span>
                        </div>
                        
                        <!-- Performance Status -->
                        <div class="mt-4">
                            @if($yearlyStats['percentage'] >= 90)
                            <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800">Excellent Attendance!</h3>
                                        <p class="text-sm text-green-700">Your attendance is outstanding. Keep up the great work!</p>
                                    </div>
                                </div>
                            </div>
                            @elseif($yearlyStats['percentage'] >= 75)
                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">Good Attendance</h3>
                                        <p class="text-sm text-blue-700">Your attendance is good. Try to maintain or improve it!</p>
                                    </div>
                                </div>
                            </div>
                            @elseif($yearlyStats['percentage'] >= 50)
                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Needs Improvement</h3>
                                        <p class="text-sm text-yellow-700">Your attendance needs improvement. Try to attend more regularly.</p>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Poor Attendance</h3>
                                        <p class="text-sm text-red-700">Your attendance is below average. Please improve your attendance.</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📋 Quick Stats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Best Performance:</span>
                            <span class="text-sm font-medium text-green-600">
                                @if($yearlyStats['percentage'] > $monthlyStats['percentage'])
                                    This Year ({{ $yearlyStats['percentage'] }}%)
                                @else
                                    This Month ({{ $monthlyStats['percentage'] }}%)
                                @endif
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Present Days:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $yearlyStats['present'] }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Absent Days:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $yearlyStats['absent'] }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Late Arrivals:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $yearlyStats['late'] }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Excused Absences:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $yearlyStats['excused'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🕒 Recent Attendance (Last 10 Records)</h3>
                @if($recentAttendance->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentAttendance as $record)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($record->attendance_date)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($record->attendance_date)->format('l') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($record->status === 'present') bg-green-100 text-green-800
                                        @elseif($record->status === 'absent') bg-red-100 text-red-800
                                        @elseif($record->status === 'late') bg-yellow-100 text-yellow-800
                                        @elseif($record->status === 'excused') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $record->remarks ?: '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No attendance records</h3>
                    <p class="mt-1 text-sm text-gray-500">No attendance records found yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
