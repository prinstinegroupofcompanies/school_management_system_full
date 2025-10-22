@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $class->name }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Code: {{ $class->code ?? '-' }} • Session: {{ $class->session ?? '-' }}</p>
                </div>
                <div class="space-x-2">
                    <a href="{{ route('attendance.student', ['class_id' => $class->id, 'date' => now()->format('Y-m-d')]) }}" class="inline-flex items-center px-3 py-2 rounded-md bg-green-600 text-white text-sm hover:bg-green-700">Take Attendance</a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Students ({{ $class->students->count() }})</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Admission #</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($class->students as $student)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $student->user->name ?? 'Student' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">{{ $student->admission_no ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-4 text-center text-gray-500">No students in this class.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Subjects</h2>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($class->subjects as $subject)
                        <li class="py-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $subject->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-300">Teacher: {{ $subject->teacher->user->name ?? $subject->teacher->name ?? 'Not Assigned' }}</p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="py-4 text-center text-gray-500">No subjects assigned.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection


