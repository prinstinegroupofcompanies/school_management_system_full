@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header -->
    <div class="bg-white shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 opacity-5"></div>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 relative">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-plus text-blue-600 mr-3"></i>
                        Add New Vehicle
                    </h1>
                    <p class="text-lg text-gray-600">Register a new vehicle for the transport system</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('transport.vehicles') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Vehicles
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-8 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="px-8 py-8">
                <form action="{{ route('transport.vehicles.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Basic Vehicle Information -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-car text-blue-600 mr-3"></i>
                            Basic Vehicle Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Vehicle Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror" 
                                       placeholder="Enter vehicle name" required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vehicle_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Vehicle Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="vehicle_number" name="vehicle_number" value="{{ old('vehicle_number') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('vehicle_number') border-red-300 @enderror" 
                                       placeholder="Enter vehicle number" required>
                                @error('vehicle_number')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Vehicle Type <span class="text-red-500">*</span>
                                </label>
                                <select id="type" name="type" 
                                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-300 @enderror" required>
                                    <option value="">Select Vehicle Type</option>
                                    <option value="bus" {{ old('type') === 'bus' ? 'selected' : '' }}>Bus</option>
                                    <option value="van" {{ old('type') === 'van' ? 'selected' : '' }}>Van</option>
                                    <option value="car" {{ old('type') === 'car' ? 'selected' : '' }}>Car</option>
                                    <option value="truck" {{ old('type') === 'truck' ? 'selected' : '' }}>Truck</option>
                                </select>
                                @error('type')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Capacity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="capacity" name="capacity" value="{{ old('capacity') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('capacity') border-red-300 @enderror" 
                                       placeholder="Enter passenger capacity" min="1" max="100" required>
                                @error('capacity')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Driver Information -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-user-tie text-green-600 mr-3"></i>
                            Driver Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="driver_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Driver Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="driver_name" name="driver_name" value="{{ old('driver_name') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('driver_name') border-red-300 @enderror" 
                                       placeholder="Enter driver name" required>
                                @error('driver_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="driver_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Driver Phone <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="driver_phone" name="driver_phone" value="{{ old('driver_phone') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('driver_phone') border-red-300 @enderror" 
                                       placeholder="Enter driver phone number" required>
                                @error('driver_phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Specifications -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-cogs text-purple-600 mr-3"></i>
                            Vehicle Specifications
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                                    Model
                                </label>
                                <input type="text" id="model" name="model" value="{{ old('model') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('model') border-red-300 @enderror" 
                                       placeholder="Enter vehicle model">
                                @error('model')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                                    Year
                                </label>
                                <input type="number" id="year" name="year" value="{{ old('year') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('year') border-red-300 @enderror" 
                                       placeholder="Enter vehicle year" min="1900" max="{{ date('Y') + 1 }}">
                                @error('year')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="status" name="status" 
                                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-300 @enderror" required>
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-sticky-note text-orange-600 mr-3"></i>
                            Additional Information
                        </h3>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                            </label>
                            <textarea id="notes" name="notes" rows="4" 
                                      class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror" 
                                      placeholder="Enter any additional notes about the vehicle">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-8 border-t border-gray-200">
                        <a href="{{ route('transport.vehicles') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i>
                            Add Vehicle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
