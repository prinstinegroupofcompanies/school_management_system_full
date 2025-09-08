@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Students</h1>
            <p class="text-gray-600">Students from your assigned classes</p>
        </div>
    </div>

    @if($assignedClasses && $assignedClasses->count())
        <div class="mb-6">
            <span class="text-sm text-gray-700">Assigned Classes:</span>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($assignedClasses as $class)
                    <span class="px-2 py-1 text-xs rounded bg-blue-50 text-blue-700">{{ $class->name }} ({{ $class->code }})</span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($students as $student)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->user->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->user->email ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-6 text-center text-sm text-gray-500">No students found for your classes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


