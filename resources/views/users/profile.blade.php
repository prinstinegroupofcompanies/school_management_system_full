@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<style>
.profile-container {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    min-height: 100vh;
    color: #e2e8f0;
}

.profile-header {
    background: rgba(30, 41, 59, 0.8);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    padding: 2rem 0;
}

.profile-content {
    padding: 2rem 0;
}

.section-title {
    color: #cbd5e1;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.card-premium {
    background: rgba(15, 23, 42, 0.5);
    border: 1px solid rgba(148, 163, 184, 0.15);
    border-radius: 1rem;
    padding: 1.25rem;
    box-shadow: 0 10px 25px rgba(2, 6, 23, 0.2);
}

.header-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
}

.user-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(148, 163, 184, 0.3);
    box-shadow: 0 8px 20px rgba(2, 6, 23, 0.25);
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
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 1.25rem;
}

.action-link {
    color: #0b1220;
    background: linear-gradient(135deg, #93c5fd 0%, #60a5fa 100%);
    text-decoration: none;
    padding: 0.55rem 1rem;
    border: 1px solid rgba(148, 163, 184, 0.25);
    border-radius: 0.5rem;
    transition: all 0.25s ease;
    font-size: 0.9rem;
    box-shadow: 0 6px 16px rgba(30, 64, 175, 0.25);
}

.action-link:hover {
    transform: translateY(-1px);
    filter: brightness(1.03);
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
            <div class="header-wrap container">
                <div class="user-meta">
                    @php($photo = $user->profile_photo ? asset('storage/' . $user->profile_photo) : null)
                    @if($photo)
                        <img class="avatar" src="{{ $photo }}" alt="Avatar">
                    @else
                        <div class="avatar" style="display:flex;align-items:center;justify-content:center;background:rgba(148,163,184,0.15);color:#e2e8f0;">
                            {{ strtoupper(substr($user->name,0,1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="user-name" style="margin:0;">{{ $user->name }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                        <div class="user-role">{{ ucfirst($user->user_type) }}</div>
                    </div>
                </div>
                <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                    <a href="{{ auth()->id() === $user->id && auth()->user()->user_type !== 'admin' ? route('me.profile') : route('users.profile', $user->id) }}" class="action-link">My Profile</a>
                    <a href="{{ auth()->id() === $user->id && auth()->user()->user_type !== 'admin' ? route('me.profile.edit') : route('users.profile.edit', $user->id) }}" class="action-link">Edit Profile</a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="profile-content">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <!-- Personal Information -->
                    <div class="mb-5 card-premium">
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
                        @if($user->city)
                        <div class="info-item">
                            <span class="info-label">City</span>
                            <span class="info-value">{{ $user->city }}</span>
                        </div>
                        @endif
                        @if($user->country)
                        <div class="info-item">
                            <span class="info-label">Country</span>
                            <span class="info-value">{{ $user->country }}</span>
                        </div>
                        @endif
                        @if($user->teacher)
                        <div class="info-item">
                            <span class="info-label">Employee ID</span>
                            <span class="info-value">{{ $user->teacher->employee_id ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Department</span>
                            <span class="info-value">{{ $user->teacher->department->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Qualification</span>
                            <span class="info-value">{{ $user->teacher->qualification ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Experience</span>
                            <span class="info-value">{{ $user->teacher->experience ?? 'N/A' }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Account Information -->
                    <div class="mb-5 card-premium">
                        <h3 class="section-title">Account Information</h3>
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="status-badge">{{ ucfirst($user->status) }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">User Type</span>
                            <span class="info-value">{{ ucfirst($user->user_type) }}</span>
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
                    <div class="mb-5 card-premium" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                        <h3 class="section-title">Quick Actions</h3>
                        <div class="quick-actions">
                            <a href="{{ auth()->id() === $user->id && auth()->user()->user_type !== 'admin' ? route('me.profile.edit') : route('users.profile.edit', $user->id) }}" class="action-link">Edit Profile</a>
                            <a href="{{ route('users.change-password', $user->id) }}" class="action-link">Change Password</a>
                            <a href="{{ route('dashboard') }}" class="action-link">Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection