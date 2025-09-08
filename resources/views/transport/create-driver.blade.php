@extends('layouts.app')

@section('title', 'Add Driver')

@section('content')
<div class="card-premium">
    <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 2rem;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-user-plus me-3"></i>Add New Driver</h3>
            <a href="{{ route('transport.drivers.index') }}" class="btn-premium">
                <i class="fas fa-arrow-left me-2"></i> Back to Drivers
            </a>
        </div>
    </div>
    
    <div class="card-body p-4">
        <form action="{{ route('transport.drivers.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label font-semibold text-gray-700">Full Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="license_number" class="form-label font-semibold text-gray-700">License Number *</label>
                        <input type="text" class="form-control @error('license_number') is-invalid @enderror" 
                               id="license_number" name="license_number" value="{{ old('license_number') }}" required>
                        @error('license_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label font-semibold text-gray-700">Phone Number *</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone') }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label font-semibold text-gray-700">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="license_expiry" class="form-label font-semibold text-gray-700">License Expiry Date *</label>
                        <input type="date" class="form-control @error('license_expiry') is-invalid @enderror" 
                               id="license_expiry" name="license_expiry" value="{{ old('license_expiry') }}" required>
                        @error('license_expiry')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="experience_years" class="form-label font-semibold text-gray-700">Years of Experience *</label>
                        <input type="number" min="0" class="form-control @error('experience_years') is-invalid @enderror" 
                               id="experience_years" name="experience_years" value="{{ old('experience_years') }}" required>
                        @error('experience_years')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label font-semibold text-gray-700">Status *</label>
                        <select class="form-control @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="address" class="form-label font-semibold text-gray-700">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label font-semibold text-gray-700">Notes</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" 
                          id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('transport.drivers.index') }}" class="btn-premium" style="background: var(--secondary-gradient);">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
                <button type="submit" class="btn-premium">
                    <i class="fas fa-save me-2"></i> Add Driver
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
