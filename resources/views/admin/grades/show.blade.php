@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-900">Review Grade</h1>
            <a href="{{ route('admin.grades.index') }}" class="px-3 py-2 bg-gray-600 text-white rounded">Back</a>
        </div>
        <div class="space-y-2 text-sm">
            <div><span class="font-semibold">Student:</span> {{ $grade->student->user->name ?? '' }}</div>
            <div><span class="font-semibold">Class:</span> {{ $grade->class->name ?? '' }}</div>
            <div><span class="font-semibold">Subject:</span> {{ $grade->subject->name ?? '' }}</div>
            <div><span class="font-semibold">Teacher:</span> {{ $grade->teacher->user->name ?? '' }}</div>
            <div><span class="font-semibold">Status:</span> <span class="capitalize">{{ $grade->status }}</span></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Semester 1</h2>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>1st Period: {{ $grade->sem1_p1 ?? '-' }}</li>
                    <li>2nd Period: {{ $grade->sem1_p2 ?? '-' }}</li>
                    <li>3rd Period: {{ $grade->sem1_p3 ?? '-' }}</li>
                    <li>Exam: {{ $grade->sem1_exam ?? '-' }}</li>
                    <li class="font-semibold">Average: {{ $grade->sem1_avg ?? '-' }}</li>
                </ul>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Semester 2</h2>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>4th Period: {{ $grade->sem2_p4 ?? '-' }}</li>
                    <li>5th Period: {{ $grade->sem2_p5 ?? '-' }}</li>
                    <li>6th Period: {{ $grade->sem2_p6 ?? '-' }}</li>
                    <li>Exam: {{ $grade->sem2_exam ?? '-' }}</li>
                    <li class="font-semibold">Average: {{ $grade->sem2_avg ?? '-' }}</li>
                </ul>
            </div>
        </div>
        <div class="mt-6 text-sm">
            <div><span class="font-semibold">Year Average:</span> {{ $grade->year_avg ?? '-' }}</div>
            <div><span class="font-semibold">Promotion:</span> {{ $grade->is_promoted ? 'Promote' : 'Do not promote' }}</div>
            <div><span class="font-semibold">Honors:</span> {{ $grade->honors_status ?? '-' }}</div>
        </div>

        <div class="mt-6 flex space-x-3">
            <form method="POST" action="{{ route('admin.grades.approve', $grade) }}">
                @csrf
                <button class="px-4 py-2 bg-green-600 text-white rounded">Approve</button>
            </form>
            <form method="POST" action="{{ route('admin.grades.reject', $grade) }}">
                @csrf
                <button class="px-4 py-2 bg-red-600 text-white rounded">Reject</button>
            </form>
        </div>
    </div>
</div>
@endsection


