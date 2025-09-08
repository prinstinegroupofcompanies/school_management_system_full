@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Grades ({{ ucfirst($status) }})</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.grades.index', ['status' => 'pending']) }}" class="px-3 py-2 rounded {{ $status==='pending' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">Pending</a>
            <a href="{{ route('admin.grades.index', ['status' => 'approved']) }}" class="px-3 py-2 rounded {{ $status==='approved' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">Approved</a>
            <a href="{{ route('admin.grades.index', ['status' => 'rejected']) }}" class="px-3 py-2 rounded {{ $status==='rejected' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">Rejected</a>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Avg</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($grades as $g)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $g->student->user->name ?? '' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $g->subject->name ?? '' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $g->year_avg ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm capitalize">{{ $g->status }}</td>
                    <td class="px-6 py-4 text-sm text-right">
                        <a href="{{ route('admin.grades.show', $g) }}" class="text-indigo-600">Review</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No records.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $grades->withQueryString()->links() }}</div>
    </div>
    
</div>
@endsection


