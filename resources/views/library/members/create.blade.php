@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('library.index') }}">Library</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('library.members') }}">Members</a></li>
                        <li class="breadcrumb-item active">Add New Member</li>
                    </ol>
                </div>
                <h4 class="page-title">Add New Member</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Member Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('library.members.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Select User <span class="text-danger">*</span></label>
                                    <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                        <option value="">Select User</option>
                                        @foreach(\App\Models\User::where('user_type', 'student')->get() as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="member_id" class="form-label">Member ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('member_id') is-invalid @enderror" 
                                           id="member_id" name="member_id" value="{{ old('member_id', 'LIB' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)) }}" required>
                                    @error('member_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address') }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="membership_type" class="form-label">Membership Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('membership_type') is-invalid @enderror" id="membership_type" name="membership_type" required>
                                        <option value="">Select Type</option>
                                        <option value="student" {{ old('membership_type') == 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="staff" {{ old('membership_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="faculty" {{ old('membership_type') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                                        <option value="external" {{ old('membership_type') == 'external' ? 'selected' : '' }}>External</option>
                                    </select>
                                    @error('membership_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" 
                                           id="expiry_date" name="expiry_date" value="{{ old('expiry_date', date('Y-m-d', strtotime('+1 year'))) }}" required>
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_books" class="form-label">Maximum Books Allowed</label>
                                    <input type="number" class="form-control @error('max_books') is-invalid @enderror" 
                                           id="max_books" name="max_books" value="{{ old('max_books', 5) }}" min="1" max="20">
                                    @error('max_books')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fine_balance" class="form-label">Fine Balance</label>
                                    <input type="number" step="0.01" class="form-control @error('fine_balance') is-invalid @enderror" 
                                           id="fine_balance" name="fine_balance" value="{{ old('fine_balance', 0) }}" min="0">
                                    @error('fine_balance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Member
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('library.members') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Member</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
