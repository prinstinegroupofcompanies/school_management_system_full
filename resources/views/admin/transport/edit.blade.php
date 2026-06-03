@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Transport Vehicle</h1>
        <form method="POST" action="{{ route('admin.transport.update', $transport) }}" class="bg-white shadow rounded-lg p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $transport->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Type *</label>
                    <select name="type" id="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="bus" {{ old('type', $transport->type) == 'bus' ? 'selected' : '' }}>Bus</option>
                        <option value="van" {{ old('type', $transport->type) == 'van' ? 'selected' : '' }}>Van</option>
                        <option value="car" {{ old('type', $transport->type) == 'car' ? 'selected' : '' }}>Car</option>
                    </select>
                </div>
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity *</label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $transport->capacity) }}" min="1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="vehicle_number" class="block text-sm font-medium text-gray-700">Vehicle Number</label>
                    <input type="text" name="vehicle_number" id="vehicle_number" value="{{ old('vehicle_number', $transport->vehicle_number) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="driver_name" class="block text-sm font-medium text-gray-700">Driver Name</label>
                    <input type="text" name="driver_name" id="driver_name" value="{{ old('driver_name', $transport->driver_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="driver_phone" class="block text-sm font-medium text-gray-700">Driver Phone</label>
                    <input type="text" name="driver_phone" id="driver_phone" value="{{ old('driver_phone', $transport->driver_phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $transport->description) }}</textarea>
            </div>
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.transport.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
