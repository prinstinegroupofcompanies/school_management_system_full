@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $subject->name }}</h1>
                    <p class="text-gray-600 mt-2">Subject Details</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Subject
                    </a>
                    <a href="{{ route('admin.subjects.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Subjects
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Subject Information -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Subject Information</h3>
                        
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Subject Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $subject->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Subject Code</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $subject->code ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Level</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($subject->level ?? 'junior') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Hours per Week</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $subject->hours_per_week ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Passing Marks</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $subject->passing_marks ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Full Marks</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $subject->full_marks ?? 'N/A' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $subject->description ?? 'No description available' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subject->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($subject->status ?? 'active') }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Assigned Teacher & Classes -->
            <div class="space-y-6">
                <!-- Assigned Teacher -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assigned Teacher</h3>
                        @if($subject->teacher)
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-yellow-500 flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">{{ substr($subject->teacher->user->name, 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $subject->teacher->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $subject->teacher->employee_id }}</div>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No teacher assigned</p>
                        @endif
                    </div>
                </div>

                <!-- Assigned Classes -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assigned Classes</h3>
                        @if($subject->classes && $subject->classes->count() > 0)
                            <div class="space-y-2">
                                @foreach($subject->classes as $class)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <span class="text-sm font-medium text-gray-900">{{ $class->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $class->code }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No classes assigned</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
