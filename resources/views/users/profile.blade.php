@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
                <h4 class="page-title">My Profile</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update-profile') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">User Type</label>
                                    <input type="text" class="form-control" value="{{ ucfirst($user->user_type) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <input type="text" class="form-control" 
                                           value="{{ $user->is_active ? 'Active' : 'Inactive' }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save me-1"></i> Update Profile
                                    </button>
                                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i> Back to Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-primary text-primary rounded-circle font-weight-medium" style="font-size: 2rem;">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    </div>
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                    <p class="text-muted">
                        <span class="badge bg-{{ $user->user_type == 'admin' ? 'danger' : ($user->user_type == 'teacher' ? 'warning' : ($user->user_type == 'student' ? 'info' : 'secondary')) }}">
                            {{ ucfirst($user->user_type) }}
                        </span>
                    </p>
                    <p class="text-muted">
                        <small>Member since {{ $user->created_at->format('M Y') }}</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection