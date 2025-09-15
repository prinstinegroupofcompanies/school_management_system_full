@extends('layouts.app')

@section('title', 'Transport Routes')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Transport Routes</h1>
                    <p class="mt-2 text-gray-600">View all available bus routes and schedules</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('student.transport.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Transport
                    </a>
                </div>
            </div>
        </div>

        <!-- Routes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($routes as $route)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $route->route_name }}</h3>
                            <p class="text-sm text-gray-600">{{ $route->description }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $route->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($route->status) }}
                        </span>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pickup: {{ $route->morning_pickup_time }}
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Dropoff: {{ $route->morning_dropoff_time }}
                        </div>

                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ $route->current_capacity }}/{{ $route->max_capacity }} passengers
                        </div>

                        @if($route->fare_amount)
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Fare: ${{ number_format($route->fare_amount, 2) }}
                        </div>
                        @endif
                    </div>

                    <!-- Pickup Locations -->
                    @if($route->route_details && is_array($route->route_details) && count($route->route_details) > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Route Details</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            @foreach($route->route_details as $location)
                            <li class="flex items-center">
                                <svg class="w-3 h-3 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $location }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Action Button -->
                    @if($route->status === 'active')
                    <button onclick="requestRoute({{ $route->id }})" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        Request This Route
                    </button>
                    @else
                    <button disabled class="w-full bg-gray-300 text-gray-500 px-4 py-2 rounded-md cursor-not-allowed">
                        Route Not Available
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No routes available</h3>
                <p class="mt-1 text-sm text-gray-500">There are currently no transport routes available for booking.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($routes->hasPages())
        <div class="mt-8">
            {{ $routes->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function requestRoute(routeId) {
    if (confirm('Are you sure you want to request this transport route?')) {
        // Create a form to submit the request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("student.transport.request") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add route ID
        const routeInput = document.createElement('input');
        routeInput.type = 'hidden';
        routeInput.name = 'route_id';
        routeInput.value = routeId;
        form.appendChild(routeInput);
        
        // Add pickup location (you might want to make this dynamic)
        const pickupInput = document.createElement('input');
        pickupInput.type = 'hidden';
        pickupInput.name = 'pickup_location';
        pickupInput.value = 'Default Location';
        form.appendChild(pickupInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
