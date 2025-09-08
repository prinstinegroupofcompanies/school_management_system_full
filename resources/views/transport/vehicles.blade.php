@extends('layouts.app')

@section('title', 'Transport Vehicles')

@section('content')
<div class="card-premium">
    <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 2rem;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-bus me-3"></i>Transport Vehicles</h3>
            <a href="{{ route('transport.vehicles.create') }}" class="btn-premium">
                <i class="fas fa-plus me-2"></i> Add Vehicle
            </a>
        </div>
    </div>
    
    <div class="card-body p-4">
        @if($vehicles->count() > 0)
            <div class="table-responsive">
                <table class="table table-premium">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-2"></i>Vehicle Number</th>
                            <th><i class="fas fa-car me-2"></i>Make & Model</th>
                            <th><i class="fas fa-calendar me-2"></i>Year</th>
                            <th><i class="fas fa-users me-2"></i>Capacity</th>
                            <th><i class="fas fa-user-tie me-2"></i>Driver</th>
                            <th><i class="fas fa-palette me-2"></i>Color</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-cogs me-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicles as $vehicle)
                        <tr>
                            <td>
                                <strong>{{ $vehicle->vehicle_number }}</strong>
                                <br><small class="text-muted">{{ $vehicle->registration_number }}</small>
                            </td>
                            <td>{{ $vehicle->make }} {{ $vehicle->model }}</td>
                            <td>{{ $vehicle->year }}</td>
                            <td>
                                <span class="badge bg-info">{{ $vehicle->capacity }} seats</span>
                            </td>
                            <td>
                                @if($vehicle->driver)
                                    <strong>{{ $vehicle->driver->name }}</strong>
                                    <br><small class="text-muted">{{ $vehicle->driver->phone }}</small>
                                @else
                                    <span class="text-muted">No driver assigned</span>
                                @endif
                            </td>
                            <td>{{ $vehicle->color ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge-premium {{ $vehicle->status }}">
                                    {{ ucfirst($vehicle->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('transport.vehicles.show', $vehicle) }}" class="btn-premium btn-sm me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('transport.vehicles.edit', $vehicle) }}" class="btn-premium btn-sm me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('transport.vehicles.destroy', $vehicle) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this vehicle?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-premium btn-sm" style="background: var(--danger-gradient);">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($vehicles->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $vehicles->links() }}
            </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-bus text-6xl text-gray-300 mb-4"></i>
                <h5 class="text-xl font-semibold text-gray-600 mb-2">No Vehicles Found</h5>
                <p class="text-gray-500 mb-4">Add your first vehicle to get started</p>
                <a href="{{ route('transport.vehicles.create') }}" class="btn-premium">
                    <i class="fas fa-plus me-2"></i> Add Vehicle
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
