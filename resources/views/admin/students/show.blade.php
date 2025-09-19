@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Student Details</h2>
                    <div class="space-x-2">
                        <a href="{{ route('students.edit', $student) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Edit Student
                        </a>
                        <a href="{{ route('students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Students
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Personal Information</h3>
                        
                        <div class="space-y-2">
                            <div>
                                <span class="font-medium text-gray-700">Full Name:</span>
                                <span class="text-gray-900">{{ $student->user->name ?? 'N/A' }}</span>
                            </div>
                            
                            <div>
                                <span class="font-medium text-gray-700">Email:</span>
                                <span class="text-gray-900">{{ $student->user->email ?? 'N/A' }}</span>
                            </div>
                            
                            <div>
                                <span class="font-medium text-gray-700">Student ID:</span>
                                <span class="text-gray-900">{{ $student->student_id ?? 'N/A' }}</span>
                            </div>
                            
                            <div>
                                <span class="font-medium text-gray-700">Date of Birth:</span>
                                <span class="text-gray-900">{{ $student->date_of_birth ?? 'N/A' }}</span>
                            </div>
                            
                            <div>
                                <span class="font-medium text-gray-700">Phone:</span>
                                <span class="text-gray-900">{{ $student->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Academic Information</h3>
                        
                        <div class="space-y-2">
                            <div>
                                <span class="font-medium text-gray-700">Class:</span>
                                <span class="text-gray-900">{{ $student->classRoom->name ?? 'N/A' }}</span>
                            </div>
                            
                            <div>
                                <span class="font-medium text-gray-700">Status:</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ($student->user->is_active ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ($student->user->is_active ?? false) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <div>
                                <span class="font-medium text-gray-700">Joined:</span>
                                <span class="text-gray-900">{{ $student->created_at ? $student->created_at->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Address</h3>
                        <p class="text-gray-900">{{ $student->address ?? 'No address provided' }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Delete Student
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection