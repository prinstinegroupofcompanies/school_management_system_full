@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Health Records</h1>
        <a href="{{ route('admin.health-safety.records.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
            Create Health Record
        </a>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Student</th>
                    <th class="py-2 px-4 border-b">Type</th>
                    <th class="py-2 px-4 border-b">Title</th>
                    <th class="py-2 px-4 border-b">Record Date</th>
                    <th class="py-2 px-4 border-b">Expiry Date</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td class="py-2 px-4 border-b">
                        {{ $record->student->first_name ?? '' }} {{ $record->student->last_name ?? '' }}
                    </td>
                    <td class="py-2 px-4 border-b">{{ $record->record_type }}</td>
                    <td class="py-2 px-4 border-b">{{ $record->title }}</td>
                    <td class="py-2 px-4 border-b">{{ $record->record_date }}</td>
                    <td class="py-2 px-4 border-b">{{ $record->expiry_date ?? '-' }}</td>
                    <td class="py-2 px-4 border-b">
                        <a href="{{ route('admin.health-safety.records.show', $record->id) }}" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-4 text-center text-gray-500">No health records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection