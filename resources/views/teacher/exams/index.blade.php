@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <h1 class="text-xl font-semibold mb-4">My Exams</h1>
            <form method="GET" class="flex flex-wrap gap-4 mb-4">
                <select name="class_id" class="border rounded px-3 py-2">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                <select name="subject_id" class="border rounded px-3 py-2">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" @selected(request('subject_id')==$s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="date" value="{{ request('date') }}" class="border rounded px-3 py-2" />
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Filter</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Class</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Subject</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($examSchedules as $ex)
                            <tr>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($ex->exam_date)->toFormattedDateString() }} {{ $ex->start_time }}</td>
                                <td class="px-4 py-2">{{ $ex->class->name ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $ex->subject->name ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    <span>{{ $ex->examType->name ?? '-' }}</span>
                                    @php($now = now())
                                    @if(($ex->is_live ?? false) || ($ex->exam_date && $ex->start_time && $ex->end_time && \Carbon\Carbon::parse($ex->exam_date.' '.$ex->start_time) <= $now && $now <= \Carbon\Carbon::parse($ex->exam_date.' '.$ex->end_time)))
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Live</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('teacher.exams.marks', $ex) }}" class="px-3 py-1 text-sm bg-green-600 text-white rounded">Enter Marks</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">No exams found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $examSchedules->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection


