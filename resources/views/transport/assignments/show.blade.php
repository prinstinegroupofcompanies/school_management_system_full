@extends('layouts.app')

@section('title', 'Transport Assignment Details')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-bus me-2"></i>Transport Assignment Details
        </h2>
        <div>
            <a href="{{ route('transport.assignments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Assignments
            </a>
            @can('manage transport')
            <a href="{{ route('transport.assignments.edit', $assignment) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Assignment Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Driver/Conductor:</dt>
                        <dd class="col-sm-8">
                            <strong>{{ $assignment->user->name ?? 'N/A' }}</strong><br>
                            <small class="text-muted">{{ $assignment->user->email ?? '' }}</small>
                        </dd>

                        <dt class="col-sm-4">Vehicle:</dt>
                        <dd class="col-sm-8">
                            @if($assignment->vehicle)
                                <strong>{{ $assignment->vehicle->vehicle_number ?? $assignment->vehicle->registration_number ?? 'N/A' }}</strong><br>
                                <small class="text-muted">
                                    @if($assignment->vehicle->vehicle_type)
                                        Type: {{ $assignment->vehicle->vehicle_type }}
                                    @endif
                                    @if($assignment->vehicle->make && $assignment->vehicle->model)
                                        | {{ $assignment->vehicle->make }} {{ $assignment->vehicle->model }}
                                    @endif
                                </small>
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Route:</dt>
                        <dd class="col-sm-8">
                            @if($assignment->route)
                                <strong>{{ $assignment->route->route_name ?? 'N/A' }}</strong><br>
                                <small class="text-muted">
                                    @if($assignment->route->route_code)
                                        Code: {{ $assignment->route->route_code }} |
                                    @endif
                                    @if($assignment->route->start_location && $assignment->route->end_location)
                                        {{ $assignment->route->start_location }} → {{ $assignment->route->end_location }}
                                    @endif
                                </small>
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Assigned From:</dt>
                        <dd class="col-sm-8">{{ $assignment->assigned_from->format('F d, Y') }}</dd>

                        <dt class="col-sm-4">Assigned To:</dt>
                        <dd class="col-sm-8">
                            {{ $assignment->assigned_to ? $assignment->assigned_to->format('F d, Y') : 'Ongoing' }}
                        </dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($assignment->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </dd>

                        @if($assignment->notes)
                        <dt class="col-sm-4">Notes:</dt>
                        <dd class="col-sm-8">{{ $assignment->notes }}</dd>
                        @endif

                        <dt class="col-sm-4">Created At:</dt>
                        <dd class="col-sm-8">{{ $assignment->created_at->format('F d, Y g:i A') }}</dd>

                        <dt class="col-sm-4">Updated At:</dt>
                        <dd class="col-sm-8">{{ $assignment->updated_at->format('F d, Y g:i A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

