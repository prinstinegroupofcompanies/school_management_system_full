@extends('layouts.app')

@section('title', 'Attendance Details')

@section('content')
<style>
.attendance-detail-container {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    min-height: 100vh;
    color: #e2e8f0;
}

.attendance-detail-header {
    background: rgba(30, 41, 59, 0.8);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    padding: 2rem 0;
}

.attendance-detail-content {
    padding: 2rem 0;
}

.detail-card {
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 1rem;
    padding: 2rem;
    margin-bottom: 2rem;
}

.detail-title {
    color: #cbd5e1;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.detail-item {
    background: rgba(30, 41, 59, 0.4);
    border: 1px solid rgba(148, 163, 184, 0.1);
    border-radius: 0.5rem;
    padding: 1rem;
}

.detail-label {
    color: #94a3b8;
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.detail-value {
    color: #e2e8f0;
    font-size: 1.1rem;
    font-weight: 600;
}

.status-badge-large {
    padding: 0.75rem 1.5rem;
    border-radius: 9999px;
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: inline-block;
}

.status-present {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    border: 2px solid rgba(16, 185, 129, 0.3);
}

.status-absent {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 2px solid rgba(239, 68, 68, 0.3);
}

.status-late {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
    border: 2px solid rgba(245, 158, 11, 0.3);
}

.back-btn {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.3);
    padding: 0.75rem 1.5rem;
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

<div class="attendance-detail-container">
    <!-- Header -->
    <div class="attendance-detail-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Attendance Details</h1>
                    <p class="text-slate-300">Detailed view of attendance record</p>
                </div>
                <a href="{{ route('student.attendance.index') }}" class="back-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Attendance
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="attendance-detail-content">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="detail-card">
                <h2 class="detail-title">Attendance Information</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Date</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Day</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <span class="status-badge-large status-{{ $attendance->status }}">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Marked By</div>
                        <div class="detail-value">{{ $attendance->marked_by ?? 'System' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Marked At</div>
                        <div class="detail-value">{{ $attendance->created_at ? \Carbon\Carbon::parse($attendance->created_at)->format('M d, Y h:i A') : '-' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Last Updated</div>
                        <div class="detail-value">{{ $attendance->updated_at ? \Carbon\Carbon::parse($attendance->updated_at)->format('M d, Y h:i A') : '-' }}</div>
                    </div>
                </div>
            </div>

            @if($attendance->remarks)
            <div class="detail-card">
                <h2 class="detail-title">Remarks</h2>
                <div class="bg-slate-800 p-4 rounded-lg border border-slate-700">
                    <p class="text-slate-200 leading-relaxed">{{ $attendance->remarks }}</p>
                </div>
            </div>
            @endif

            <div class="detail-card">
                <h2 class="detail-title">Additional Information</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Student ID</div>
                        <div class="detail-value">{{ $attendance->student_id }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Record ID</div>
                        <div class="detail-value">{{ $attendance->id }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
