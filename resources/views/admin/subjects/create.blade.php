@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Subject</h1>
                    <p class="text-gray-600 mt-2">Add a new subject to the curriculum</p>
                </div>
                <a href="{{ route('admin.subjects.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Subjects
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('admin.subjects.store') }}" method="POST" class="p-6">
                @csrf
                
                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Subject Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Subject Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="e.g., Mathematics">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subject Code -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Subject Code *</label>
                            <input type="text" id="code" name="code" value="{{ old('code') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('code') border-red-500 @enderror"
                                   placeholder="e.g., MATH101">
                            @error('code')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                      placeholder="Brief description of the subject">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Assignment Information -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assignment Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Teacher -->
                        <div>
                            <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-2">Assigned Teacher</label>
                            <select id="teacher_id" name="teacher_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('teacher_id') border-red-500 @enderror">
                                <option value="">Select a teacher</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->user->name }} ({{ $teacher->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Level -->
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                            <select id="level" name="level"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('level') border-red-500 @enderror">
                                <option value="junior" {{ old('level') == 'junior' ? 'selected' : '' }}>Junior</option>
                                <option value="senior" {{ old('level') == 'senior' ? 'selected' : '' }}>Senior</option>
                            </select>
                            @error('level')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Classes (Multiple Selection) -->
                        <div class="md:col-span-2">
                            <label for="class_ids" class="block text-sm font-medium text-gray-700 mb-2">Assign to Classes *</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-3">
                                @foreach($classes as $class)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" 
                                               class="mr-2 rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 @error('class_ids') border-red-500 @enderror"
                                               {{ in_array($class->id, old('class_ids', [])) ? 'checked' : '' }}>
                                        <span class="text-sm text-gray-700">{{ $class->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('class_ids')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hours per Week -->
                        <div>
                            <label for="hours_per_week" class="block text-sm font-medium text-gray-700 mb-2">Hours per Week</label>
                            <input type="number" id="hours_per_week" name="hours_per_week" value="{{ old('hours_per_week', 4) }}" min="1" max="20"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('hours_per_week') border-red-500 @enderror">
                            @error('hours_per_week')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="status" name="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Grading Information -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Grading Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Passing Marks -->
                        <div>
                            <label for="passing_marks" class="block text-sm font-medium text-gray-700 mb-2">Passing Marks</label>
                            <input type="number" id="passing_marks" name="passing_marks" value="{{ old('passing_marks', 50) }}" min="0" max="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('passing_marks') border-red-500 @enderror">
                            @error('passing_marks')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Full Marks -->
                        <div>
                            <label for="full_marks" class="block text-sm font-medium text-gray-700 mb-2">Full Marks</label>
                            <input type="number" id="full_marks" name="full_marks" value="{{ old('full_marks', 100) }}" min="1" max="500"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('full_marks') border-red-500 @enderror">
                            @error('full_marks')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.subjects.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded transition-colors">
                        Create Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection