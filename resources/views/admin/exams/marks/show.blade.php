@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Exam Mark Details</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.exams.marks.edit', $mark) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Edit</a>
                <a href="{{ route('admin.exams.marks.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Back to list</a>
            </div>
        </div>
        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            <p><span class="font-medium text-gray-700">Student:</span> {{ $mark->student->user->name ?? 'N/A' }} ({{ $mark->student->student_id ?? 'N/A' }})</p>
            <p><span class="font-medium text-gray-700">Exam:</span> {{ $mark->examSchedule->examType->name ?? 'N/A' }} – {{ $mark->examSchedule->subject->name ?? 'N/A' }}, {{ $mark->examSchedule->class->name ?? 'N/A' }}</p>
            <p><span class="font-medium text-gray-700">Marks:</span> {{ $mark->marks_obtained ?? 0 }} / {{ $mark->total_marks ?? 100 }} ({{ number_format($mark->percentage ?? 0, 1) }}%)</p>
            <p><span class="font-medium text-gray-700">Status:</span> {{ ucfirst($mark->status ?? 'N/A') }}</p>
            @if($mark->remarks)<p><span class="font-medium text-gray-700">Remarks:</span> {{ $mark->remarks }}</p>@endif
            @if($mark->teacher_comments)<p><span class="font-medium text-gray-700">Teacher comments:</span> {{ $mark->teacher_comments }}</p>@endif
        </div>
    </div>
</div>
@endsection
