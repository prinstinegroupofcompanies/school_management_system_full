@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Super Admin Dashboard</h1>
        <p class="text-gray-600 mb-8">Monitor schools and manage the system across all institutions.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Total Schools</h3>
                <p class="text-3xl font-bold text-purple-600 mt-1">{{ $schoolsCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Active Schools</h3>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $activeSchools }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Total School Users</h3>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalUsers }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Recent Schools</h2>
                <a href="{{ route('super_admin.schools.index') }}" class="text-purple-600 hover:text-purple-800 font-medium">View all</a>
            </div>
            <div class="p-4">
                @if($recentSchools->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($recentSchools as $school)
                    <li class="py-3 flex justify-between items-center">
                        <div>
                            <a href="{{ route('super_admin.schools.show', $school) }}" class="font-medium text-gray-900 hover:text-purple-600">{{ $school->name }}</a>
                            @if($school->code)<span class="text-gray-500 text-sm ml-2">({{ $school->code }})</span>@endif
                        </div>
                        <span class="text-sm text-gray-500">{{ $school->users_count }} user(s)</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-gray-500">No schools yet. <a href="{{ route('super_admin.schools.create') }}" class="text-purple-600 hover:text-purple-800">Add a school</a>.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
