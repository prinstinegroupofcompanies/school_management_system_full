@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">User Information</h5>
                    <div>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning me-2">
                            <i class="mdi mdi-pencil"></i> Edit User
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-4">
                            <!-- User Photo -->
                            <div class="text-center mb-4">
                                @if($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}" 
                                         alt="{{ $user->name }}" class="rounded-circle mb-3" 
                                         style="width: 150px; height: 150px;">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                         style="width: 150px; height: 150px;">
                                        <i class="mdi mdi-account mdi-48px text-muted"></i>
                                    </div>
                                @endif
                                <h4 class="mb-1">{{ $user->name }}</h4>
                                <p class="text-muted">{{ $user->email }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Basic Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Full Name:</strong></td>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $user->phone ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Role:</strong></td>
                                            <td>
                                                @if($user->role == 'admin')
                                                    <span class="badge bg-danger">Admin</span>
                                                @elseif($user->role == 'teacher')
                                                    <span class="badge bg-warning">Teacher</span>
                                                @elseif($user->role == 'student')
                                                    <span class="badge bg-info">Student</span>
                                                @elseif($user->role == 'finance')
                                                    <span class="badge bg-success">Finance</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($user->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($user->status == 'inactive')
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @elseif($user->status == 'suspended')
                                                    <span class="badge bg-danger">Suspended</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($user->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Additional Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Additional Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Date of Birth:</strong></td>
                                            <td>{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('M d, Y') : 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Gender:</strong></td>
                                            <td>{{ $user->gender ? ucfirst($user->gender) : 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address:</strong></td>
                                            <td>{{ $user->address ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Login:</strong></td>
                                            <td>{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('M d, Y H:i') : 'Never' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Role-specific Information -->
                            @if($user->student)
                            <div class="mt-4">
                                <h5 class="mb-3">Student Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Student ID:</strong></td>
                                        <td>{{ $user->student->student_id ?? 'Not assigned' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Class:</strong></td>
                                        <td>{{ $user->student->classRoom->name ?? 'Not assigned' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if($user->student->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($user->student->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @elseif($user->teacher)
                            <div class="mt-4">
                                <h5 class="mb-3">Teacher Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Employee ID:</strong></td>
                                        <td>{{ $user->teacher->employee_id ?? 'Not assigned' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Department:</strong></td>
                                        <td>{{ $user->teacher->department ?? 'Not assigned' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if($user->teacher->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($user->teacher->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
