@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $lessonPlan->title }}</h1>
                    <div class="mt-2 flex items-center space-x-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($lessonPlan->status === 'draft') bg-gray-100 text-gray-800
                            @elseif($lessonPlan->status === 'submitted') bg-blue-100 text-blue-800
                            @elseif($lessonPlan->status === 'first_level_approved') bg-yellow-100 text-yellow-800
                            @elseif($lessonPlan->status === 'second_level_approved') bg-green-100 text-green-800
                            @elseif($lessonPlan->status === 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $lessonPlan->status_text }}
                        </span>
                        <span class="text-sm text-gray-500">
                            Created {{ $lessonPlan->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @if($lessonPlan->canBeEdited())
                    <a href="{{ route('teacher.lesson-plans.edit', $lessonPlan) }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    @endif
                    @if($lessonPlan->canBeSubmitted())
                    <form action="{{ route('teacher.lesson-plans.submit', $lessonPlan) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center"
                                onclick="return confirm('Are you sure you want to submit this lesson plan for approval?')">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Submit for Approval
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('teacher.lesson-plans.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Lesson Plans
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Basic Information</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Subject</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $lessonPlan->subject->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Class</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $lessonPlan->class->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Lesson Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $lessonPlan->lesson_date ? $lessonPlan->lesson_date->format('M d, Y') : 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($lessonPlan->start_time && $lessonPlan->end_time)
                                        {{ \Carbon\Carbon::createFromFormat('H:i', $lessonPlan->start_time)->format('g:i A') }} - 
                                        {{ \Carbon\Carbon::createFromFormat('H:i', $lessonPlan->end_time)->format('g:i A') }}
                                        ({{ $lessonPlan->duration_minutes }} minutes)
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        @if($lessonPlan->description)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $lessonPlan->description }}</dd>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Learning Objectives -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Learning Objectives</h3>
                        <div class="text-sm text-gray-900 whitespace-pre-wrap">{{ $lessonPlan->objectives }}</div>
                    </div>
                </div>

                <!-- Materials Needed -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Materials Needed</h3>
                        <div class="text-sm text-gray-900 whitespace-pre-wrap">{{ $lessonPlan->materials_needed }}</div>
                    </div>
                </div>

                <!-- Activities -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Activities</h3>
                        <div class="text-sm text-gray-900 whitespace-pre-wrap">{{ $lessonPlan->activities }}</div>
                    </div>
                </div>

                <!-- Assessment -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assessment</h3>
                        <div class="text-sm text-gray-900 whitespace-pre-wrap">{{ $lessonPlan->assessment }}</div>
                    </div>
                </div>

                <!-- Homework -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Homework</h3>
                        <div class="text-sm text-gray-900 whitespace-pre-wrap">{{ $lessonPlan->homework }}</div>
                    </div>
                </div>

                @if($lessonPlan->notes)
                <!-- Additional Notes -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Additional Notes</h3>
                        <div class="text-sm text-gray-900 whitespace-pre-wrap">{{ $lessonPlan->notes }}</div>
                    </div>
                </div>
                @endif

                <!-- Attachments -->
                @if($lessonPlan->attachments && count($lessonPlan->attachments) > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Attachments</h3>
                        <ul class="space-y-3">
                            @foreach($lessonPlan->attachments as $attachment)
                            <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="h-8 w-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $attachment['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($attachment['size'] / 1024, 1) }} KB</p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" 
                                   class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Download
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Approval Status -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Approval Status</h3>
                        <div class="space-y-4">
                            @if($lessonPlan->approvals->count() > 0)
                                @foreach($lessonPlan->approvals as $approval)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $approval->level_text }}</p>
                                        <p class="text-xs text-gray-500">
                                            @if($approval->approver)
                                                {{ $approval->approver->name }}
                                            @else
                                                Pending Assignment
                                            @endif
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($approval->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($approval->status === 'approved') bg-green-100 text-green-800
                                        @elseif($approval->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $approval->status_text }}
                                    </span>
                                </div>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-500">No approval records yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Lesson Plan Info -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Lesson Plan Info</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="text-sm text-gray-900">{{ $lessonPlan->created_at->format('M d, Y g:i A') }}</dd>
                            </div>
                            @if($lessonPlan->updated_at != $lessonPlan->created_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="text-sm text-gray-900">{{ $lessonPlan->updated_at->format('M d, Y g:i A') }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Version</dt>
                                <dd class="text-sm text-gray-900">{{ $lessonPlan->version ?? '1.0' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Actions</h3>
                        <div class="space-y-3">
                            @if($lessonPlan->canBeEdited())
                            <a href="{{ route('teacher.lesson-plans.edit', $lessonPlan) }}" 
                               class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Lesson Plan
                            </a>
                            @endif
                            
                            @if($lessonPlan->canBeSubmitted())
                            <form action="{{ route('teacher.lesson-plans.submit', $lessonPlan) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                                        onclick="return confirm('Are you sure you want to submit this lesson plan for approval?')">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Submit for Approval
                                </button>
                            </form>
                            @endif

                            @if(in_array($lessonPlan->status, ['draft', 'rejected']))
                            <form action="{{ route('teacher.lesson-plans.destroy', $lessonPlan) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this lesson plan? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Lesson Plan
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
