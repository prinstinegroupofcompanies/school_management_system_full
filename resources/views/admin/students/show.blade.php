@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Student Details</h1>
                <p class="text-gray-600">View comprehensive information about the student</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.students.edit', $student) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit Student
                </a>
                <a href="{{ route('admin.students.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>

        <!-- Student Information Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Student ID</label>
                            <p class="text-gray-900">{{ $student->student_id }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Full Name</label>
                            <p class="text-gray-900">{{ $student->user->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900">{{ $student->user->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($student->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Class</label>
                            <p class="text-gray-900">{{ $student->class->name ?? 'Not Assigned' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Class Code</label>
                            <p class="text-gray-900">{{ $student->class->code ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Enrollment Date</label>
                            <p class="text-gray-900">{{ $student->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="text-gray-900">{{ $student->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subjects Card -->
        @if($student->subjects && $student->subjects->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Enrolled Subjects</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($student->subjects as $subject)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900">{{ $subject->name }}</h4>
                    <p class="text-sm text-gray-600">{{ $subject->code }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $subject->description }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Actions Card -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.students.edit', $student) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit Student
                </a>
                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline" 
                      onsubmit="return confirm('Are you sure you want to delete this student? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-trash mr-2"></i>Delete Student
                    </button>
                </form>
                <a href="{{ route('admin.students.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
