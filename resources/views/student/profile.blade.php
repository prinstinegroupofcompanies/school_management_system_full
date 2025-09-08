@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<style>
.profile-container {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    min-height: 100vh;
    color: #e2e8f0;
    padding: 0;
    margin: 0;
}

.profile-header {
    background: rgba(30, 41, 59, 0.9);
    backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    padding: 2rem 0;
    margin: 0;
}

.profile-content {
    padding: 2rem 0;
    max-width: 1200px;
    margin: 0 auto;
}

.section-title {
    color: #cbd5e1;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
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
    font-size: 0.9rem;
}

.info-value {
    color: #e2e8f0;
    font-weight: 400;
}

.status-badge {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 500;
}

.quick-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 1.5rem;
}

.action-link {
    color: #e2e8f0;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border: 1px solid rgba(148, 163, 184, 0.3);
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.action-link:hover {
    background: rgba(148, 163, 184, 0.1);
    border-color: rgba(148, 163, 184, 0.5);
    color: #f1f5f9;
    text-decoration: none;
}

.nav-link {
    color: #94a3b8;
    text-decoration: none;
    margin-right: 2rem;
    transition: color 0.3s ease;
}

.nav-link.active {
    color: #e2e8f0;
    font-weight: 500;
}

.nav-link:hover {
    color: #e2e8f0;
    text-decoration: none;
}

.user-summary {
    text-align: right;
}

.user-name {
    font-size: 1.5rem;
    font-weight: 600;
    color: #e2e8f0;
    margin-bottom: 0.25rem;
}

.user-email {
    color: #94a3b8;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.user-role {
    color: #22c55e;
    font-size: 0.8rem;
    font-weight: 500;
}

.profile-photo-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 1rem;
}

.profile-photo {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(59, 130, 246, 0.3);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
}

.profile-photo:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
}

.profile-photo-placeholder {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: rgba(148, 163, 184, 0.2);
    border: 4px solid rgba(148, 163, 184, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.profile-photo-placeholder:hover {
    background: rgba(148, 163, 184, 0.3);
    border-color: rgba(148, 163, 184, 0.5);
}

.profile-photo-icon {
    width: 60px;
    height: 60px;
    color: #94a3b8;
}

@media (max-width: 768px) {
    .quick-actions {
        flex-direction: column;
    }
    
    .action-link {
        text-align: center;
    }
    
    .user-summary {
        text-align: left;
        margin-top: 1rem;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
}
</style>

<div class="profile-container">
    <div class="container">
        <!-- Header -->
        <div class="profile-header">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <a href="{{ route('student.profile') }}" class="nav-link active">My Profile</a>
                        <a href="{{ route('student.profile.edit') }}" class="nav-link">Edit Profile</a>
                    </div>
                    <div class="user-summary">
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                        <div class="user-role">Student</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="profile-content">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                    <!-- Profile Photo -->
                    <div class="mb-5">
                        <h3 class="section-title">Profile Photo</h3>
                        <div class="profile-photo-container">
                            @if($user->profile_photo)
                                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile Photo" class="profile-photo">
                            @else
                                <div class="profile-photo-placeholder">
                                    <svg class="profile-photo-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Personal Information -->
                    <div class="mb-5">
                        <h3 class="section-title">Personal Information</h3>
                        <div class="info-item">
                            <span class="info-label">Full Name</span>
                            <span class="info-value">{{ $user->name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $user->email }}</span>
                        </div>
                        @if($user->phone)
                        <div class="info-item">
                            <span class="info-label">Phone</span>
                            <span class="info-value">{{ $user->phone }}</span>
                        </div>
                        @endif
                        @if($user->address)
                        <div class="info-item">
                            <span class="info-label">Address</span>
                            <span class="info-value">{{ $user->address }}</span>
                        </div>
                        @endif
                        @if($user->student)
                        <div class="info-item">
                            <span class="info-label">Admission No</span>
                            <span class="info-value">{{ $user->student->admission_no }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Student ID</span>
                            <span class="info-value">{{ $user->student->student_id }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Class</span>
                            <span class="info-value">{{ $user->student->class->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Section</span>
                            <span class="info-value">{{ $user->student->section->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Academic Year</span>
                            <span class="info-value">{{ $user->student->academic_year }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Account Information -->
                    <div class="mb-5">
                        <h3 class="section-title">Account Information</h3>
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="status-badge">{{ ucfirst($user->status) }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Member Since</span>
                            <span class="info-value">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($user->last_login_at)
                        <div class="info-item">
                            <span class="info-label">Last Login</span>
                            <span class="info-value">{{ $user->last_login_at->format('M d, Y H:i') }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="mb-5">
                        <h3 class="section-title">Quick Actions</h3>
                        <div class="quick-actions">
                            <a href="{{ route('student.profile.edit') }}" class="action-link">Edit Profile</a>
                            <a href="{{ route('student.change-password') }}" class="action-link">Change Password</a>
                            <a href="{{ route('student.subjects.index') }}" class="action-link">My Subjects</a>
                            <a href="{{ route('student.exams.index') }}" class="action-link">My Exams</a>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
