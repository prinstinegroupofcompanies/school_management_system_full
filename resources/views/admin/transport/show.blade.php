@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Transport Vehicle</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.transport.edit', $transport) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Edit</a>
                <a href="{{ route('admin.transport.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Back</a>
            </div>
        </div>
        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            <p><span class="font-medium text-gray-700">Name:</span> {{ $transport->name }}</p>
            <p><span class="font-medium text-gray-700">Type:</span> {{ ucfirst($transport->type ?? '—') }}</p>
            <p><span class="font-medium text-gray-700">Capacity:</span> {{ $transport->capacity ?? '—' }}</p>
            <p><span class="font-medium text-gray-700">Vehicle number:</span> {{ $transport->vehicle_number ?? '—' }}</p>
            <p><span class="font-medium text-gray-700">Driver:</span> {{ $transport->driver_name ?? '—' }} @if($transport->driver_phone)({{ $transport->driver_phone }})@endif</p>
            <p><span class="font-medium text-gray-700">Status:</span> {{ ucfirst($transport->status ?? 'N/A') }}</p>
            @if($transport->description)<p><span class="font-medium text-gray-700">Description:</span> {{ $transport->description }}</p>@endif
            @if($transport->routes->count() > 0)
            <p><span class="font-medium text-gray-700">Routes:</span> {{ $transport->routes->pluck('route_name')->join(', ') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
