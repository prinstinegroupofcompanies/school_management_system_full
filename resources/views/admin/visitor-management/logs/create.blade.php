@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-plus text-blue-500 mr-3"></i>
                    Create Visitor Log Entry
                </h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.visitor-management.logs.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Logs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-6">
                <form action="{{ route('admin.visitor-management.logs.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Visitor and Destination -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="visitor_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Visitor <span class="text-red-500">*</span>
                            </label>
                            <select id="visitor_id" name="visitor_id" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('visitor_id') border-red-300 @enderror" required>
                                <option value="">Select Visitor</option>
                                @foreach($visitors ?? [] as $visitor)
                                    <option value="{{ $visitor->id }}" {{ old('visitor_id') == $visitor->id ? 'selected' : '' }}>
                                        {{ $visitor->first_name }} {{ $visitor->last_name }}
                                        @if($visitor->organization)
                                            - {{ $visitor->organization }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('visitor_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="destination" class="block text-sm font-medium text-gray-700 mb-1">
                                Destination <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="destination" name="destination" value="{{ old('destination') }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('destination') border-red-300 @enderror" 
                                   placeholder="Enter destination" required>
                            @error('destination')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Student and Staff Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Student (Optional)
                            </label>
                            <select id="student_id" name="student_id" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('student_id') border-red-300 @enderror">
                                <option value="">Select Student</option>
                                @foreach($students ?? [] as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->first_name }} {{ $student->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Staff (Optional)
                            </label>
                            <select id="staff_id" name="staff_id" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('staff_id') border-red-300 @enderror">
                                <option value="">Select Staff</option>
                                @foreach($staff ?? [] as $staffMember)
                                    <option value="{{ $staffMember->id }}" {{ old('staff_id') == $staffMember->id ? 'selected' : '' }}>
                                        {{ $staffMember->first_name }} {{ $staffMember->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('staff_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div>
                        <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">
                            Purpose <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="purpose" name="purpose" value="{{ old('purpose') }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('purpose') border-red-300 @enderror" 
                               placeholder="Enter purpose of visit" required>
                        @error('purpose')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Purpose Details -->
                    <div>
                        <label for="purpose_details" class="block text-sm font-medium text-gray-700 mb-1">
                            Purpose Details
                        </label>
                        <textarea id="purpose_details" name="purpose_details" rows="3" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('purpose_details') border-red-300 @enderror" 
                                  placeholder="Provide additional details about the purpose">{{ old('purpose_details') }}</textarea>
                        @error('purpose_details')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Escort Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="escort_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Escort Name
                            </label>
                            <input type="text" id="escort_name" name="escort_name" value="{{ old('escort_name') }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('escort_name') border-red-300 @enderror" 
                                   placeholder="Enter escort name">
                            @error('escort_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="escort_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Escort Phone
                            </label>
                            <input type="text" id="escort_phone" name="escort_phone" value="{{ old('escort_phone') }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('escort_phone') border-red-300 @enderror" 
                                   placeholder="Enter escort phone number">
                            @error('escort_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Expected Check Out Time -->
                    <div>
                        <label for="expected_check_out_time" class="block text-sm font-medium text-gray-700 mb-1">
                            Expected Check Out Time
                        </label>
                        <input type="datetime-local" id="expected_check_out_time" name="expected_check_out_time" value="{{ old('expected_check_out_time') }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('expected_check_out_time') border-red-300 @enderror">
                        @error('expected_check_out_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.visitor-management.logs.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Create Log Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
