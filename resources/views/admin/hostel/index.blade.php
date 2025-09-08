@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Hostel Management</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('hostel.allocations.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New Allocation
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Search and Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('admin.hostel.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                               placeholder="Student name or room number">
                    </div>
                    <div>
                        <label for="block" class="block text-sm font-medium text-gray-700">Block</label>
                        <select name="block" id="block" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="">All Blocks</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block }}" {{ request('block') == $block ? 'selected' : '' }}>
                                    {{ $block }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="room_type" class="block text-sm font-medium text-gray-700">Room Type</label>
                        <select name="room_type" id="room_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="">All Types</option>
                            <option value="single" {{ request('room_type') == 'single' ? 'selected' : '' }}>Single</option>
                            <option value="double" {{ request('room_type') == 'double' ? 'selected' : '' }}>Double</option>
                            <option value="triple" {{ request('room_type') == 'triple' ? 'selected' : '' }}>Triple</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hostel Allocations Table -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Hostel Allocations List</h3>
                    <span class="text-sm text-gray-500">Total: {{ $allocations->total() }} allocations</span>
                </div>
                
                @if($allocations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allocation Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($allocations as $allocation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">{{ substr($allocation->student->user->name ?? 'ST', 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $allocation->student->user->name ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $allocation->student->student_id ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $allocation->student->class->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-bold">{{ $allocation->room_number ?? 'N/A' }}</span>
                                    </div>
                                    <div class="text-sm text-gray-500">Block: {{ $allocation->block ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">Floor: {{ $allocation->floor ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">Type: {{ ucfirst($allocation->room_type ?? 'N/A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($allocation->allocation_date)
                                            From: {{ $allocation->allocation_date->format('M d, Y') }}
                                        @else
                                            No start date
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if($allocation->end_date)
                                            To: {{ $allocation->end_date->format('M d, Y') }}
                                        @else
                                            No end date
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if($allocation->allocation_date && $allocation->end_date)
                                            @php
                                                $days = $allocation->allocation_date->diffInDays($allocation->end_date);
                                            @endphp
                                            Duration: {{ $days }} days
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $status = $allocation->status ?? 'active';
                                        $statusColors = [
                                            'active' => 'bg-green-100 text-green-800',
                                            'inactive' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                    <div class="text-sm text-gray-500 mt-1">
                                        @if($allocation->roommates)
                                            <span class="text-blue-600">{{ count($allocation->roommates) }} roommate(s)</span>
                                        @else
                                            No roommates
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('hostel.allocations.show', $allocation) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                        <a href="{{ route('hostel.allocations.edit', $allocation) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form method="POST" action="{{ route('hostel.allocations.destroy', $allocation) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this allocation?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $allocations->links() }}
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hostel allocations found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new hostel allocation.</p>
                    <div class="mt-6">
                        <a href="{{ route('hostel.allocations.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Allocation
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
