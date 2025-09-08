@extends('layouts.app')

@section('title', 'Transport Drivers')

@section('content')
<div class="card-premium">
    <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 2rem;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-user-tie me-3"></i>Transport Drivers</h3>
            <a href="{{ route('transport.drivers.create') }}" class="btn-premium">
                <i class="fas fa-plus me-2"></i> Add Driver
            </a>
        </div>
    </div>
    
    <div class="card-body p-4">
        @if($drivers->count() > 0)
            <div class="table-responsive">
                <table class="table table-premium">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user me-2"></i>Driver Name</th>
                            <th><i class="fas fa-id-card me-2"></i>License Number</th>
                            <th><i class="fas fa-phone me-2"></i>Phone</th>
                            <th><i class="fas fa-envelope me-2"></i>Email</th>
                            <th><i class="fas fa-calendar-alt me-2"></i>Experience</th>
                            <th><i class="fas fa-calendar-check me-2"></i>License Expiry</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-cogs me-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drivers as $driver)
                        <tr>
                            <td>
                                <strong>{{ $driver->name }}</strong>
                                @if($driver->address)
                                    <br><small class="text-muted">{{ Str::limit($driver->address, 30) }}</small>
                                @endif
                            </td>
                            <td>
                                <code>{{ $driver->license_number }}</code>
                            </td>
                            <td>{{ $driver->phone }}</td>
                            <td>{{ $driver->email ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $driver->experience_years }} years</span>
                            </td>
                            <td>
                                <span class="{{ $driver->license_expiry < now()->addDays(30) ? 'text-danger' : 'text-success' }}">
                                    {{ $driver->license_expiry->format('M d, Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge-premium {{ $driver->status }}">
                                    {{ ucfirst($driver->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('transport.drivers.show', $driver) }}" class="btn-premium btn-sm me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('transport.drivers.edit', $driver) }}" class="btn-premium btn-sm me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('transport.drivers.destroy', $driver) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this driver?')">
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
            @if($drivers->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $drivers->links() }}
            </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-user-tie text-6xl text-gray-300 mb-4"></i>
                <h5 class="text-xl font-semibold text-gray-600 mb-2">No Drivers Found</h5>
                <p class="text-gray-500 mb-4">Add your first driver to get started</p>
                <a href="{{ route('transport.drivers.create') }}" class="btn-premium">
                    <i class="fas fa-plus me-2"></i> Add Driver
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
