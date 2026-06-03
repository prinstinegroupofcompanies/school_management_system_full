@extends('layouts.app')

@section('title', 'Driver Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-tachometer-alt me-2"></i>Driver Dashboard
            </h2>
            <p class="text-muted">Welcome, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    @if($currentAssignment)
    <!-- Current Assignment Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Current Assignment
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-muted">Vehicle</h6>
                            <p class="mb-0">
                                <strong>
                                    {{ $currentAssignment->vehicle->vehicle_number ?? $currentAssignment->vehicle->registration_number ?? 'N/A' }}
                                </strong>
                            </p>
                            @if($currentAssignment->vehicle)
                                <small class="text-muted">
                                    {{ $currentAssignment->vehicle->vehicle_type ?? '' }}
                                    @if($currentAssignment->vehicle->make && $currentAssignment->vehicle->model)
                                        | {{ $currentAssignment->vehicle->make }} {{ $currentAssignment->vehicle->model }}
                                    @endif
                                </small>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Route</h6>
                            <p class="mb-0">
                                <strong>{{ $currentAssignment->route->route_name ?? 'N/A' }}</strong>
                            </p>
                            @if($currentAssignment->route)
                                <small class="text-muted">
                                    @if($currentAssignment->route->route_code)
                                        Code: {{ $currentAssignment->route->route_code }}
                                    @endif
                                    @if($currentAssignment->route->start_location && $currentAssignment->route->end_location)
                                        <br>{{ $currentAssignment->route->start_location }} → {{ $currentAssignment->route->end_location }}
                                    @endif
                                </small>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Assignment Period</h6>
                            <p class="mb-0">
                                <strong>From:</strong> {{ $currentAssignment->assigned_from->format('M d, Y') }}<br>
                                @if($currentAssignment->assigned_to)
                                    <strong>To:</strong> {{ $currentAssignment->assigned_to->format('M d, Y') }}
                                @else
                                    <span class="badge bg-success">Ongoing</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($currentAssignment->notes)
                    <hr>
                    <div>
                        <h6 class="text-muted">Notes</h6>
                        <p class="mb-0">{{ $currentAssignment->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- No Current Assignment -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>No Active Assignment</strong><br>
                You currently do not have an active transport assignment. Please contact the administrator.
            </div>
        </div>
    </div>
    @endif

    <!-- Assignment History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Assignment History
                    </h5>
                </div>
                <div class="card-body">
                    @if($assignmentHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Route</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignmentHistory as $assignment)
                                <tr>
                                    <td>
                                        {{ $assignment->vehicle->vehicle_number ?? $assignment->vehicle->registration_number ?? 'N/A' }}
                                    </td>
                                    <td>{{ $assignment->route->route_name ?? 'N/A' }}</td>
                                    <td>{{ $assignment->assigned_from->format('M d, Y') }}</td>
                                    <td>
                                        {{ $assignment->assigned_to ? $assignment->assigned_to->format('M d, Y') : 'Ongoing' }}
                                    </td>
                                    <td>
                                        @if($assignment->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($assignmentHistory->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $assignmentHistory->links() }}
                    </div>
                    @endif
                    @else
                    <p class="text-muted text-center mb-0">No assignment history available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

