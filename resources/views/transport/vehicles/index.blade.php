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
                        <li class="breadcrumb-item active">Vehicles Management</li>
                    </ol>
                </div>
                <h4 class="page-title">Vehicles Management</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Fleet Management</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('transport.vehicles.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add New Vehicle
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search vehicles...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="bus">Bus</option>
                                <option value="van">Van</option>
                                <option value="car">Car</option>
                                <option value="truck">Truck</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary" onclick="clearFilters()">Clear Filters</button>
                        </div>
                    </div>

                    <!-- Vehicles Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="vehiclesTable">
                            <thead>
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Type</th>
                                    <th>Capacity</th>
                                    <th>Driver</th>
                                    <th>Status</th>
                                    <th>Routes</th>
                                    <th>Utilization</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicles as $vehicle)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="mdi mdi-bus text-muted"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $vehicle->name }}</h6>
                                                <small class="text-muted">{{ $vehicle->vehicle_number }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst($vehicle->type) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $vehicle->capacity }} seats</span>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ $vehicle->driver_name }}</h6>
                                            <small class="text-muted">{{ $vehicle->driver_phone }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $vehicle->status === 'active' ? 'success' : ($vehicle->status === 'maintenance' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($vehicle->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $vehicle->routes_count ?? 0 }} routes</span>
                                    </td>
                                    <td>
                                        @php
                                            $utilization = $vehicle->capacity > 0 ? round(($vehicle->current_passengers ?? 0) / $vehicle->capacity * 100, 1) : 0;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $utilization > 80 ? 'bg-danger' : ($utilization > 60 ? 'bg-warning' : 'bg-success') }}" 
                                                 role="progressbar" style="width: {{ $utilization }}%">
                                                {{ $utilization }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('transport.vehicles.show', $vehicle->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('transport.vehicles.edit', $vehicle->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteVehicle({{ $vehicle->id }})" title="Delete">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="mdi mdi-bus mdi-48px"></i>
                                            <p class="mt-2">No vehicles found</p>
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
                            Showing {{ $vehicles->firstItem() ?? 0 }} to {{ $vehicles->lastItem() ?? 0 }} of {{ $vehicles->total() }} entries
                        </div>
                        <div>
                            {{ $vehicles->links() }}
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
                <p>Are you sure you want to delete this vehicle? This action cannot be undone.</p>
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
function deleteVehicle(vehicleId) {
    document.getElementById('deleteForm').action = `/transport/vehicles/${vehicleId}`;
    $('#deleteModal').modal('show');
}

// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('typeFilter').addEventListener('change', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const typeFilter = document.getElementById('typeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const rows = document.querySelectorAll('#vehiclesTable tbody tr');
    
    rows.forEach(row => {
        const name = row.cells[0].textContent.toLowerCase();
        const type = row.cells[1].textContent.toLowerCase();
        const status = row.cells[4].textContent.toLowerCase();
        
        const matchesSearch = name.includes(searchTerm);
        const matchesType = !typeFilter || type.includes(typeFilter);
        const matchesStatus = !statusFilter || status.includes(statusFilter);
        
        if (matchesSearch && matchesType && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filterTable();
}
</script>
@endpush
