@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.show', $user) }}">{{ $user->name }}</a></li>
                        <li class="breadcrumb-item active">Edit Profile</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Profile: {{ $user->name }}</h4>
            </div>
            </div>
        </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Profile Information</h5>
                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="mdi mdi-arrow-left"></i> Back to User
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Personal Information</h6>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                            </div>
                            
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                           placeholder="Enter phone number">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}">
                                    @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-control @error('gender') is-invalid @enderror" 
                                            id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3" 
                                              placeholder="Enter address">{{ old('address', $user->address) }}</textarea>
                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Account Information</h6>
                            
                                <div class="mb-3">
                                    <label for="role" class="form-label">User Role <span class="text-danger">*</span></label>
                                    <select class="form-control @error('role') is-invalid @enderror" 
                                            id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                        <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                        <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="finance" {{ old('role', $user->role) == 'finance' ? 'selected' : '' }}>Finance Officer</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                        </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">Account Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status">
                                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Profile Photo</label>
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                           id="photo" name="photo" accept="image/*">
                                    @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                    <small class="form-text text-muted">Max file size: 2MB. Supported formats: JPG, PNG, GIF</small>
                                </div>
                                
                                @if($user->photo)
                                <div class="mb-3">
                                    <label class="form-label">Current Photo</label>
                                    <div>
                                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Current Photo" 
                                             class="rounded-circle" style="width: 80px; height: 80px;">
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Role-specific Information -->
                        <div id="role-specific-info" style="display: none;">
                            <hr>
                            <h6 class="text-primary mb-3">Role-specific Information</h6>
                            
                            <!-- Teacher Information -->
                            <div id="teacher-info" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="employee_id" class="form-label">Employee ID</label>
                                            <input type="text" class="form-control @error('employee_id') is-invalid @enderror" 
                                                   id="employee_id" name="employee_id" value="{{ old('employee_id', $user->teacher->employee_id ?? '') }}">
                                            @error('employee_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="department" class="form-label">Department</label>
                                            <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                                   id="department" name="department" value="{{ old('department', $user->teacher->department ?? '') }}">
                                            @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Student Information -->
                            <div id="student-info" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="student_id" class="form-label">Student ID</label>
                                            <input type="text" class="form-control @error('student_id') is-invalid @enderror" 
                                                   id="student_id" name="student_id" value="{{ old('student_id', $user->student->student_id ?? '') }}">
                                            @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="class_id" class="form-label">Class</label>
                                            <select class="form-control @error('class_id') is-invalid @enderror" 
                                                    id="class_id" name="class_id">
                                                <option value="">Select Class</option>
                                                @foreach(\App\Models\ClassRoom::all() as $class)
                                                    <option value="{{ $class->id }}" {{ old('class_id', $user->student->class_id ?? '') == $class->id ? 'selected' : '' }}>
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Update User
                            </button>
                            <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">
                                <i class="mdi mdi-close"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('role').addEventListener('change', function() {
    const role = this.value;
    const roleSpecificInfo = document.getElementById('role-specific-info');
    const teacherInfo = document.getElementById('teacher-info');
    const studentInfo = document.getElementById('student-info');
    
    // Hide all role-specific sections
    roleSpecificInfo.style.display = 'none';
    teacherInfo.style.display = 'none';
    studentInfo.style.display = 'none';
    
    if (role === 'teacher') {
        roleSpecificInfo.style.display = 'block';
        teacherInfo.style.display = 'block';
    } else if (role === 'student') {
        roleSpecificInfo.style.display = 'block';
        studentInfo.style.display = 'block';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    if (roleSelect.value) {
        roleSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection