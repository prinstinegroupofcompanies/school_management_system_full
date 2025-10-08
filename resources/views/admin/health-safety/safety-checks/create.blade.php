@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-plus text-green-500 mr-3"></i>
                    Create Safety Check
                </h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.health-safety.safety-checks.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Safety Checks
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-6">
                <form action="{{ route('admin.health-safety.safety-checks.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Check Type and Area -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="check_type" class="block text-sm font-medium text-gray-700 mb-1">
                                Check Type <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="check_type" name="check_type" value="{{ old('check_type') }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('check_type') border-red-300 @enderror" 
                                   placeholder="Enter check type" required>
                            @error('check_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="area_checked" class="block text-sm font-medium text-gray-700 mb-1">
                                Area Checked <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="area_checked" name="area_checked" value="{{ old('area_checked') }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('area_checked') border-red-300 @enderror" 
                                   placeholder="Enter area being checked" required>
                            @error('area_checked')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Check Date and Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="check_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Check Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="check_date" name="check_date" value="{{ old('check_date', date('Y-m-d')) }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('check_date') border-red-300 @enderror" required>
                            @error('check_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="status" name="status" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('status') border-red-300 @enderror" required>
                                <option value="">Select Status</option>
                                <option value="passed" {{ old('status') === 'passed' ? 'selected' : '' }}>Passed</option>
                                <option value="failed" {{ old('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="needs_attention" {{ old('status') === 'needs_attention' ? 'selected' : '' }}>Needs Attention</option>
                                <option value="critical" {{ old('status') === 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Check Description -->
                    <div>
                        <label for="check_description" class="block text-sm font-medium text-gray-700 mb-1">
                            Check Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="check_description" name="check_description" rows="4" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('check_description') border-red-300 @enderror" 
                                  placeholder="Describe what was checked" required>{{ old('check_description') }}</textarea>
                        @error('check_description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Findings -->
                    <div>
                        <label for="findings" class="block text-sm font-medium text-gray-700 mb-1">
                            Findings
                        </label>
                        <textarea id="findings" name="findings" rows="3" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('findings') border-red-300 @enderror" 
                                  placeholder="Describe any findings from the check">{{ old('findings') }}</textarea>
                        @error('findings')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Recommendations -->
                    <div>
                        <label for="recommendations" class="block text-sm font-medium text-gray-700 mb-1">
                            Recommendations
                        </label>
                        <textarea id="recommendations" name="recommendations" rows="3" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('recommendations') border-red-300 @enderror" 
                                  placeholder="Provide recommendations for improvement">{{ old('recommendations') }}</textarea>
                        @error('recommendations')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Corrective Actions -->
                    <div>
                        <label for="corrective_actions" class="block text-sm font-medium text-gray-700 mb-1">
                            Corrective Actions
                        </label>
                        <textarea id="corrective_actions" name="corrective_actions" rows="3" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('corrective_actions') border-red-300 @enderror" 
                                  placeholder="Describe corrective actions taken or planned">{{ old('corrective_actions') }}</textarea>
                        @error('corrective_actions')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Next Check Date -->
                    <div>
                        <label for="next_check_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Next Check Date
                        </label>
                        <input type="date" id="next_check_date" name="next_check_date" value="{{ old('next_check_date') }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('next_check_date') border-red-300 @enderror">
                        @error('next_check_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.health-safety.safety-checks.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Create Safety Check
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
