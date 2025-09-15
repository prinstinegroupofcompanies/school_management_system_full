@extends('layouts.app')

@section('title', 'Comprehensive Reports')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Comprehensive Reports</h1>
        <div class="flex space-x-4">
            <button onclick="exportReport()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                Export All Reports
            </button>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Academic Reports -->
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Academic Reports</h3>
                    <p class="text-sm text-gray-600">Student performance, grades, and academic analytics</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.academic') }}" class="block w-full text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                    View Academic Reports
                </a>
                <div class="text-xs text-gray-500 text-center">
                    Student stats, grade analysis, subject performance
                </div>
            </div>
        </div>

        <!-- Financial Reports -->
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Financial Reports</h3>
                    <p class="text-sm text-gray-600">Fee collection, payments, and financial analytics</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.financial') }}" class="block w-full text-center bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                    View Financial Reports
                </a>
                <div class="text-xs text-gray-500 text-center">
                    Fee collection, payment methods, revenue analysis
                </div>
            </div>
        </div>

        <!-- Attendance Reports -->
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Attendance Reports</h3>
                    <p class="text-sm text-gray-600">Student attendance patterns and analytics</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.attendance') }}" class="block w-full text-center bg-yellow-600 text-white py-2 rounded-lg hover:bg-yellow-700">
                    View Attendance Reports
                </a>
                <div class="text-xs text-gray-500 text-center">
                    Attendance rates, trends, class-wise analysis
                </div>
            </div>
        </div>

        <!-- Staff Reports -->
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Staff Reports</h3>
                    <p class="text-sm text-gray-600">Staff performance, payroll, and HR analytics</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.staff') }}" class="block w-full text-center bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700">
                    View Staff Reports
                </a>
                <div class="text-xs text-gray-500 text-center">
                    Performance reviews, payroll analysis, department stats
                </div>
            </div>
        </div>

        <!-- Library Reports -->
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Library Reports</h3>
                    <p class="text-sm text-gray-600">Book circulation, popular titles, and library analytics</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('admin.reports.library') }}" class="block w-full text-center bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">
                    View Library Reports
                </a>
                <div class="text-xs text-gray-500 text-center">
                    Book circulation, popular titles, overdue books
                </div>
            </div>
        </div>

        <!-- System Reports -->
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-gray-100 rounded-lg">
                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">System Reports</h3>
                    <p class="text-sm text-gray-600">System usage, notifications, and technical analytics</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="#" class="block w-full text-center bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700">
                    View System Reports
                </a>
                <div class="text-xs text-gray-500 text-center">
                    System usage, notifications, user activity
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ \App\Models\Student::count() }}</div>
                <div class="text-sm text-gray-600">Total Students</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ \App\Models\Teacher::count() }}</div>
                <div class="text-sm text-gray-600">Total Teachers</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">L$ {{ number_format(\App\Models\FeePayment::where('status', 'paid')->sum('amount_paid'), 2) }}</div>
                <div class="text-sm text-gray-600">Total Revenue</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-yellow-600">{{ \App\Models\StudentAttendance::where('status', 'present')->count() }}</div>
                <div class="text-sm text-gray-600">Attendance Records</div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent System Activity</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                    <span class="text-sm text-gray-600">New student enrolled</span>
                </div>
                <span class="text-xs text-gray-500">2 minutes ago</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                    <span class="text-sm text-gray-600">Fee payment received</span>
                </div>
                <span class="text-xs text-gray-500">15 minutes ago</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                    <span class="text-sm text-gray-600">Exam scheduled</span>
                </div>
                <span class="text-xs text-gray-500">1 hour ago</span>
            </div>
        </div>
    </div>
</div>

<script>
function exportReport() {
    if (confirm('Export all reports as PDF?')) {
        // This would typically trigger a download
        alert('Export functionality would be implemented here');
    }
}
</script>
@endsection