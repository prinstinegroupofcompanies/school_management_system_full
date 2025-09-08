@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<style>
.edit-profile-container {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    min-height: 100vh;
    color: #e2e8f0;
}

.edit-profile-header {
    background: rgba(30, 41, 59, 0.8);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    padding: 2rem 0;
}

.edit-profile-content {
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
    background: linear-gradient(135deg, #93c5fd 0%, #3b82f6 100%);
    border: none;
    border-radius: 0.5rem;
    color: white;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    filter: brightness(1.05);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(30, 64, 175, 0.35);
}

.btn-secondary {
    background: rgba(148, 163, 184, 0.15);
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

.current-photo {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(148, 163, 184, 0.3);
}

.photo-placeholder {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(30, 41, 59, 0.5);
    border: 2px solid rgba(148, 163, 184, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
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
            <div class="d-flex justify-content-between align-items-center" style="gap:1rem;">
                <div>
                    <a href="{{ auth()->id() === $user->id && auth()->user()->user_type !== 'admin' ? route('me.profile') : route('users.profile', $user->id) }}" class="nav-link">My Profile</a>
                    <a href="{{ auth()->id() === $user->id && auth()->user()->user_type !== 'admin' ? route('me.profile.edit') : route('users.profile.edit', $user->id) }}" class="nav-link active">Edit Profile</a>
                </div>
                <div class="user-summary">
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-email">{{ $user->email }}</div>
                    <div class="user-role">{{ ucfirst($user->user_type) }}</div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="edit-profile-content">
            <form action="{{ auth()->id() === $user->id && auth()->user()->user_type !== 'admin' ? route('me.profile.update') : route('users.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-lg-10 col-xl-9">
                        <!-- Personal Information -->
                        <div class="mb-5 card-premium">
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
                            
                            <div class="form-group">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city', $user->city) }}" 
                                       placeholder="Enter your city">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                       id="country" name="country" value="{{ old('country', $user->country) }}" 
                                       placeholder="Enter your country">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Profile Photo -->
                        <div class="mb-5 card-premium">
                            <h3 class="section-title">Profile Photo</h3>
                            
                            <div class="form-group">
                                <label class="form-label">Current Photo</label>
                                <div class="mb-3">
                                    @if($user->profile_photo)
                                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Current Photo" 
                                             class="current-photo">
                                    @else
                                        <div class="photo-placeholder">
                                            <i class="fas fa-user fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <label for="profile_photo" class="form-label">Upload New Photo</label>
                                <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" 
                                       id="profile_photo" name="profile_photo" accept="image/*">
                                @error('profile_photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">Upload a profile photo (JPG, PNG, GIF - Max 2MB)</small>
                            </div>
                        </div>

                        @if($user->teacher)
                        <!-- Professional Information -->
                        <div class="mb-5">
                            <h3 class="section-title">Professional Information</h3>
                            
                            <div class="form-group">
                                <label for="qualification" class="form-label">Qualification</label>
                                <textarea class="form-control @error('qualification') is-invalid @enderror" 
                                          id="qualification" name="qualification" rows="3" 
                                          placeholder="Enter your qualifications">{{ old('qualification', $user->teacher->qualification) }}</textarea>
                                @error('qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="experience" class="form-label">Experience</label>
                                <textarea class="form-control @error('experience') is-invalid @enderror" 
                                          id="experience" name="experience" rows="3" 
                                          placeholder="Enter your experience">{{ old('experience', $user->teacher->experience) }}</textarea>
                                @error('experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" 
                                          id="bio" name="bio" rows="4" 
                                          placeholder="Tell us about yourself">{{ old('bio', $user->teacher->bio) }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="form-group" style="display:flex;gap:1rem;align-items:center;">
                            <button type="submit" class="btn btn-primary me-3">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                            <a href="{{ route('users.profile', $user->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection