@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Hostel Details</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.hostel.edit', $hostel) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Edit</a>
                <a href="{{ route('admin.hostel.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Back</a>
            </div>
        </div>
        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            <p><span class="font-medium text-gray-700">Name:</span> {{ $hostel->name }}</p>
            <p><span class="font-medium text-gray-700">Address:</span> {{ $hostel->address ?? '—' }}</p>
            <p><span class="font-medium text-gray-700">Phone:</span> {{ $hostel->phone ?? '—' }}</p>
            <p><span class="font-medium text-gray-700">Email:</span> {{ $hostel->email ?? '—' }}</p>
            <p><span class="font-medium text-gray-700">Warden:</span> {{ $hostel->warden_name ?? '—' }} @if($hostel->warden_phone)({{ $hostel->warden_phone }})@endif</p>
            <p><span class="font-medium text-gray-700">Capacity:</span> {{ $hostel->capacity ?? '—' }}</p>
            <p><span class="font-medium text-gray-700">Status:</span> {{ ucfirst($hostel->status ?? 'N/A') }}</p>
            @if($hostel->description)<p><span class="font-medium text-gray-700">Description:</span> {{ $hostel->description }}</p>@endif
        </div>
    </div>
</div>
@endsection
