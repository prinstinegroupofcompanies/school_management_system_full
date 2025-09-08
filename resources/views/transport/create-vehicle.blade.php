@extends('layouts.app')

@section('title', 'Add Vehicle')

@section('content')
<div class="card-premium">
    <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 2rem;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-bus me-3"></i>Add New Vehicle</h3>
            <a href="{{ route('transport.vehicles.index') }}" class="btn-premium">
                <i class="fas fa-arrow-left me-2"></i> Back to Vehicles
            </a>
        </div>
    </div>
    
    <div class="card-body p-4">
        <form action="{{ route('transport.vehicles.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="vehicle_number" class="form-label font-semibold text-gray-700">Vehicle Number *</label>
                        <input type="text" class="form-control @error('vehicle_number') is-invalid @enderror" 
                               id="vehicle_number" name="vehicle_number" value="{{ old('vehicle_number') }}" required>
                        @error('vehicle_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="registration_number" class="form-label font-semibold text-gray-700">Registration Number *</label>
                        <input type="text" class="form-control @error('registration_number') is-invalid @enderror" 
                               id="registration_number" name="registration_number" value="{{ old('registration_number') }}" required>
                        @error('registration_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="make" class="form-label font-semibold text-gray-700">Make *</label>
                        <input type="text" class="form-control @error('make') is-invalid @enderror" 
                               id="make" name="make" value="{{ old('make') }}" required>
                        @error('make')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="model" class="form-label font-semibold text-gray-700">Model *</label>
                        <input type="text" class="form-control @error('model') is-invalid @enderror" 
                               id="model" name="model" value="{{ old('model') }}" required>
                        @error('model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="year" class="form-label font-semibold text-gray-700">Year *</label>
                        <input type="number" min="1900" max="{{ date('Y') + 1 }}" 
                               class="form-control @error('year') is-invalid @enderror" 
                               id="year" name="year" value="{{ old('year') }}" required>
                        @error('year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="capacity" class="form-label font-semibold text-gray-700">Seating Capacity *</label>
                        <input type="number" min="1" class="form-control @error('capacity') is-invalid @enderror" 
                               id="capacity" name="capacity" value="{{ old('capacity') }}" required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="color" class="form-label font-semibold text-gray-700">Color</label>
                        <input type="text" class="form-control @error('color') is-invalid @enderror" 
                               id="color" name="color" value="{{ old('color') }}">
                        @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="driver_id" class="form-label font-semibold text-gray-700">Driver</label>
                        <select class="form-control @error('driver_id') is-invalid @enderror" 
                                id="driver_id" name="driver_id">
                            <option value="">Select Driver (Optional)</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name }} - {{ $driver->license_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('driver_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="insurance_expiry" class="form-label font-semibold text-gray-700">Insurance Expiry *</label>
                        <input type="date" class="form-control @error('insurance_expiry') is-invalid @enderror" 
                               id="insurance_expiry" name="insurance_expiry" value="{{ old('insurance_expiry') }}" required>
                        @error('insurance_expiry')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="fitness_expiry" class="form-label font-semibold text-gray-700">Fitness Expiry *</label>
                        <input type="date" class="form-control @error('fitness_expiry') is-invalid @enderror" 
                               id="fitness_expiry" name="fitness_expiry" value="{{ old('fitness_expiry') }}" required>
                        @error('fitness_expiry')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="permit_expiry" class="form-label font-semibold text-gray-700">Permit Expiry *</label>
                        <input type="date" class="form-control @error('permit_expiry') is-invalid @enderror" 
                               id="permit_expiry" name="permit_expiry" value="{{ old('permit_expiry') }}" required>
                        @error('permit_expiry')
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
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="notes" class="form-label font-semibold text-gray-700">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('transport.vehicles.index') }}" class="btn-premium" style="background: var(--secondary-gradient);">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
                <button type="submit" class="btn-premium">
                    <i class="fas fa-save me-2"></i> Add Vehicle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
