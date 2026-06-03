@extends('layouts.app')

@section('title', 'Edit Transport Assignment')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-bus me-2"></i>Edit Transport Assignment
        </h2>
        <a href="{{ route('transport.assignments.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Assignments
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('transport.assignments.update', $assignment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Driver/Conductor</label>
                            <input type="text" class="form-control" value="{{ $assignment->user->name ?? 'N/A' }}" disabled>
                            <small class="form-text text-muted">Driver/Conductor cannot be changed. Create a new assignment instead.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vehicle_id" class="form-label">Vehicle</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror">
                                    <option value="">Select Vehicle (Optional)</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $assignment->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->vehicle_number ?? $vehicle->registration_number ?? 'Vehicle #' . $vehicle->id }}
                                            @if($vehicle->vehicle_type)
                                                - {{ $vehicle->vehicle_type }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="route_id" class="form-label">Route</label>
                                <select name="route_id" id="route_id" class="form-select @error('route_id') is-invalid @enderror">
                                    <option value="">Select Route (Optional)</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id }}" {{ old('route_id', $assignment->route_id) == $route->id ? 'selected' : '' }}>
                                            {{ $route->route_name ?? 'Route #' . $route->id }}
                                            @if($route->route_code)
                                                ({{ $route->route_code }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('route_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="assigned_from" class="form-label">
                                    Assigned From <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="assigned_from" id="assigned_from" 
                                       class="form-control @error('assigned_from') is-invalid @enderror" 
                                       value="{{ old('assigned_from', $assignment->assigned_from->format('Y-m-d')) }}" required>
                                @error('assigned_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assigned_to" class="form-label">Assigned To</label>
                                <input type="date" name="assigned_to" id="assigned_to" 
                                       class="form-control @error('assigned_to') is-invalid @enderror" 
                                       value="{{ old('assigned_to', $assignment->assigned_to ? $assignment->assigned_to->format('Y-m-d') : '') }}">
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Leave empty for ongoing assignment.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       class="form-check-input" {{ old('is_active', $assignment->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">Active</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      placeholder="Any additional notes about this assignment...">{{ old('notes', $assignment->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('transport.assignments.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

