@extends('layouts.app')

@section('title', 'My Exam Marks')

@section('content')
<style>
.marks-container {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    min-height: 100vh;
    color: #e2e8f0;
    padding: 0;
    margin: 0;
}

.marks-header {
    background: rgba(30, 41, 59, 0.9);
    backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    padding: 2rem 0;
    margin: 0;
}

.marks-content {
    padding: 2rem 0;
    max-width: 1200px;
    margin: 0 auto;
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
    color: #3b82f6;
    text-decoration: none;
}

.page-title {
    color: #e2e8f0;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.page-subtitle {
    color: #94a3b8;
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.exam-card {
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 1rem;
    padding: 2rem;
    margin-bottom: 2rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.exam-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
    border-color: rgba(148, 163, 184, 0.4);
}

.exam-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.exam-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
}

.exam-title {
    color: #e2e8f0;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.exam-subtitle {
    color: #94a3b8;
    font-size: 1rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-completed {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.status-pending {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.exam-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.detail-section {
    background: rgba(15, 23, 42, 0.4);
    border-radius: 0.75rem;
    padding: 1.5rem;
    border: 1px solid rgba(148, 163, 184, 0.1);
}

.section-title {
    color: #cbd5e1;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    color: #94a3b8;
    font-weight: 500;
    font-size: 0.95rem;
}

.detail-value {
    color: #e2e8f0;
    font-weight: 600;
    font-size: 1rem;
}

.grade-badge {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.grade-a {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.grade-b {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.grade-c {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.grade-f {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.view-btn {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.view-btn:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    color: white;
    text-decoration: none;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 1rem;
    margin-top: 2rem;
}

.empty-icon {
    width: 6rem;
    height: 6rem;
    background: rgba(148, 163, 184, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
}

.empty-icon svg {
    width: 3rem;
    height: 3rem;
    color: #94a3b8;
}

.empty-title {
    color: #e2e8f0;
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.empty-text {
    color: #94a3b8;
    font-size: 1.1rem;
    margin-bottom: 2rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.empty-action {
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

.empty-action:hover {
    background: rgba(59, 130, 246, 0.3);
    transform: translateY(-1px);
    color: #3b82f6;
    text-decoration: none;
}

.performance-chart {
    background: rgba(15, 23, 42, 0.4);
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(148, 163, 184, 0.1);
}

.chart-title {
    color: #cbd5e1;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.performance-bar {
    background: rgba(148, 163, 184, 0.2);
    height: 0.5rem;
    border-radius: 9999px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.performance-fill {
    height: 100%;
    border-radius: 9999px;
    transition: width 0.3s ease;
}

.performance-text {
    color: #94a3b8;
    font-size: 0.9rem;
    text-align: right;
}

.icon {
    width: 1.25rem;
    height: 1.25rem;
}
</style>

<div class="marks-container">
    <!-- Header -->
    <div class="marks-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">My Exam Marks</h1>
                    <p class="page-subtitle">View your exam results and academic performance</p>
                </div>
                <a href="{{ route('student.exams.index') }}" class="back-btn">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Exams
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="marks-content">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($examAttempts && $examAttempts->count() > 0)
                @foreach($examAttempts as $attempt)
                <div class="exam-card">
                    <div class="exam-header">
                        <div>
                            <h3 class="exam-title">{{ $attempt->examSchedule->title }}</h3>
                            <p class="exam-subtitle">
                                {{ $attempt->examSchedule->subject->name }} - {{ $attempt->examSchedule->class->name }}
                            </p>
                        </div>
                        <span class="status-badge status-{{ $attempt->status === 'completed' ? 'completed' : 'pending' }}">
                            {{ ucfirst($attempt->status) }}
                        </span>
                    </div>

                    <div class="exam-details">
                        <!-- Exam Information -->
                        <div class="detail-section">
                            <h4 class="section-title">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Exam Details
                            </h4>
                            <div class="detail-item">
                                <span class="detail-label">Exam Type</span>
                                <span class="detail-value">{{ $attempt->examSchedule->examType->name ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Subject</span>
                                <span class="detail-value">{{ $attempt->examSchedule->subject->name }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Class</span>
                                <span class="detail-value">{{ $attempt->examSchedule->class->name }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Exam Date</span>
                                <span class="detail-value">
                                    {{ $attempt->examSchedule->start_date ? \Carbon\Carbon::parse($attempt->examSchedule->start_date)->format('M d, Y') : 'N/A' }}
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Submitted</span>
                                <span class="detail-value">
                                    {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('M d, Y H:i') : 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <!-- Performance -->
                        <div class="detail-section">
                            <h4 class="section-title">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Performance
                            </h4>
                            <div class="detail-item">
                                <span class="detail-label">Total Marks</span>
                                <span class="detail-value">{{ $attempt->total_marks ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Obtained Marks</span>
                                <span class="detail-value">{{ $attempt->obtained_marks ?? 'Pending' }}</span>
                            </div>
                            @if($attempt->obtained_marks && $attempt->total_marks)
                            <div class="detail-item">
                                <span class="detail-label">Percentage</span>
                                <span class="detail-value">{{ round(($attempt->obtained_marks / $attempt->total_marks) * 100, 2) }}%</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Grade</span>
                                <span class="detail-value">
                                    @php
                                        $percentage = ($attempt->obtained_marks / $attempt->total_marks) * 100;
                                        if ($percentage >= 90) $grade = 'A+';
                                        elseif ($percentage >= 80) $grade = 'A';
                                        elseif ($percentage >= 70) $grade = 'B';
                                        elseif ($percentage >= 60) $grade = 'C';
                                        elseif ($percentage >= 50) $grade = 'D';
                                        else $grade = 'F';
                                        
                                        $gradeClass = 'grade-f';
                                        if ($percentage >= 80) $gradeClass = 'grade-a';
                                        elseif ($percentage >= 70) $gradeClass = 'grade-b';
                                        elseif ($percentage >= 60) $gradeClass = 'grade-c';
                                    @endphp
                                    <span class="grade-badge {{ $gradeClass }}">{{ $grade }}</span>
                                </span>
                            </div>
                            
                            <!-- Performance Bar -->
                            <div class="performance-chart">
                                <div class="chart-title">Performance</div>
                                <div class="performance-bar">
                                    <div class="performance-fill" style="width: {{ $percentage }}%; background: linear-gradient(135deg, 
                                        @if($percentage >= 80) #22c55e 0%, #16a34a 100%
                                        @elseif($percentage >= 70) #3b82f6 0%, #1d4ed8 100%
                                        @elseif($percentage >= 60) #f59e0b 0%, #d97706 100%
                                        @else #ef4444 0%, #dc2626 100%
                                        @endif
                                    );"></div>
                                </div>
                                <div class="performance-text">{{ round($percentage, 1) }}%</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('student.exams.results', $attempt) }}" class="view-btn">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Detailed Results
                        </a>
                    </div>
                </div>
                @endforeach
            @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="empty-title">No Exam Marks Found</h3>
                <p class="empty-text">
                    You haven't completed any exams yet. Once you take exams and they are graded, your marks will appear here.
                </p>
                <a href="{{ route('student.exams.index') }}" class="empty-action">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    View Available Exams
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection