@extends('layouts.app')

@section('title', 'Create Transport Route')

@section('content')
<div class="card-premium">
    <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 2rem;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-plus me-3"></i>Create Transport Route</h3>
            <a href="{{ route('transport.routes.index') }}" class="btn-premium">
                <i class="fas fa-arrow-left me-2"></i> Back to Routes
            </a>
        </div>
    </div>
    
    <div class="card-body p-4">
        <form action="{{ route('transport.routes.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label font-semibold text-gray-700">Route Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="vehicle_id" class="form-label font-semibold text-gray-700">Vehicle *</label>
                        <select class="form-control @error('vehicle_id') is-invalid @enderror" 
                                id="vehicle_id" name="vehicle_id" required>
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->vehicle_number }} - {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="driver_id" class="form-label font-semibold text-gray-700">Driver *</label>
                        <select class="form-control @error('driver_id') is-invalid @enderror" 
                                id="driver_id" name="driver_id" required>
                            <option value="">Select Driver</option>
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
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="distance" class="form-label font-semibold text-gray-700">Distance (km) *</label>
                        <input type="number" step="0.1" class="form-control @error('distance') is-invalid @enderror" 
                               id="distance" name="distance" value="{{ old('distance') }}" required>
                        @error('distance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="start_location" class="form-label font-semibold text-gray-700">Start Location *</label>
                        <input type="text" class="form-control @error('start_location') is-invalid @enderror" 
                               id="start_location" name="start_location" value="{{ old('start_location') }}" required>
                        @error('start_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_location" class="form-label font-semibold text-gray-700">End Location *</label>
                        <input type="text" class="form-control @error('end_location') is-invalid @enderror" 
                               id="end_location" name="end_location" value="{{ old('end_location') }}" required>
                        @error('end_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="estimated_time" class="form-label font-semibold text-gray-700">Estimated Time *</label>
                        <input type="text" class="form-control @error('estimated_time') is-invalid @enderror" 
                               id="estimated_time" name="estimated_time" value="{{ old('estimated_time') }}" 
                               placeholder="e.g., 45 minutes" required>
                        @error('estimated_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fare" class="form-label font-semibold text-gray-700">Fare (USD) *</label>
                        <input type="number" step="0.01" class="form-control @error('fare') is-invalid @enderror" 
                               id="fare" name="fare" value="{{ old('fare') }}" required>
                        @error('fare')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label font-semibold text-gray-700">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label font-semibold text-gray-700" for="is_active">
                        Active Route
                    </label>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('transport.routes.index') }}" class="btn-premium" style="background: var(--secondary-gradient);">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
                <button type="submit" class="btn-premium">
                    <i class="fas fa-save me-2"></i> Create Route
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
