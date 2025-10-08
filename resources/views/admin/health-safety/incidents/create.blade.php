@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-plus text-red-500 mr-3"></i>
                    Create New Incident
                </h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.health-safety.incidents.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Incidents
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-6">
                <form action="{{ route('admin.health-safety.incidents.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="incident_type" class="block text-sm font-medium text-gray-700 mb-1">
                                Incident Type <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="incident_type" name="incident_type" value="{{ old('incident_type') }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('incident_type') border-red-300 @enderror" 
                                   placeholder="Enter incident type" required>
                            @error('incident_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="severity" class="block text-sm font-medium text-gray-700 mb-1">
                                Severity <span class="text-red-500">*</span>
                            </label>
                            <select id="severity" name="severity" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('severity') border-red-300 @enderror" required>
                                <option value="">Select Severity</option>
                                <option value="minor" {{ old('severity') === 'minor' ? 'selected' : '' }}>Minor</option>
                                <option value="moderate" {{ old('severity') === 'moderate' ? 'selected' : '' }}>Moderate</option>
                                <option value="major" {{ old('severity') === 'major' ? 'selected' : '' }}>Major</option>
                                <option value="critical" {{ old('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                            @error('severity')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Location and Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                Location <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="location" name="location" value="{{ old('location') }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('location') border-red-300 @enderror" 
                                   placeholder="Enter incident location" required>
                            @error('location')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="incident_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Incident Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="incident_date" name="incident_date" value="{{ old('incident_date', date('Y-m-d')) }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('incident_date') border-red-300 @enderror" required>
                            @error('incident_date')
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
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('student_id') border-red-300 @enderror">
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
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('staff_id') border-red-300 @enderror">
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

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="4" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('description') border-red-300 @enderror" 
                                  placeholder="Describe the incident in detail" required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Symptoms -->
                    <div>
                        <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-1">
                            Symptoms
                        </label>
                        <textarea id="symptoms" name="symptoms" rows="3" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('symptoms') border-red-300 @enderror" 
                                  placeholder="Describe any symptoms observed">{{ old('symptoms') }}</textarea>
                        @error('symptoms')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions Taken -->
                    <div>
                        <label for="actions_taken" class="block text-sm font-medium text-gray-700 mb-1">
                            Actions Taken
                        </label>
                        <textarea id="actions_taken" name="actions_taken" rows="3" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('actions_taken') border-red-300 @enderror" 
                                  placeholder="Describe actions taken in response to the incident">{{ old('actions_taken') }}</textarea>
                        @error('actions_taken')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Medical Treatment -->
                    <div>
                        <label for="medical_treatment" class="block text-sm font-medium text-gray-700 mb-1">
                            Medical Treatment
                        </label>
                        <textarea id="medical_treatment" name="medical_treatment" rows="3" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 @error('medical_treatment') border-red-300 @enderror" 
                                  placeholder="Describe any medical treatment provided">{{ old('medical_treatment') }}</textarea>
                        @error('medical_treatment')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.health-safety.incidents.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Create Incident
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
