@extends('layouts.app')

@section('title', 'Lesson Plan Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $lessonPlan->title }}</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $lessonPlan->subject->name ?? 'No Subject' }} • 
                            {{ $lessonPlan->class->name ?? 'No Class' }} • 
                            {{ $lessonPlan->teacher->user->name ?? 'Unknown Teacher' }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.lesson-plans.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Lesson Plans
                        </a>
                        <a href="{{ route('admin.lesson-plans.edit', $lessonPlan) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                        @if($lessonPlan->status === 'submitted')
                            <form action="{{ route('admin.lesson-plans.approve', $lessonPlan) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.lesson-plans.reject', $lessonPlan) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Reject
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Status and Basic Info -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Lesson Plan Information</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($lessonPlan->status === 'approved') bg-green-100 text-green-800
                                @elseif($lessonPlan->status === 'rejected') bg-red-100 text-red-800
                                @elseif($lessonPlan->status === 'submitted') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($lessonPlan->status) }}
                            </span>
                        </div>
                        
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Subject</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $lessonPlan->subject->name ?? 'No Subject' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Class</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $lessonPlan->class->name ?? 'No Class' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Teacher</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $lessonPlan->teacher->user->name ?? 'Unknown Teacher' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Week Period</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $lessonPlan->week_start_date ? $lessonPlan->week_start_date->format('M d, Y') : 'N/A' }} - 
                                    {{ $lessonPlan->week_end_date ? $lessonPlan->week_end_date->format('M d, Y') : 'N/A' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $lessonPlan->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $lessonPlan->updated_at->format('M d, Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Learning Objectives -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Learning Objectives</h3>
                        <div class="prose max-w-none">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $lessonPlan->objectives }}</p>
                        </div>
                    </div>
                </div>

                <!-- Materials Needed -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Materials Needed</h3>
                        <div class="prose max-w-none">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $lessonPlan->materials }}</p>
                        </div>
                    </div>
                </div>

                <!-- Activities -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Activities</h3>
                        <div class="prose max-w-none">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $lessonPlan->activities }}</p>
                        </div>
                    </div>
                </div>

                <!-- Assessment -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assessment</h3>
                        <div class="prose max-w-none">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $lessonPlan->assessment }}</p>
                        </div>
                    </div>
                </div>

                @if($lessonPlan->homework)
                <!-- Homework -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Homework</h3>
                        <div class="prose max-w-none">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $lessonPlan->homework }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($lessonPlan->notes)
                <!-- Additional Notes -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Additional Notes</h3>
                        <div class="prose max-w-none">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $lessonPlan->notes }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Actions -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('admin.lesson-plans.download', $lessonPlan) }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download PDF
                            </a>
                            
                            <form action="{{ route('admin.lesson-plans.destroy', $lessonPlan) }}" method="POST" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to delete this lesson plan?')"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Approval History -->
                @if($lessonPlan->approvals && $lessonPlan->approvals->count() > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Approval History</h3>
                        <div class="space-y-4">
                            @foreach($lessonPlan->approvals as $approval)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $approval->approver->name ?? 'Unknown Approver' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $approval->created_at->format('M d, Y H:i') }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($approval->status === 'approved') bg-green-100 text-green-800
                                            @elseif($approval->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($approval->status) }}
                                        </span>
                                    </div>
                                    @if($approval->comments)
                                        <p class="mt-2 text-sm text-gray-600">{{ $approval->comments }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
