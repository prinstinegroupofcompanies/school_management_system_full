@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Attendance Management</h1>
                <p class="text-gray-600 mt-2">Manage student and teacher attendance</p>
            </div>
        </div>

        <!-- Attendance Options -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Student Attendance -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Attendance</h3>
                <form method="GET" action="{{ route('attendance.student') }}" class="space-y-4">
                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Select Class</label>
                        <select id="class_id" name="class_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                required>
                            <option value="">Select a class</option>
                            @if(isset($classes))
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" id="date" name="date" value="{{ $today }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-users mr-2"></i>Take Student Attendance
                    </button>
                </form>
            </div>

            <!-- Teacher Attendance -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Teacher Attendance</h3>
                <form method="GET" action="{{ route('attendance.teacher') }}" class="space-y-4">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" id="date" name="date" value="{{ $today }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>Take Teacher Attendance
                    </button>
                </form>
            </div>
        </div>

        <!-- Recent Attendance Records -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Attendance Records</h3>
            <div class="text-center py-8">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-900 mb-2">No attendance records yet</h4>
                <p class="text-gray-500">Start by taking attendance for a class or teachers.</p>
            </div>
        </div>
    </div>
</div>
@endsection
