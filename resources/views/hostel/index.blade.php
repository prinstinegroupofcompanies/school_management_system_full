@extends('layouts.app')

@section('title', 'Hostel Management')

@section('content')
<div class="card-premium">
    <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 2rem;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-building me-3"></i>Hostel Management</h3>
            <a href="{{ route('hostel.create-hostel') }}" class="btn-premium">
                <i class="fas fa-plus me-2"></i> Add Hostel
            </a>
        </div>
    </div>
    
    <div class="card-body p-4">
        @if($hostels->count() > 0)
            <!-- Hostels Grid -->
            <div class="row mb-4">
                @foreach($hostels as $hostel)
                <div class="col-md-4 mb-4">
                    <div class="card-premium h-100">
                        <div class="card-body">
                            <div class="d-flex items-center mb-3">
                                <div class="h-12 w-12 bg-gradient-primary rounded-xl flex items-center justify-center me-3">
                                    <i class="fas fa-building text-white text-xl"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 mb-1">{{ $hostel->name }}</h5>
                                    <p class="text-sm text-gray-600">{{ $hostel->location ?? 'No location set' }}</p>
                                </div>
                            </div>
                            
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ $hostel->rooms_count }}</div>
                                    <div class="text-xs text-gray-600 font-semibold">ROOMS</div>
                                </div>
                                <div class="col-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ $hostel->students_count }}</div>
                                    <div class="text-xs text-gray-600 font-semibold">STUDENTS</div>
                                </div>
                                <div class="col-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ $hostel->capacity }}</div>
                                    <div class="text-xs text-gray-600 font-semibold">CAPACITY</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Occupancy</span>
                                    <span class="text-gray-900 font-semibold">{{ number_format(($hostel->students_count / max($hostel->capacity, 1)) * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-primary h-2 rounded-full" style="width: {{ ($hostel->students_count / max($hostel->capacity, 1)) * 100 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button class="btn-premium btn-sm flex-1">
                                    <i class="fas fa-eye me-1"></i> View
                                </button>
                                <button class="btn-premium btn-sm flex-1">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                <h5 class="text-xl font-semibold text-gray-600 mb-2">No Hostels Found</h5>
                <p class="text-gray-500 mb-4">Start by creating your first hostel</p>
                <a href="{{ route('hostel.create-hostel') }}" class="btn-premium">
                    <i class="fas fa-plus me-2"></i> Create Hostel
                </a>
            </div>
        @endif

        <!-- Rooms Section -->
        @if($rooms->count() > 0)
        <div class="card-premium mt-4">
            <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 1.5rem;">
                <h4 class="mb-0"><i class="fas fa-bed me-2"></i>Room Management</h4>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-premium mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>Room Number</th>
                                <th><i class="fas fa-tag me-2"></i>Room Name</th>
                                <th><i class="fas fa-building me-2"></i>Hostel</th>
                                <th><i class="fas fa-users me-2"></i>Capacity</th>
                                <th><i class="fas fa-user-friends me-2"></i>Occupancy</th>
                                <th><i class="fas fa-dollar-sign me-2"></i>Monthly Rent</th>
                                <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                <th><i class="fas fa-cogs me-2"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rooms as $room)
                            <tr>
                                <td><strong>{{ $room->room_number }}</strong></td>
                                <td>{{ $room->room_name ?? 'N/A' }}</td>
                                <td>{{ $room->hostel->name ?? 'N/A' }}</td>
                                <td>{{ $room->capacity }}</td>
                                <td>{{ $room->current_occupancy }}</td>
                                <td>${{ number_format($room->monthly_rent, 2) }} {{ $room->currency ?? 'USD' }}</td>
                                <td>
                                    <span class="status-badge-premium {{ $room->status }}">
                                        {{ ucfirst($room->status) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-premium btn-sm me-1">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn-premium btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($rooms->hasPages())
                <div class="d-flex justify-content-center p-4">
                    {{ $rooms->links() }}
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="card-premium mt-4">
            <div class="card-body text-center py-12">
                <i class="fas fa-bed text-6xl text-gray-300 mb-4"></i>
                <h5 class="text-xl font-semibold text-gray-600 mb-2">No Rooms Available</h5>
                <p class="text-gray-500">Create rooms for your hostels to get started</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection