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
                        @forelse($exams as $ex)
                            <tr>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($ex->start_time)->toFormattedDateString() }} {{ \Carbon\Carbon::parse($ex->start_time)->format('H:i') }}</td>
                                <td class="px-4 py-2">{{ $ex->classRoom->name ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $ex->subject->name ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    <span>{{ ucfirst($ex->exam_type) }}</span>
                                    @php($now = now())
                                    @if($ex->is_published && $ex->start_time && $ex->end_time && \Carbon\Carbon::parse($ex->start_time) <= $now && $now <= \Carbon\Carbon::parse($ex->end_time))
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Live</span>
                                    @elseif($ex->is_published)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Published</span>
                                    @else
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('teacher.exams.show', $ex) }}" class="px-3 py-1 text-sm bg-blue-600 text-white rounded">View</a>
                                        <a href="{{ route('teacher.exams.edit', $ex) }}" class="px-3 py-1 text-sm bg-yellow-600 text-white rounded">Edit</a>
                                        @if($ex->is_published)
                                            <form method="POST" action="{{ route('teacher.exams.unpublish', $ex) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 text-sm bg-orange-600 text-white rounded">Unpublish</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('teacher.exams.publish', $ex) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 text-sm bg-green-600 text-white rounded">Publish</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('teacher.exams.destroy', $ex) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this exam?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 text-sm bg-red-600 text-white rounded">Delete</button>
                                        </form>
                                    </div>
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
            <div class="mt-4">{{ $exams->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection


