@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Teacher</h1>
                <p class="text-gray-600">Update teacher information and settings</p>
            </div>
            <a href="{{ route('admin.teachers.show', $teacher) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Teacher
            </a>
        </div>

        <!-- Edit Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="name" id="name" 
                                   value="{{ old('name', $teacher->user->name) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" id="email" 
                                   value="{{ old('email', $teacher->user->email) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('email')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Professional Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="active" {{ old('status', $teacher->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $teacher->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $teacher->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="subjects" class="block text-sm font-medium text-gray-700 mb-2">Assigned Subjects</label>
                            <select name="subjects[]" id="subjects" multiple
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                            {{ in_array($subject->id, old('subjects', $teacher->subjects->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $subject->name }} ({{ $subject->code }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple subjects</p>
                            @error('subjects')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Class Assignment -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Class Assignment</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="classes" class="block text-sm font-medium text-gray-700 mb-2">Assigned Classes</label>
                            <select name="classes[]" id="classes" multiple
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" 
                                            {{ in_array($class->id, old('classes', $assignedClasses)) ? 'selected' : '' }}>
                                        {{ $class->name }} (Session: {{ $class->session ?? 'A' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple classes</p>
                            @error('classes')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Class Teacher Assignment -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Class Teacher Assignment</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="class_teacher_classes" class="block text-sm font-medium text-gray-700 mb-2">Class Teacher For</label>
                            <select name="class_teacher_classes[]" id="class_teacher_classes" multiple
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" 
                                            {{ in_array($class->id, old('class_teacher_classes', $teacher->classTeacherClasses->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $class->name }} (Session: {{ $class->session ?? 'A' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Select classes where this teacher will be the class teacher. Hold Ctrl/Cmd to select multiple.</p>
                            @error('class_teacher_classes')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.teachers.show', $teacher) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-save mr-2"></i>Update Teacher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
