@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Schools</h1>
                <a href="{{ route('super_admin.schools.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Add School</a>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <form method="GET" action="{{ route('super_admin.schools.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, code, email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Filter</button>
            </form>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">School</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Users</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($schools as $school)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('super_admin.schools.show', $school) }}" class="font-medium text-purple-600 hover:text-purple-800">{{ $school->name }}</a>
                            @if($school->email)<div class="text-sm text-gray-500">{{ $school->email }}</div>@endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $school->code ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $school->users_count }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $school->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $school->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('super_admin.schools.show', $school) }}" class="text-purple-600 hover:text-purple-800 mr-3">View</a>
                            <a href="{{ route('super_admin.schools.edit', $school) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No schools found. <a href="{{ route('super_admin.schools.create') }}" class="text-purple-600">Add one</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($schools->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">{{ $schools->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
