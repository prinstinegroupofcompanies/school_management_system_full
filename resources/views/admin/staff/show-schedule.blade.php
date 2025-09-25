@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Schedule Details</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.staff.schedules.edit', $schedule) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('admin.staff.schedules') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Schedules
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
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->staff->user->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee ID</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->staff->employee_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->staff->department->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Designation</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->staff->designation->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Schedule Information -->
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Schedule Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Schedule Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->schedule_date ? $schedule->schedule_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Time</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $schedule->start_time ? $schedule->start_time->format('H:i') : 'N/A' }} - 
                                {{ $schedule->end_time ? $schedule->end_time->format('H:i') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Shift Type</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($schedule->shift_type == 'morning') bg-yellow-100 text-yellow-800
                                @elseif($schedule->shift_type == 'afternoon') bg-orange-100 text-orange-800
                                @elseif($schedule->shift_type == 'evening') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($schedule->shift_type) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Duration</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->duration_hours }} hours</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Work Location</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->work_location ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($schedule->status == 'completed') bg-green-100 text-green-800
                                @elseif($schedule->status == 'in_progress') bg-yellow-100 text-yellow-800
                                @elseif($schedule->status == 'confirmed') bg-blue-100 text-blue-800
                                @elseif($schedule->status == 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $schedule->status)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Duties and Notes -->
                @if($schedule->duties)
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Duties</h3>
                    <p class="text-sm text-gray-900">{{ $schedule->duties }}</p>
                </div>
                @endif

                @if($schedule->notes)
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Notes</h3>
                    <p class="text-sm text-gray-900">{{ $schedule->notes }}</p>
                </div>
                @endif

                <!-- Assignment Information -->
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assignment Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assigned By</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->assignedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Created At</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->created_at ? $schedule->created_at->format('M d, Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
