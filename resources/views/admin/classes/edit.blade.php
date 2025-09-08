@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Class</h1>
                <p class="text-gray-600">Update class information and settings</p>
            </div>
            <a href="{{ route('admin.classes.show', $class) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Class
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('admin.classes.update', $class) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Class Name</label>
                            <input type="text" name="name" id="name" 
                                   value="{{ old('name', $class->name) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="session" class="block text-sm font-medium text-gray-700 mb-2">Session</label>
                            <select name="session" id="session"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach(['A','B','C','D','E','F'] as $opt)
                                    <option value="{{ $opt }}" {{ old('session', $class->session) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('session')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Class Capacity</label>
                            <input type="number" name="capacity" id="capacity" min="1"
                                   value="{{ old('capacity', $class->capacity) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('capacity')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Teacher Assignment -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Teacher Assignment</h3>
                    
                    <!-- Teachers -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Teachers</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($teachers as $teacher)
                                <div class="flex items-center">
                                    <input type="checkbox" id="teacher_{{ $teacher->id }}" name="teachers[]" value="{{ $teacher->id }}"
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                                           {{ in_array($teacher->id, old('teachers', $assignedTeachers)) ? 'checked' : '' }}>
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
                                <option value="{{ $teacher->id }}" {{ old('class_teacher_id', $classTeacherId) == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user->name }} ({{ $teacher->teacher_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('class_teacher_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Information</h3>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Class Status</label>
                        <select name="status" id="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="active" {{ old('status', $class->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $class->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.classes.show', $class) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-save mr-2"></i>Update Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
