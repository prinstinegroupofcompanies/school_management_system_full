@extends('layouts.app')

@section('title', 'Subject Details')

@section('content')
<style>
.subject-container {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    min-height: 100vh;
    color: #e2e8f0;
    padding: 0;
    margin: 0;
}

.subject-header {
    background: rgba(30, 41, 59, 0.9);
    backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    padding: 2rem 0;
    margin: 0;
}

.subject-content {
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

.subject-title {
    color: #e2e8f0;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.subject-subtitle {
    color: #94a3b8;
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.info-card {
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 1rem;
    padding: 2rem;
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    border-color: rgba(148, 163, 184, 0.3);
}

.info-title {
    color: #cbd5e1;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    color: #94a3b8;
    font-weight: 500;
    font-size: 0.95rem;
}

.info-value {
    color: #e2e8f0;
    font-weight: 600;
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

.status-active {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.status-inactive {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.actions-card {
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 1rem;
    padding: 2rem;
    height: fit-content;
}

.actions-title {
    color: #cbd5e1;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    width: 100%;
    padding: 1rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
    border: none;
    cursor: pointer;
}

.action-btn:last-child {
    margin-bottom: 0;
}

.action-btn:hover {
    transform: translateY(-2px);
    text-decoration: none;
}

.action-btn-exams {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.action-btn-exams:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    color: white;
}

.action-btn-grades {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.action-btn-grades:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    color: white;
}

.action-btn-notifications {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}

.action-btn-notifications:hover {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
    color: white;
}

.icon {
    width: 1.25rem;
    height: 1.25rem;
}

.subject-icon {
    width: 3rem;
    height: 3rem;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.subject-icon svg {
    width: 1.5rem;
    height: 1.5rem;
    color: white;
}

.description-card {
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 1rem;
    padding: 2rem;
    margin-top: 2rem;
}

.description-text {
    color: #e2e8f0;
    line-height: 1.6;
    font-size: 1rem;
}
</style>

<div class="subject-container">
    <!-- Header -->
    <div class="subject-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="subject-title">{{ $subject->name }}</h1>
                    <p class="subject-subtitle">Subject Details & Information</p>
                </div>
                <a href="{{ route('student.subjects.index') }}" class="back-btn">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Subjects
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="subject-content">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Information -->
                <div class="lg:col-span-2">
                    <div class="info-grid">
                        <!-- Basic Information -->
                        <div class="info-card">
                            <div class="subject-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h3 class="info-title">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Basic Information
                            </h3>
                            <div class="info-item">
                                <span class="info-label">Subject Name</span>
                                <span class="info-value">{{ $subject->name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Subject Code</span>
                                <span class="info-value">{{ $subject->code }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Class</span>
                                <span class="info-value">{{ $subject->class->name ?? 'N/A' }}</span>
                            </div>
                            @if($subject->credits)
                            <div class="info-item">
                                <span class="info-label">Credits</span>
                                <span class="info-value">{{ $subject->credits }}</span>
                            </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label">Status</span>
                                <span class="status-badge status-{{ $subject->is_active ? 'active' : 'inactive' }}">
                                    {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        <!-- Teacher Information -->
                        <div class="info-card">
                            <h3 class="info-title">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Teacher Information
                            </h3>
                            <div class="info-item">
                                <span class="info-label">Assigned Teacher</span>
                                <span class="info-value">{{ $subject->teacher->name ?? 'Not assigned' }}</span>
                            </div>
                            @if($subject->teacher)
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $subject->teacher->email ?? 'N/A' }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    @if($subject->description)
                    <div class="description-card">
                        <h3 class="info-title">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Subject Description
                        </h3>
                        <p class="description-text">{{ $subject->description }}</p>
                    </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="actions-card">
                    <h3 class="actions-title">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Quick Actions
                    </h3>
                    
                    <a href="{{ route('student.exams.index') }}" class="action-btn action-btn-exams">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View Exams
                    </a>
                    
                    <a href="{{ route('student.grades.grade-sheet', ['year' => date('Y'), 'semester' => 1]) }}" class="action-btn action-btn-grades">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        View Grades
                    </a>
                    
                    <a href="{{ route('notifications.index') }}" class="action-btn action-btn-notifications">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-6H4v6zm0-8h6V5H4v6zm8-8h6v6h-6V3z"></path>
                        </svg>
                        Notifications
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection