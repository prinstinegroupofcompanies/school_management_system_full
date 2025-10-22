@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Take Attendance</h1>
                <div class="flex space-x-3">
                    <a href="{{ route('teacher.attendance.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Class Information -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $class->name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">Class Code: {{ $class->code ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="text-lg font-medium text-gray-900">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $class->students->count() }}</div>
                        <div class="text-sm text-gray-500">Total Students</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600" id="present-count">0</div>
                        <div class="text-sm text-gray-500">Present</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600" id="absent-count">0</div>
                        <div class="text-sm text-gray-500">Absent</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Form -->
        <form action="{{ route('teacher.attendance.store') }}" method="POST" id="attendance-form">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <input type="hidden" name="date" value="{{ $date }}">
            
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Student Attendance</h3>
                    
                    <div class="space-y-4">
                        @foreach($class->students as $student)
                            @php
                                $existingStatus = $existingAttendance[$student->id] ?? 'present';
                            @endphp
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ substr($student->user->name ?? 'ST', 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $student->user->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $student->student_id ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" 
                                               id="present-{{ $student->id }}" 
                                               name="attendance[{{ $loop->index }}][status]" 
                                               value="present" 
                                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                               {{ $existingStatus === 'present' ? 'checked' : '' }}>
                                        <label for="present-{{ $student->id }}" class="text-sm text-gray-700">Present</label>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" 
                                               id="absent-{{ $student->id }}" 
                                               name="attendance[{{ $loop->index }}][status]" 
                                               value="absent" 
                                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                                               {{ $existingStatus === 'absent' ? 'checked' : '' }}>
                                        <label for="absent-{{ $student->id }}" class="text-sm text-gray-700">Absent</label>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" 
                                               id="late-{{ $student->id }}" 
                                               name="attendance[{{ $loop->index }}][status]" 
                                               value="late" 
                                               class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300"
                                               {{ $existingStatus === 'late' ? 'checked' : '' }}>
                                        <label for="late-{{ $student->id }}" class="text-sm text-gray-700">Late</label>
                                    </div>
                                    <input type="hidden" name="attendance[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('teacher.attendance.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Attendance
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateCounts() {
        const presentCount = document.querySelectorAll('input[name*="[status]"][value="present"]:checked').length;
        const absentCount = document.querySelectorAll('input[name*="[status]"][value="absent"]:checked').length;
        
        document.getElementById('present-count').textContent = presentCount;
        document.getElementById('absent-count').textContent = absentCount;
    }
    
    // Update counts on page load
    updateCounts();
    
    // Update counts when radio buttons change
    document.querySelectorAll('input[name*="[status]"]').forEach(radio => {
        radio.addEventListener('change', updateCounts);
    });
    
    // Form submission
    document.getElementById('attendance-form').addEventListener('submit', function(e) {
        const totalStudents = {{ $class->students->count() }};
        const checkedCount = document.querySelectorAll('input[name*="[status]"]:checked').length;
        
        if (checkedCount < totalStudents) {
            e.preventDefault();
            alert('Please mark attendance for all students.');
            return false;
        }
    });
});
</script>
@endpush
@endsection
