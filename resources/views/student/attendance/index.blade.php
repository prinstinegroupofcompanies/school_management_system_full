@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
<style>
.attendance-container {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    min-height: 100vh;
    color: #e2e8f0;
}

.attendance-header {
    background: rgba(30, 41, 59, 0.8);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    padding: 2rem 0;
}

.attendance-content {
    padding: 2rem 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 1rem;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #94a3b8;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.present { color: #10b981; }
.absent { color: #ef4444; }
.late { color: #f59e0b; }
.percentage { color: #3b82f6; }

.attendance-table {
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 1rem;
    overflow: hidden;
}

.table-header {
    background: rgba(30, 41, 59, 0.8);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
}

.table-title {
    color: #cbd5e1;
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: rgba(30, 41, 59, 0.9);
    color: #94a3b8;
    font-weight: 600;
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.table td {
    padding: 1rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    color: #e2e8f0;
}

.table tbody tr:hover {
    background: rgba(30, 41, 59, 0.4);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-present {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-absent {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.status-late {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.monthly-chart {
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.chart-title {
    color: #cbd5e1;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.month-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
}

.month-item {
    text-align: center;
    padding: 1rem;
    background: rgba(30, 41, 59, 0.4);
    border-radius: 0.5rem;
    border: 1px solid rgba(148, 163, 184, 0.1);
}

.month-name {
    color: #94a3b8;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.month-percentage {
    color: #3b82f6;
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.month-details {
    color: #64748b;
    font-size: 0.7rem;
}

.back-btn {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.3);
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.back-btn:hover {
    background: rgba(59, 130, 246, 0.3);
    transform: translateY(-1px);
}
</style>

<div class="attendance-container">
    <!-- Header -->
    <div class="attendance-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">My Attendance</h1>
                    <p class="text-slate-300">Track your attendance records and statistics</p>
                </div>
                <a href="{{ route('student.dashboard') }}" class="back-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="attendance-content">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number percentage">{{ $stats['attendance_percentage'] }}%</div>
                    <div class="stat-label">Overall Attendance</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number present">{{ $stats['present_days'] }}</div>
                    <div class="stat-label">Present Days</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number absent">{{ $stats['absent_days'] }}</div>
                    <div class="stat-label">Absent Days</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number late">{{ $stats['late_days'] }}</div>
                    <div class="stat-label">Late Days</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #94a3b8;">{{ $stats['total_days'] }}</div>
                    <div class="stat-label">Total Days</div>
                </div>
            </div>

            <!-- Monthly Attendance Chart -->
            <div class="monthly-chart">
                <h3 class="chart-title">Monthly Attendance Overview</h3>
                <div class="month-grid">
                    @foreach($monthlyAttendance as $month)
                    <div class="month-item">
                        <div class="month-name">{{ $month['month'] }}</div>
                        <div class="month-percentage">{{ $month['percentage'] }}%</div>
                        <div class="month-details">{{ $month['present'] }}/{{ $month['total'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Attendance Records Table -->
            <div class="attendance-table">
                <div class="table-header">
                    <h3 class="table-title">Recent Attendance Records</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th>Marked By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceRecords as $record)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($record->attendance_date)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->attendance_date)->format('l') }}</td>
                                <td>
                                    <span class="status-badge status-{{ $record->status }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td>{{ $record->remarks ?? '-' }}</td>
                                <td>{{ $record->marked_by ?? 'System' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-slate-400">
                                    <div class="text-4xl mb-2">📅</div>
                                    <p>No attendance records found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($attendanceRecords->hasPages())
            <div class="mt-6">
                {{ $attendanceRecords->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
