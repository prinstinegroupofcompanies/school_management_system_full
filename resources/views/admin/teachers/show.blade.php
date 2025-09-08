@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Teacher Details</h1>
                <p class="text-gray-600">View comprehensive information about the teacher</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.teachers.edit', $teacher) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit Teacher
                </a>
                <a href="{{ route('admin.teachers.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>

        <!-- Teacher Information Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Teacher ID</label>
                            <p class="text-gray-900">{{ $teacher->teacher_id }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Full Name</label>
                            <p class="text-gray-900">{{ $teacher->user->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900">{{ $teacher->user->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $teacher->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($teacher->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($teacher->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Professional Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Hire Date</label>
                            <p class="text-gray-900">{{ $teacher->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="text-gray-900">{{ $teacher->updated_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Assigned Subjects</label>
                            <p class="text-gray-900">{{ $teacher->subjects->count() }} subjects</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Assigned Classes</label>
                            <p class="text-gray-900">{{ $teacher->classes->count() }} classes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subjects Card -->
        @if($teacher->subjects && $teacher->subjects->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Subjects</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($teacher->subjects as $subject)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900">{{ $subject->name }}</h4>
                    <p class="text-sm text-gray-600">{{ $subject->code }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $subject->description }}</p>
                    <div class="mt-2">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $subject->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($subject->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Classes Card -->
        @if($teacher->classes && $teacher->classes->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Classes</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($teacher->classes as $class)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900">{{ $class->name }}</h4>
                    <p class="text-sm text-gray-600">{{ $class->code }}</p>
                    <p class="text-xs text-gray-500 mt-1">Capacity: {{ $class->capacity }} students</p>
                    <div class="mt-2">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $class->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($class->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Actions Card -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.teachers.edit', $teacher) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit Teacher
                </a>
                <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" class="inline" 
                      onsubmit="return confirm('Are you sure you want to delete this teacher? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-trash mr-2"></i>Delete Teacher
                    </button>
                </form>
                <a href="{{ route('admin.teachers.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
