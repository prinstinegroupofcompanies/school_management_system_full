@extends('layouts.app')

@section('title', 'Transport Assignments')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-bus me-2"></i>Transport Assignments
        </h2>
        @can('manage transport')
        <a href="{{ route('transport.assignments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Assignment
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('transport.assignments.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="user_id" class="form-label">Driver/Conductor</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">All</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ request('user_id') == $driver->id ? 'selected' : '' }}>
                                {{ $driver->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('transport.assignments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Driver/Conductor</th>
                            <th>Vehicle</th>
                            <th>Route</th>
                            <th>Assigned From</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td>
                                <strong>{{ $assignment->user->name ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $assignment->user->email ?? '' }}</small>
                            </td>
                            <td>
                                @if($assignment->vehicle)
                                    {{ $assignment->vehicle->vehicle_number ?? $assignment->vehicle->registration_number ?? 'N/A' }}
                                    <br><small class="text-muted">{{ $assignment->vehicle->vehicle_type ?? '' }}</small>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($assignment->route)
                                    <strong>{{ $assignment->route->route_name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $assignment->route->route_code ?? '' }}</small>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
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
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('transport.assignments.show', $assignment) }}" class="btn btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('manage transport')
                                    <a href="{{ route('transport.assignments.edit', $assignment) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('transport.assignments.destroy', $assignment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate this assignment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Deactivate">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <p class="text-muted mb-0">No assignments found.</p>
                                @can('manage transport')
                                <a href="{{ route('transport.assignments.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus me-2"></i>Create First Assignment
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($assignments->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $assignments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

