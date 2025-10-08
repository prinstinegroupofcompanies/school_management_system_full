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
                        <li class="breadcrumb-item active">Routes Management</li>
                    </ol>
                </div>
                <h4 class="page-title">Transport Routes</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Transport Routes</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('transport.routes.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add New Route
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search routes...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="vehicleFilter">
                                <option value="">All Vehicles</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary" onclick="clearFilters()">Clear Filters</button>
                        </div>
                    </div>

                    <!-- Routes Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="routesTable">
                            <thead>
                                <tr>
                                    <th>Route</th>
                                    <th>Code</th>
                                    <th>Start Location</th>
                                    <th>End Location</th>
                                    <th>Distance</th>
                                    <th>Fare</th>
                                    <th>Vehicle</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($routes as $route)
                                <tr>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $route->route_name }}</h6>
                                            <small class="text-muted">{{ $route->description }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $route->route_code }}</span>
                                    </td>
                                    <td>{{ $route->start_location }}</td>
                                    <td>{{ $route->end_location }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $route->distance_km }} km</span>
                                    </td>
                                    <td>
                                        <span class="text-success font-weight-bold">{{ $route->currency }} {{ number_format($route->fare_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($route->transport)
                                            <div>
                                                <h6 class="mb-0">{{ $route->transport->name }}</h6>
                                                <small class="text-muted">{{ $route->transport->vehicle_number }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">No Vehicle</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $route->current_passengers > 0 ? 'warning' : 'success' }}">
                                            {{ $route->current_passengers }}/{{ $route->max_capacity }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $route->is_active ? 'success' : 'secondary' }}">
                                            {{ $route->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('transport.routes.show', $route->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('transport.routes.edit', $route->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteRoute({{ $route->id }})" title="Delete">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="mdi mdi-map-marker-path mdi-48px"></i>
                                            <p class="mt-2">No routes found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $routes->firstItem() ?? 0 }} to {{ $routes->lastItem() ?? 0 }} of {{ $routes->total() }} entries
                        </div>
                        <div>
                            {{ $routes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this route? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteRoute(routeId) {
    document.getElementById('deleteForm').action = `/transport/routes/${routeId}`;
    $('#deleteModal').modal('show');
}

// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);
document.getElementById('vehicleFilter').addEventListener('change', filterTable);

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const vehicleFilter = document.getElementById('vehicleFilter').value;
    
    const rows = document.querySelectorAll('#routesTable tbody tr');
    
    rows.forEach(row => {
        const routeName = row.cells[0].textContent.toLowerCase();
        const startLocation = row.cells[2].textContent.toLowerCase();
        const endLocation = row.cells[3].textContent.toLowerCase();
        const status = row.cells[8].textContent.toLowerCase();
        const vehicle = row.cells[6].textContent.toLowerCase();
        
        const matchesSearch = routeName.includes(searchTerm) || startLocation.includes(searchTerm) || endLocation.includes(searchTerm);
        const matchesStatus = !statusFilter || status.includes(statusFilter);
        const matchesVehicle = !vehicleFilter || vehicle.includes(vehicleFilter);
        
        if (matchesSearch && matchesStatus && matchesVehicle) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('vehicleFilter').value = '';
    filterTable();
}
</script>
@endpush
