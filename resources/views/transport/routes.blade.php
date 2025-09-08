@extends('layouts.app')

@section('title', 'Transport Routes')

@section('content')
<div class="card-premium">
    <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 2rem;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-route me-3"></i>Transport Routes</h3>
            <a href="{{ route('transport.routes.create') }}" class="btn-premium">
                <i class="fas fa-plus me-2"></i> Add Route
            </a>
        </div>
    </div>
    
    <div class="card-body p-4">
        @if($routes->count() > 0)
            <div class="table-responsive">
                <table class="table table-premium">
                    <thead>
                        <tr>
                            <th><i class="fas fa-signature me-2"></i>Route Name</th>
                            <th><i class="fas fa-map-marker-alt me-2"></i>From</th>
                            <th><i class="fas fa-map-marker me-2"></i>To</th>
                            <th><i class="fas fa-bus me-2"></i>Vehicle</th>
                            <th><i class="fas fa-user-tie me-2"></i>Driver</th>
                            <th><i class="fas fa-route me-2"></i>Distance</th>
                            <th><i class="fas fa-dollar-sign me-2"></i>Fare</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-cogs me-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($routes as $route)
                        <tr>
                            <td>
                                <strong>{{ $route->name }}</strong>
                                @if($route->description)
                                    <br><small class="text-muted">{{ Str::limit($route->description, 50) }}</small>
                                @endif
                            </td>
                            <td>{{ $route->start_location }}</td>
                            <td>{{ $route->end_location }}</td>
                            <td>
                                @if($route->vehicle)
                                    <span class="badge bg-info">{{ $route->vehicle->vehicle_number }}</span>
                                    <br><small class="text-muted">{{ $route->vehicle->model }}</small>
                                @else
                                    <span class="text-muted">No vehicle assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($route->driver)
                                    <strong>{{ $route->driver->name }}</strong>
                                    <br><small class="text-muted">{{ $route->driver->phone }}</small>
                                @else
                                    <span class="text-muted">No driver assigned</span>
                                @endif
                            </td>
                            <td>{{ $route->distance }} km</td>
                            <td>${{ number_format($route->fare, 2) }}</td>
                            <td>
                                <span class="status-badge-premium {{ $route->is_active ? 'active' : 'inactive' }}">
                                    {{ $route->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('transport.routes.show', $route) }}" class="btn-premium btn-sm me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('transport.routes.edit', $route) }}" class="btn-premium btn-sm me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('transport.routes.destroy', $route) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this route?')">
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
            @if($routes->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $routes->links() }}
            </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-route text-6xl text-gray-300 mb-4"></i>
                <h5 class="text-xl font-semibold text-gray-600 mb-2">No Transport Routes Found</h5>
                <p class="text-gray-500 mb-4">Create your first transport route to get started</p>
                <a href="{{ route('transport.routes.create') }}" class="btn-premium">
                    <i class="fas fa-plus me-2"></i> Create Route
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
