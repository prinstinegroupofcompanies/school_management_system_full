@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Class Details</h1>
                <p class="text-gray-600">View comprehensive information about the class</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.classes.edit', $class) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit Class
                </a>
                <a href="{{ route('admin.classes.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>

        <!-- Class Information Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Class Name</label>
                            <p class="text-gray-900">{{ $class->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Class Code</label>
                            <p class="text-gray-900">{{ $class->code }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $class->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($class->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Class Teacher</label>
                            <p class="text-gray-900">{{ $class->teacher->user->name ?? 'Not Assigned' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Capacity</label>
                            <p class="text-gray-900">{{ $class->capacity }} students</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Current Students</label>
                            <p class="text-gray-900">{{ $class->students->count() }} students</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Created Date</label>
                            <p class="text-gray-900">{{ $class->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Card -->
        @if($class->students && $class->students->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Enrolled Students ({{ $class->students->count() }})</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($class->students as $student)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->student_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($student->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.students.show', $student) }}" 
                                   class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Subjects Card -->
        @if($class->subjects && $class->subjects->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Subjects ({{ $class->subjects->count() }})</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($class->subjects as $subject)
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

        <!-- Actions Card -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.classes.edit', $class) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit Class
                </a>
                <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="inline" 
                      onsubmit="return confirm('Are you sure you want to delete this class? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-trash mr-2"></i>Delete Class
                    </button>
                </form>
                <a href="{{ route('admin.classes.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
