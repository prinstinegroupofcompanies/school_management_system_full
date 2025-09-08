@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <h1 class="text-xl font-semibold mb-4">My Classes - Student Attendance History</h1>
            <form method="GET" class="flex flex-col md:flex-row md:items-end gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Class</label>
                    <select name="class_id" class="mt-1 block w-64 border-gray-300 rounded-md">
                        <option value="">All</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Month</label>
                    <input type="month" name="month" value="{{ request('month') }}" class="mt-1 block w-48 border-gray-300 rounded-md" />
                </div>
                <div>
                    <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Filter</button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($records as $row)
                            <tr>
                                <td class="px-4 py-2">{{ $row->attendance_date }}</td>
                                <td class="px-4 py-2">{{ $row->class->name ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $row->student->user->name ?? 'Student' }}</td>
                                <td class="px-4 py-2 capitalize">{{ $row->status }}</td>
                                <td class="px-4 py-2">{{ $row->remarks ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $records->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection


