@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Homework</h1>
                    <p class="text-gray-600 mt-2">View and submit your assignments</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if($assignments && $assignments->count() > 0)
            <div class="grid gap-6">
                @foreach($assignments as $assignment)
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900">{{ $assignment->title }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $assignment->subject->name ?? 'N/A' }}</p>
                                <p class="text-gray-600 mt-2">{{ Str::limit($assignment->description, 150) }}</p>
                                
                                <div class="flex items-center space-x-4 mt-4 text-sm text-gray-500">
                                    <span>Due: {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('M j, Y g:i A') : 'No due date' }}</span>
                                    @if($assignment->teacher)
                                        <span>By: {{ $assignment->teacher->user->name ?? 'Unknown' }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="ml-6 flex flex-col items-end space-y-2">
                                @if($assignment->student_submission)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Submitted
                                    </span>
                                @elseif($assignment->is_overdue)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Overdue
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('student.homework.show', $assignment) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        View Details
                                    </a>
                                    @if(!$assignment->student_submission && !$assignment->is_overdue)
                                        <a href="{{ route('student.homework.create', $assignment) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700">
                                            Submit
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($assignments->hasPages())
                <div class="mt-8">
                    {{ $assignments->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No homework assignments</h3>
                    <p class="mt-1 text-sm text-gray-500">You don't have any homework assignments at the moment.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh the page every 30 seconds to show real-time updates
    setInterval(function() {
        // Only refresh if user is not actively interacting with forms or modals
        if (!document.querySelector('.modal:not(.hidden)') && 
            !document.activeElement.tagName.match(/INPUT|TEXTAREA|SELECT|BUTTON/)) {
            window.location.reload();
        }
    }, 30000); // 30 seconds

    // Real-time notification polling
    if (typeof window.pollNotifications === 'function') {
        window.pollNotifications();
    }

    // Add smooth transitions for status changes
    const statusElements = document.querySelectorAll('[data-status]');
    statusElements.forEach(element => {
        element.style.transition = 'all 0.3s ease-in-out';
    });

    // Highlight new assignments
    const newAssignments = document.querySelectorAll('.assignment-item[data-new="true"]');
    newAssignments.forEach(item => {
        item.classList.add('animate-pulse');
        setTimeout(() => {
            item.classList.remove('animate-pulse');
        }, 3000);
    });
});
</script>
@endpush