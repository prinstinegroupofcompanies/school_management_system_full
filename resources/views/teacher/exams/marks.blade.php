@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <h1 class="text-xl font-semibold mb-1">Enter Marks</h1>
            <p class="text-sm text-gray-500">{{ $examSchedule->examType->name ?? 'Exam' }} • {{ $examSchedule->class->name ?? '-' }} • {{ $examSchedule->subject->name ?? '-' }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('teacher.exams.marks.store', $examSchedule) }}">
                @csrf
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Student</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Marks (/{{ $examSchedule->total_marks }})</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($students as $idx => $student)
                                @php($existing = $marks[$student->id] ?? null)
                                <tr>
                                    <td class="px-4 py-2">{{ $student->user->name ?? 'Student' }}</td>
                                    <td class="px-4 py-2">
                                        <input type="hidden" name="marks[{{ $idx }}][student_id]" value="{{ $student->id }}" />
                                        <input type="number" name="marks[{{ $idx }}][marks_obtained]" value="{{ $existing->marks_obtained ?? '' }}" step="0.01" min="0" max="{{ $examSchedule->total_marks }}" class="border rounded px-3 py-2 w-40" />
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="text" name="marks[{{ $idx }}][remarks]" value="{{ $existing->remarks ?? '' }}" class="border rounded px-3 py-2 w-full" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <button class="px-4 py-2 bg-green-600 text-white rounded">Save Marks</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


