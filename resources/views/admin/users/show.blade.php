@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User Management</a></li>
                        <li class="breadcrumb-item active">User Details</li>
                    </ol>
                </div>
                <h4 class="page-title">User Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">{{ $user->name }}</h5>
                            <p class="text-muted">{{ $user->email }}</p>
                            
                            <div class="row mt-4">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">User Type:</label>
                                        <span class="badge bg-{{ $user->user_type == 'admin' ? 'danger' : ($user->user_type == 'teacher' ? 'warning' : ($user->user_type == 'student' ? 'info' : 'secondary')) }}">
                                            {{ ucfirst($user->user_type) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status:</label>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Created:</label>
                                        <p class="text-muted">{{ $user->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Last Updated:</label>
                                        <p class="text-muted">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($user->student)
                            <div class="mt-4">
                                <h6 class="text-primary">Student Information</h6>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><strong>Student ID:</strong> {{ $user->student->student_id ?? 'N/A' }}</p>
                                        <p><strong>Admission Number:</strong> {{ $user->student->admission_no ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><strong>Class:</strong> {{ $user->student->classRoom->name ?? 'N/A' }}</p>
                                        <p><strong>Status:</strong> {{ ucfirst($user->student->status ?? 'N/A') }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($user->teacher)
                            <div class="mt-4">
                                <h6 class="text-primary">Teacher Information</h6>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><strong>Employee ID:</strong> {{ $user->teacher->employee_id ?? 'N/A' }}</p>
                                        <p><strong>Department:</strong> {{ $user->teacher->department ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><strong>Qualification:</strong> {{ $user->teacher->qualification ?? 'N/A' }}</p>
                                        <p><strong>Status:</strong> {{ ucfirst($user->teacher->status ?? 'N/A') }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($user->staff)
                            <div class="mt-4">
                                <h6 class="text-primary">Staff Information</h6>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><strong>Employee ID:</strong> {{ $user->staff->employee_id ?? 'N/A' }}</p>
                                        <p><strong>Department:</strong> {{ $user->staff->department ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><strong>Position:</strong> {{ $user->staff->position ?? 'N/A' }}</p>
                                        <p><strong>Status:</strong> {{ ucfirst($user->staff->status ?? 'N/A') }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-primary text-primary rounded-circle font-weight-medium" style="font-size: 2rem;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                </div>
                                <h5>{{ $user->name }}</h5>
                                <p class="text-muted">{{ $user->email }}</p>
                                
                                <div class="mt-4">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary me-2">
                                        <i class="mdi mdi-pencil me-1"></i> Edit User
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i> Back to Users
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
