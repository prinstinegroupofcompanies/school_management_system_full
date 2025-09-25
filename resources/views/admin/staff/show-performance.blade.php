@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Performance Evaluation Details</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.staff.performance.edit', $performance) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('admin.staff.performance') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Performance
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <!-- Staff Information -->
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Staff Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Staff Member</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $performance->staff->user->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee ID</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $performance->staff->employee_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $performance->staff->department->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Designation</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $performance->staff->designation->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Evaluation Information -->
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Evaluation Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Evaluator</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $performance->evaluator->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Evaluation Period</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $performance->evaluation_period }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Period Start</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $performance->period_start ? $performance->period_start->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Period End</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $performance->period_end ? $performance->period_end->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Evaluation Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $performance->evaluation_date ? $performance->evaluation_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($performance->status == 'approved') bg-green-100 text-green-800
                                @elseif($performance->status == 'draft') bg-yellow-100 text-yellow-800
                                @elseif($performance->status == 'submitted') bg-blue-100 text-blue-800
                                @elseif($performance->status == 'reviewed') bg-purple-100 text-purple-800
                                @elseif($performance->status == 'disputed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($performance->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Performance Scores -->
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Performance Scores</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Punctuality</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($performance->punctuality / 10) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $performance->punctuality }}/10</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Work Quality</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($performance->work_quality / 10) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $performance->work_quality }}/10</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Teamwork</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($performance->teamwork / 10) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $performance->teamwork }}/10</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Communication</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($performance->communication / 10) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $performance->communication }}/10</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Initiative</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($performance->initiative / 10) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $performance->initiative }}/10</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Problem Solving</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($performance->problem_solving / 10) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $performance->problem_solving }}/10</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overall Performance -->
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Overall Performance</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Overall Score</label>
                            <div class="mt-2 flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-4 mr-4">
                                    <div class="bg-green-600 h-4 rounded-full" style="width: {{ ($performance->overall_score / 10) * 100 }}%"></div>
                                </div>
                                <span class="text-lg font-bold text-gray-900">{{ $performance->overall_score }}/10</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Performance Rating</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-2
                                @if($performance->performance_rating == 'excellent') bg-green-100 text-green-800
                                @elseif($performance->performance_rating == 'good') bg-blue-100 text-blue-800
                                @elseif($performance->performance_rating == 'satisfactory') bg-yellow-100 text-yellow-800
                                @elseif($performance->performance_rating == 'needs_improvement') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $performance->performance_rating)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Strengths and Areas for Improvement -->
                <div class="mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($performance->strengths)
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Strengths</h3>
                            <p class="text-sm text-gray-900">{{ $performance->strengths }}</p>
                        </div>
                        @endif

                        @if($performance->areas_for_improvement)
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Areas for Improvement</h3>
                            <p class="text-sm text-gray-900">{{ $performance->areas_for_improvement }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Goals and Comments -->
                @if($performance->goals)
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Goals</h3>
                    <p class="text-sm text-gray-900">{{ $performance->goals }}</p>
                </div>
                @endif

                @if($performance->comments)
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Comments</h3>
                    <p class="text-sm text-gray-900">{{ $performance->comments }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
