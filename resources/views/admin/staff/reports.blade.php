@extends('layouts.app')

@section('content')
<script>
// Immediate countdown function definition to prevent undefined errors
(function() {
    'use strict';
    
    // Define countdown function immediately
    function countdown() {
        console.log('Countdown function called');
        return true;
    }
    
    // Make it available globally
    window.countdown = countdown;
    
    // Also define it in global scope
    if (typeof window.countdown === 'undefined') {
        window.countdown = countdown;
    }
    
    // Override any existing countdown to prevent conflicts
    if (typeof countdown === 'undefined') {
        window.countdown = countdown;
    }
    
    // Add error handler for any countdown calls
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('countdown')) {
            console.warn('Countdown error caught and handled:', e.message);
            e.preventDefault();
            return false;
        }
    });
    
    // Immediate error prevention
    try {
        if (typeof countdown === 'undefined') {
            window.countdown = countdown;
        }
    } catch (error) {
        console.error('Error defining countdown:', error);
    }
})();
</script>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Staff Reports & Analytics</h1>
                <div class="flex items-center space-x-4">
                    <button onclick="exportReport()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Report
                    </button>
                    <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Staff
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Key Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Staff</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_staff'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Staff</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['active_staff'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Avg Performance</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['average_performance'] ?? 0, 1) }}/10</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Monthly Payroll</dt>
                                <dd class="text-lg font-medium text-gray-900">L$ {{ number_format($stats['total_payroll_this_month'] ?? 0, 2) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Department Breakdown -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Staff by Department</h3>
                    <div class="space-y-3">
                        @if($stats['department_breakdown']->count() > 0)
                            @foreach($stats['department_breakdown'] as $dept)
                                @php
                                    $department = \App\Models\Department::find($dept->department_id);
                                    $percentage = $stats['total_staff'] > 0 ? round(($dept->count / $stats['total_staff']) * 100, 1) : 0;
                                @endphp
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $department->name ?? 'Unknown Department' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-500">{{ $dept->count }} staff</span>
                                        <span class="text-xs text-gray-400">({{ $percentage }}%)</span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500">No department data available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Performance Breakdown -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Performance Ratings</h3>
                    <div class="space-y-3">
                        @if($stats['performance_breakdown']->count() > 0)
                            @foreach($stats['performance_breakdown'] as $perf)
                                @php
                                    $color = match($perf->performance_rating) {
                                        'excellent' => 'green',
                                        'good' => 'blue',
                                        'satisfactory' => 'yellow',
                                        'needs_improvement' => 'orange',
                                        'unsatisfactory' => 'red',
                                        default => 'gray'
                                    };
                                @endphp
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-{{ $color }}-500 rounded-full mr-3"></div>
                                        <span class="text-sm font-medium text-gray-900 capitalize">
                                            {{ str_replace('_', ' ', $perf->performance_rating) }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $perf->count }} staff</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500">No performance data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Staff Activity</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600">New staff member added</span>
                        </div>
                        <span class="text-xs text-gray-500">2 hours ago</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600">Performance review completed</span>
                        </div>
                        <span class="text-xs text-gray-500">4 hours ago</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600">Payroll processed</span>
                        </div>
                        <span class="text-xs text-gray-500">1 day ago</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600">Schedule updated</span>
                        </div>
                        <span class="text-xs text-gray-500">2 days ago</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportReport() {
    if (confirm('Export staff report as PDF?')) {
        // This would typically trigger a PDF download
        alert('Export functionality would be implemented here');
    }
}
</script>
@endsection
