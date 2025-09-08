@extends('layouts.app')

@section('title', 'Transport Management')

@section('content')
<div class="card-premium">
    <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 2rem;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-bus me-3"></i>Transport Management</h3>
            <a href="{{ route('transport.routes.index') }}" class="btn-premium">
                <i class="fas fa-route me-2"></i> Manage Routes
            </a>
        </div>
    </div>
    
    <div class="card-body p-4">
        <!-- Statistics Grid -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card-premium h-100">
                    <div class="card-body text-center">
                        <div class="h-16 w-16 bg-gradient-primary rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-route text-white text-2xl"></i>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 mb-1">{{ $routes->total() }}</h4>
                        <p class="text-gray-600 font-semibold">Transport Routes</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card-premium h-100">
                    <div class="card-body text-center">
                        <div class="h-16 w-16 bg-gradient-secondary rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-bus text-white text-2xl"></i>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 mb-1">{{ $vehicles->total() }}</h4>
                        <p class="text-gray-600 font-semibold">Vehicles</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card-premium h-100">
                    <div class="card-body text-center">
                        <div class="h-16 w-16 bg-gradient-success rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-user-tie text-white text-2xl"></i>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 mb-1">0</h4>
                        <p class="text-gray-600 font-semibold">Active Drivers</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card-premium h-100">
                    <div class="card-body text-center">
                        <div class="h-16 w-16 bg-gradient-warning rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 mb-1">0</h4>
                        <p class="text-gray-600 font-semibold">Students Using Transport</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Routes Section -->
        <div class="card-premium">
            <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 1.5rem;">
                <h4 class="mb-0"><i class="fas fa-list me-2"></i>Recent Routes</h4>
            </div>
            
            <div class="card-body p-0">
                @if($routes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-signature me-2"></i>Route Name</th>
                                    <th><i class="fas fa-bus me-2"></i>Vehicle</th>
                                    <th><i class="fas fa-user-tie me-2"></i>Driver</th>
                                    <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                    <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($routes as $route)
                                <tr>
                                    <td>
                                        <strong>{{ $route->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $route->vehicle->vehicle_number ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $route->driver->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="status-badge-premium {{ $route->status }}">
                                            {{ ucfirst($route->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-premium btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="text-center py-8">
                                            <i class="fas fa-route text-6xl text-gray-300 mb-4"></i>
                                            <h5 class="text-xl font-semibold text-gray-600 mb-2">No Routes Found</h5>
                                            <p class="text-gray-500">Start by creating your first transport route</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-route text-6xl text-gray-300 mb-4"></i>
                        <h5 class="text-xl font-semibold text-gray-600 mb-2">No Routes Available</h5>
                        <p class="text-gray-500 mb-4">Create your first transport route to get started</p>
                        <a href="{{ route('transport.routes.index') }}" class="btn-premium">
                            <i class="fas fa-plus me-2"></i> Create Route
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection