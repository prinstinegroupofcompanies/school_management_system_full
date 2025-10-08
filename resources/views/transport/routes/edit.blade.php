@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('transport.index') }}">Transport</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('transport.routes') }}">Routes</a></li>
                        <li class="breadcrumb-item active">Edit Route</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Route</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Route Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('transport.routes.update', $route) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="route_name" class="form-label">Route Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('route_name') is-invalid @enderror" 
                                           id="route_name" name="route_name" value="{{ old('route_name', $route->route_name) }}" required>
                                    @error('route_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="route_code" class="form-label">Route Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('route_code') is-invalid @enderror" 
                                           id="route_code" name="route_code" value="{{ old('route_code', $route->route_code) }}" required>
                                    @error('route_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_location" class="form-label">Start Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('start_location') is-invalid @enderror" 
                                           id="start_location" name="start_location" value="{{ old('start_location', $route->start_location) }}" required>
                                    @error('start_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_location" class="form-label">End Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('end_location') is-invalid @enderror" 
                                           id="end_location" name="end_location" value="{{ old('end_location', $route->end_location) }}" required>
                                    @error('end_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="distance_km" class="form-label">Distance (KM) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" class="form-control @error('distance_km') is-invalid @enderror" 
                                           id="distance_km" name="distance_km" value="{{ old('distance_km', $route->distance_km) }}" required>
                                    @error('distance_km')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fare_amount" class="form-label">Fare Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('fare_amount') is-invalid @enderror" 
                                           id="fare_amount" name="fare_amount" value="{{ old('fare_amount', $route->fare_amount) }}" required>
                                    @error('fare_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                    <select class="form-control @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                        <option value="">Select Currency</option>
                                        <option value="USD" {{ old('currency', $route->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="LRD" {{ old('currency', $route->currency) == 'LRD' ? 'selected' : '' }}>LRD</option>
                                        <option value="EUR" {{ old('currency', $route->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_duration" class="form-label">Estimated Duration (minutes)</label>
                                    <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                           id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $route->estimated_duration) }}">
                                    @error('estimated_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="active" {{ old('status', $route->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $route->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $route->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('transport.routes') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Route</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
