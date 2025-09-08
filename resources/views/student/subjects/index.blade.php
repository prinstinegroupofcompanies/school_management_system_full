@extends('layouts.app')

@section('title', 'My Subjects')

@section('content')
<style>
.subjects-container {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    min-height: 100vh;
    color: #e2e8f0;
    padding: 0;
    margin: 0;
}

.subjects-header {
    background: rgba(30, 41, 59, 0.9);
    backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    padding: 2rem 0;
    margin: 0;
}

.subjects-content {
    padding: 2rem 0;
    max-width: 1200px;
    margin: 0 auto;
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

.subjects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.subject-card {
    background: rgba(30, 41, 59, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 1rem;
    padding: 2rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.subject-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
    border-color: rgba(148, 163, 184, 0.4);
}

.subject-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.subject-icon {
    width: 3rem;
    height: 3rem;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.subject-icon svg {
    width: 1.5rem;
    height: 1.5rem;
    color: white;
}

.subject-title {
    color: #e2e8f0;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.subject-info {
    margin-bottom: 1.5rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    color: #94a3b8;
    font-weight: 500;
    font-size: 0.9rem;
}

.info-value {
    color: #e2e8f0;
    font-weight: 600;
    font-size: 0.95rem;
}

.subject-description {
    color: #94a3b8;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
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
    width: 100%;
    justify-content: center;
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
    width: 4rem;
    height: 4rem;
    background: rgba(148, 163, 184, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}

.empty-icon svg {
    width: 2rem;
    height: 2rem;
    color: #94a3b8;
}

.empty-title {
    color: #e2e8f0;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empty-text {
    color: #94a3b8;
    font-size: 1rem;
    margin-bottom: 0;
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
    margin-bottom: 2rem;
}

.back-btn:hover {
    background: rgba(59, 130, 246, 0.3);
    transform: translateY(-1px);
    color: #3b82f6;
    text-decoration: none;
}
</style>

<div class="subjects-container">
    <!-- Header -->
    <div class="subjects-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="page-title">My Subjects</h1>
                    <p class="page-subtitle">View all your enrolled subjects and course information</p>
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
    <div class="subjects-content">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($subjects->count() > 0)
            <div class="subjects-grid">
                @foreach($subjects as $subject)
                <div class="subject-card">
                    <div class="subject-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    
                    <h3 class="subject-title">{{ $subject->name }}</h3>
                    
                    <div class="subject-info">
                        <div class="info-row">
                            <span class="info-label">Subject Code</span>
                            <span class="info-value">{{ $subject->code }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Teacher</span>
                            <span class="info-value">{{ $subject->teacher->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Class</span>
                            <span class="info-value">{{ $subject->class->name ?? 'N/A' }}</span>
                        </div>
                        @if($subject->credits)
                        <div class="info-row">
                            <span class="info-label">Credits</span>
                            <span class="info-value">{{ $subject->credits }}</span>
                        </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subject->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </span>
                        </div>
                    </div>

                    @if($subject->description)
                    <p class="subject-description">{{ $subject->description }}</p>
                    @endif

                    <a href="{{ route('student.subjects.show', $subject) }}" class="view-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Details
                    </a>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="empty-title">No Subjects Found</h3>
                <p class="empty-text">No subjects have been assigned to your class yet. Please contact your administrator.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection