@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Subject</h1>
                <p class="text-gray-600 mt-2">Update subject information</p>
            </div>
            <a href="{{ route('admin.subjects.show', $subject) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Subject
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('admin.subjects.update', $subject) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Subject Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Subject Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Subject Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $subject->name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror" 
                                   required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Code -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Subject Code *</label>
                            <input type="text" id="code" name="code" value="{{ old('code', $subject->code) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror" 
                                   required>
                            @error('code')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Classes -->
                        <div class="md:col-span-2">
                            <label for="class_ids" class="block text-sm font-medium text-gray-700 mb-2">Classes *</label>
                            <p class="text-sm text-gray-500 mb-2">Hold Ctrl/Cmd to select multiple classes</p>
                            <select id="class_ids" name="class_ids[]" multiple size="6"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('class_ids') border-red-500 @enderror"
                                    required>
                                @if(isset($classes))
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" 
                                                {{ (collect(old('class_ids', $subject->classes->pluck('id')->toArray()))->contains($class->id)) ? 'selected' : '' }}>
                                            {{ $class->name }} ({{ $class->session ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('class_ids')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            @error('class_ids.*')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Teacher -->
                        <div>
                            <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-2">Teacher</label>
                            <select id="teacher_id" name="teacher_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('teacher_id') border-red-500 @enderror">
                                <option value="">Select a teacher</option>
                                @if(isset($teachers))
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $subject->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->user->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('teacher_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Level -->
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level *</label>
                            <select id="level" name="level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('level') border-red-500 @enderror" required>
                                <option value="junior" {{ old('level', $subject->level) === 'junior' ? 'selected' : '' }}>Junior High</option>
                                <option value="senior" {{ old('level', $subject->level) === 'senior' ? 'selected' : '' }}>Senior High</option>
                            </select>
                            @error('level')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hours Per Week -->
                        <div>
                            <label for="hours_per_week" class="block text-sm font-medium text-gray-700 mb-2">Hours Per Week *</label>
                            <input type="number" id="hours_per_week" name="hours_per_week" value="{{ old('hours_per_week', $subject->hours_per_week) }}" min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('hours_per_week') border-red-500 @enderror" 
                                   required>
                            @error('hours_per_week')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Subject Type -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Subject Type</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Compulsory -->
                        <div class="flex items-center">
                            <input type="checkbox" id="is_compulsory" name="is_compulsory" value="1" 
                                   {{ old('is_compulsory', $subject->is_compulsory) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_compulsory" class="ml-2 block text-sm text-gray-900">
                                Compulsory Subject
                            </label>
                        </div>

                        <!-- Elective -->
                        <div class="flex items-center">
                            <input type="checkbox" id="is_elective" name="is_elective" value="1" 
                                   {{ old('is_elective', $subject->is_elective) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_elective" class="ml-2 block text-sm text-gray-900">
                                Elective Subject
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                  placeholder="Enter subject description...">{{ old('description', $subject->description) }}</textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.subjects.show', $subject) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
