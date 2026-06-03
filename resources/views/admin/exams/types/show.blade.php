@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Exam Type: {{ $examType->name }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.exams.types.edit', $examType) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Edit</a>
                <a href="{{ route('admin.exams.types.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Back to list</a>
            </div>
        </div>
        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            <p><span class="font-medium text-gray-700">Code:</span> {{ $examType->code ?? '—' }}</p>
            <p><span class="font-medium text-gray-700">Type:</span> {{ ucfirst($examType->type ?? '—') }}</p>
            <p><span class="font-medium text-gray-700">Total marks:</span> {{ $examType->total_marks ?? '—' }}</p>
            <p><span class="font-medium text-gray-700">Passing marks:</span> {{ $examType->passing_marks ?? '—' }}</p>
            <p><span class="font-medium text-gray-700">Duration:</span> {{ $examType->duration_minutes ? $examType->duration_minutes . ' mins' : '—' }}</p>
            <p><span class="font-medium text-gray-700">Weightage:</span> {{ $examType->weightage_percentage ?? '—' }}%</p>
            <p><span class="font-medium text-gray-700">Status:</span> {{ ucfirst($examType->status ?? 'N/A') }}</p>
            <p><span class="font-medium text-gray-700">Compulsory:</span> {{ $examType->is_compulsory ? 'Yes' : 'No' }}</p>
            <p><span class="font-medium text-gray-700">Counts for final:</span> {{ $examType->counts_for_final ? 'Yes' : 'No' }}</p>
            @if($examType->description)<p><span class="font-medium text-gray-700">Description:</span> {{ $examType->description }}</p>@endif
        </div>
        @if($examType->examSchedules->count() > 0)
        <div class="bg-white shadow rounded-lg p-6 mt-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Exam schedules using this type ({{ $examType->examSchedules->count() }})</h2>
            <ul class="divide-y divide-gray-200">
                @foreach($examType->examSchedules->take(10) as $schedule)
                <li class="py-2">
                    <a href="{{ route('admin.exams.schedules.show', $schedule) }}" class="text-indigo-600 hover:text-indigo-900">{{ $schedule->title }}</a>
                    <span class="text-gray-500 text-sm"> — {{ $schedule->start_date?->format('M d, Y') ?? 'N/A' }}</span>
                </li>
                @endforeach
            </ul>
            @if($examType->examSchedules->count() > 10)
            <p class="text-sm text-gray-500 mt-2">… and {{ $examType->examSchedules->count() - 10 }} more</p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
