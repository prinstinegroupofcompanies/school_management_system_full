@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<style>
.edit-profile-container {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    min-height: 100vh;
    color: #e2e8f0;
    padding: 0;
    margin: 0;
}

.edit-profile-header {
    background: rgba(30, 41, 59, 0.9);
    backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    padding: 2rem 0;
    margin: 0;
}

.edit-profile-content {
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

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    color: #94a3b8;
    font-weight: 500;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    display: block;
}

.form-control {
    background: rgba(30, 41, 59, 0.5);
    border: 1px solid rgba(148, 163, 184, 0.3);
    border-radius: 0.5rem;
    color: #e2e8f0;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    background: rgba(30, 41, 59, 0.8);
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    color: #e2e8f0;
}

.form-control:disabled {
    background: rgba(30, 41, 59, 0.3);
    border-color: rgba(148, 163, 184, 0.2);
    color: #94a3b8;
}

.form-control::placeholder {
    color: #64748b;
}

.invalid-feedback {
    color: #ef4444;
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.form-text {
    color: #64748b;
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border: none;
    border-radius: 0.5rem;
    color: white;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
    background: rgba(148, 163, 184, 0.2);
    border: 1px solid rgba(148, 163, 184, 0.3);
    border-radius: 0.5rem;
    color: #e2e8f0;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-secondary:hover {
    background: rgba(148, 163, 184, 0.3);
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

.current-photo-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.current-photo {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(59, 130, 246, 0.3);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.photo-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(148, 163, 184, 0.2);
    border: 3px solid rgba(148, 163, 184, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
}

.photo-placeholder-icon {
    width: 48px;
    height: 48px;
    color: #94a3b8;
}

.upload-section {
    text-align: center;
}

.upload-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.3);
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.upload-label:hover {
    background: rgba(59, 130, 246, 0.3);
    transform: translateY(-1px);
}

.upload-icon {
    width: 1.25rem;
    height: 1.25rem;
}

.upload-text {
    font-size: 0.95rem;
}

@media (max-width: 768px) {
    .user-summary {
        text-align: left;
        margin-top: 1rem;
    }
}
</style>

<div class="edit-profile-container">
    <div class="container">
        <!-- Header -->
        <div class="edit-profile-header">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <a href="{{ route('student.profile') }}" class="nav-link">My Profile</a>
                        <a href="{{ route('student.profile.edit') }}" class="nav-link active">Edit Profile</a>
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
        <div class="edit-profile-content">
            <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Personal Information -->
                        <div class="mb-5">
                            <h3 class="section-title">Personal Information</h3>
                            
                            <div class="form-group">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="{{ $user->email }}" disabled>
                                <small class="form-text">Email cannot be changed</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                       placeholder="Enter your phone number">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3" 
                                          placeholder="Enter your address">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Profile Photo -->
                        <div class="mb-5">
                            <h3 class="section-title">Profile Photo</h3>
                            
                            <div class="form-group">
                                <label class="form-label">Current Photo</label>
                                <div class="current-photo-container mb-4">
                                    @if($user->profile_photo)
                                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Current Photo" 
                                             class="current-photo" id="current-photo-preview">
                                    @else
                                        <div class="photo-placeholder" id="current-photo-preview">
                                            <svg class="photo-placeholder-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="upload-section">
                                    <label for="profile_photo" class="upload-label">
                                        <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <span class="upload-text">Choose New Photo</span>
                                    </label>
                                    <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" 
                                           id="profile_photo" name="profile_photo" accept="image/*" style="display: none;">
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text">Upload a profile photo (JPG, PNG, GIF - Max 2MB)</small>
                                </div>
                            </div>
                        </div>

                        @if($user->student)
                        <!-- Academic Information (Read-only) -->
                        <div class="mb-5">
                            <h3 class="section-title">Academic Information</h3>
                            <small class="form-text mb-3">The following information cannot be modified</small>
                            
                            <div class="form-group">
                                <label class="form-label">Admission Number</label>
                                <input type="text" class="form-control" value="{{ $user->student->admission_no }}" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Student ID</label>
                                <input type="text" class="form-control" value="{{ $user->student->student_id }}" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Class</label>
                                <input type="text" class="form-control" value="{{ $user->student->class->name ?? 'N/A' }}" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Academic Year</label>
                                <input type="text" class="form-control" value="{{ $user->student->academic_year }}" disabled>
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary me-3">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                            <a href="{{ route('student.profile') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile_photo');
    const preview = document.getElementById('current-photo-preview');
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Create new image element
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Preview';
                img.className = 'current-photo';
                
                // Replace the preview
                preview.parentNode.replaceChild(img, preview);
                img.id = 'current-photo-preview';
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection
