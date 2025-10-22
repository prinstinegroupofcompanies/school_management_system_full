@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header -->
    <div class="bg-white shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-blue-600 opacity-5"></div>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 relative">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-bus text-green-600 mr-3"></i>
                        Assign Students to Transport
                    </h1>
                    <p class="text-lg text-gray-600">Assign students to transport routes and vehicles</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('transport.students.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Students
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-8 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="px-8 py-8">
                <form action="{{ route('transport.students.assign.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Student Selection -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-user text-blue-600 mr-3"></i>
                            Student Selection
                        </h3>
                        
                        <div>
                            <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Student <span class="text-red-500">*</span>
                            </label>
                            <select id="student_id" name="student_id" 
                                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('student_id') border-red-300 @enderror" required>
                                <option value="">Select a student</option>
                                @foreach($students ?? [] as $student)
                                    <option value="{{ $student->id }}" {{ (old('student_id') == $student->id || $selectedStudent?->id == $student->id) ? 'selected' : '' }}>
                                        {{ $student->first_name }} {{ $student->last_name }} 
                                        @if($student->classRoom)
                                            - {{ $student->classRoom->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Route Selection -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-route text-green-600 mr-3"></i>
                            Transport Route
                        </h3>
                        
                        <div>
                            <label for="route_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Route <span class="text-red-500">*</span>
                            </label>
                            <select id="route_id" name="route_id" 
                                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('route_id') border-red-300 @enderror" required>
                                <option value="">Select a transport route</option>
                                @foreach($routes ?? [] as $route)
                                    <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                        {{ $route->name }} 
                                        @if($route->transport)
                                            - {{ $route->transport->name }} ({{ $route->transport->vehicle_number }})
                                        @endif
                                        - {{ $route->pickup_time }} to {{ $route->dropoff_time }}
                                    </option>
                                @endforeach
                            </select>
                            @error('route_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-map-marker-alt text-purple-600 mr-3"></i>
                            Location Details
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="pickup_location" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pickup Location
                                </label>
                                <input type="text" id="pickup_location" name="pickup_location" value="{{ old('pickup_location') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('pickup_location') border-red-300 @enderror" 
                                       placeholder="Enter specific pickup location">
                                @error('pickup_location')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="dropoff_location" class="block text-sm font-medium text-gray-700 mb-2">
                                    Dropoff Location
                                </label>
                                <input type="text" id="dropoff_location" name="dropoff_location" value="{{ old('dropoff_location') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('dropoff_location') border-red-300 @enderror" 
                                       placeholder="Enter specific dropoff location">
                                @error('dropoff_location')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="assignment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Assignment Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="assignment_date" name="assignment_date" value="{{ old('assignment_date', date('Y-m-d')) }}" 
                                   class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('assignment_date') border-red-300 @enderror" required>
                            @error('assignment_date')
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
                                      class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 @error('notes') border-red-300 @enderror" 
                                      placeholder="Enter any additional notes about the assignment">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-8 border-t border-gray-200">
                        <a href="{{ route('transport.students.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i>
                            Assign Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
