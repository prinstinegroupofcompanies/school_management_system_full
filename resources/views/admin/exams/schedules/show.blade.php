@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Exam Schedule Details</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.exams.schedules.edit', $schedule) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Edit</a>
                <a href="{{ route('admin.exams.schedules.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Back to list</a>
            </div>
        </div>
        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            <p><span class="font-medium text-gray-700">Title:</span> {{ $schedule->title }}</p>
            <p><span class="font-medium text-gray-700">Exam type:</span> {{ $schedule->examType->name ?? 'N/A' }}</p>
            <p><span class="font-medium text-gray-700">Class:</span> {{ $schedule->class->name ?? 'N/A' }}</p>
            <p><span class="font-medium text-gray-700">Subject:</span> {{ $schedule->subject->name ?? '—' }}</p>
            <p><span class="font-medium text-gray-700">Dates:</span> {{ $schedule->start_date?->format('M d, Y') }} – {{ $schedule->end_date?->format('M d, Y') }}</p>
            <p><span class="font-medium text-gray-700">Venue:</span> {{ $schedule->venue ?? '—' }}</p>
            <p><span class="font-medium text-gray-700">Status:</span> {{ ucfirst($schedule->status ?? 'N/A') }}</p>
        </div>
    </div>
</div>
@endsection
