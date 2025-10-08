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
                        <li class="breadcrumb-item active">Student Assignments</li>
                    </ol>
                </div>
                <h4 class="page-title">Student Transport Assignments</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Student Transport Assignments</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('transport.students.assign') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Assign Student
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search students...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="routeFilter">
                                <option value="">All Routes</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary" onclick="clearFilters()">Clear Filters</button>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="studentsTable">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Route</th>
                                    <th>Pickup Time</th>
                                    <th>Drop-off Time</th>
                                    <th>Fare</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="mdi mdi-account text-muted"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $student->user->name ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $student->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $student->classRoom->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if($student->transportRoute)
                                            <div>
                                                <h6 class="mb-0">{{ $student->transportRoute->route_name }}</h6>
                                                <small class="text-muted">{{ $student->transportRoute->start_location }} - {{ $student->transportRoute->end_location }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">No Route Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->transportRoute)
                                            <span class="badge badge-primary">{{ $student->transportRoute->morning_pickup_time }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->transportRoute)
                                            <span class="badge badge-success">{{ $student->transportRoute->afternoon_dropoff_time }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->transportRoute)
                                            <span class="text-success font-weight-bold">{{ $student->transportRoute->currency }} {{ number_format($student->transportRoute->fare_amount, 2) }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $student->transportRoute ? 'success' : 'secondary' }}">
                                            {{ $student->transportRoute ? 'Active' : 'Not Assigned' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('transport.students.show', $student->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            @if($student->transportRoute)
                                                <a href="{{ route('transport.students.edit', $student->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" onclick="removeAssignment({{ $student->id }})" title="Remove Assignment">
                                                    <i class="mdi mdi-close"></i>
                                                </button>
                                            @else
                                                <a href="{{ route('transport.students.assign', ['student_id' => $student->id]) }}" class="btn btn-sm btn-outline-success" title="Assign Route">
                                                    <i class="mdi mdi-plus"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="mdi mdi-account-group mdi-48px"></i>
                                            <p class="mt-2">No students found</p>
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
                            Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} entries
                        </div>
                        <div>
                            {{ $students->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remove Assignment Modal -->
<div class="modal fade" id="removeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Removal</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove this student's transport assignment? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="removeForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remove Assignment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function removeAssignment(studentId) {
    document.getElementById('removeForm').action = `/transport/students/${studentId}/remove`;
    $('#removeModal').modal('show');
}

// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('routeFilter').addEventListener('change', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const routeFilter = document.getElementById('routeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const rows = document.querySelectorAll('#studentsTable tbody tr');
    
    rows.forEach(row => {
        const studentName = row.cells[0].textContent.toLowerCase();
        const routeName = row.cells[2].textContent.toLowerCase();
        const status = row.cells[6].textContent.toLowerCase();
        
        const matchesSearch = studentName.includes(searchTerm);
        const matchesRoute = !routeFilter || routeName.includes(routeFilter);
        const matchesStatus = !statusFilter || status.includes(statusFilter);
        
        if (matchesSearch && matchesRoute && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('routeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filterTable();
}
</script>
@endpush
