@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Class</h1>
                    <p class="text-gray-600 mt-2">Add a new class and assign teachers</p>
                </div>
                <a href="{{ route('admin.classes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Classes
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('admin.classes.store') }}" method="POST" class="p-6">
                @csrf
                
                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Class Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Class Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="e.g., Grade 10A">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Session -->
                        <div>
                            <label for="session" class="block text-sm font-medium text-gray-700 mb-2">Session *</label>
                            <select id="session" name="session"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('session') border-red-500 @enderror">
                                @foreach(['A','B','C','D','E','F'] as $opt)
                                    <option value="{{ $opt }}" {{ old('session') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('session')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Capacity -->
                        <div>
                            <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Capacity *</label>
                            <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 30) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('capacity') border-red-500 @enderror"
                                   min="1">
                            @error('capacity')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select id="status" name="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                  placeholder="Optional description of the class">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Location Information -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Room Number -->
                        <div>
                            <label for="room_number" class="block text-sm font-medium text-gray-700 mb-2">Room Number</label>
                            <input type="text" id="room_number" name="room_number" value="{{ old('room_number') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('room_number') border-red-500 @enderror"
                                   placeholder="e.g., R101">
                            @error('room_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Building -->
                        <div>
                            <label for="building" class="block text-sm font-medium text-gray-700 mb-2">Building</label>
                            <input type="text" id="building" name="building" value="{{ old('building') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('building') border-red-500 @enderror"
                                   placeholder="e.g., Main Building">
                            @error('building')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Floor -->
                        <div>
                            <label for="floor" class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                            <input type="text" id="floor" name="floor" value="{{ old('floor') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('floor') border-red-500 @enderror"
                                   placeholder="e.g., 1st Floor">
                            @error('floor')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Teacher Assignment -->
                <div class="pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Teacher Assignment</h3>
                    
                    <!-- Teachers -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Teachers</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($teachers as $teacher)
                                <div class="flex items-center">
                                    <input type="checkbox" id="teacher_{{ $teacher->id }}" name="teachers[]" value="{{ $teacher->id }}"
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                                           {{ in_array($teacher->id, old('teachers', [])) ? 'checked' : '' }}>
                                    <label for="teacher_{{ $teacher->id }}" class="ml-2 text-sm text-gray-700">
                                        {{ $teacher->user->name }} ({{ $teacher->teacher_id }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('teachers')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Class Teacher -->
                    <div>
                        <label for="class_teacher_id" class="block text-sm font-medium text-gray-700 mb-2">Class Teacher</label>
                        <select id="class_teacher_id" name="class_teacher_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('class_teacher_id') border-red-500 @enderror">
                            <option value="">Select a class teacher (optional)</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('class_teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user->name }} ({{ $teacher->teacher_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('class_teacher_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.classes.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Create Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
