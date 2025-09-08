@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Gradesheet ({{ $year }})</h1>
        <div class="space-x-2">
            <a href="{{ route('student.gradesheet.pdf', ['year' => $year, 'period' => $period]) }}" class="px-3 py-2 bg-green-600 text-white rounded">Download PDF</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sem 1 Avg</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sem 2 Avg</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Avg</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($grades as $g)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $g->subject->name ?? '' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $g->sem1_avg ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $g->sem2_avg ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $g->year_avg ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm capitalize">{{ $g->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No grades yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


