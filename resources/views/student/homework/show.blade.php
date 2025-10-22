@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $assignment->title }}</h1>
            <p class="text-gray-600 mt-1">
                {{ $assignment->subject->name }} • {{ $assignment->classRoom->name }} • 
                Due: {{ $assignment->getFormattedDueDate() }}
            </p>
        </div>
        <a href="{{ route('student.homework.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Back to Assignments
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <!-- Assignment Status Alert -->
        @if($isOverdue && !$submission)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Assignment Overdue</h3>
                        <p class="text-sm text-red-700 mt-1">This assignment is past its due date. Late submissions may incur penalties.</p>
                    </div>
                </div>
            </div>
        @elseif($submission)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-green-800">Assignment Submitted</h3>
                        <p class="text-sm text-green-700 mt-1">
                            Submitted on {{ $submission->submitted_at->format('M d, Y \a\t g:i A') }}
                            @if($submission->is_late)
                                <span class="font-medium">(Late submission)</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Assignment Details -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Assignment Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $assignment->description }}</p>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-1">Assignment Type</h3>
                        <p class="text-gray-700">{{ ucfirst($assignment->assignment_type) }}</p>
                    </div>
                    
                    <div>
                        <h3 class="font-medium text-gray-900 mb-1">Total Points</h3>
                        <p class="text-gray-700">{{ $assignment->total_points }} points</p>
                    </div>
                    
                    <div>
                        <h3 class="font-medium text-gray-900 mb-1">Teacher</h3>
                        <p class="text-gray-700">{{ $assignment->teacher->user->name ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <h3 class="font-medium text-gray-900 mb-1">Due Date</h3>
                        <p class="text-gray-700">{{ $assignment->getFormattedDueDate() }}</p>
                    </div>
                </div>
            </div>

            @if($assignment->instructions && count($assignment->instructions) > 0)
                <div class="mt-6">
                    <h3 class="font-medium text-gray-900 mb-2">Instructions</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        @foreach($assignment->instructions as $instruction)
                            <li>{{ $instruction }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($assignment->hasAttachments())
                <div class="mt-6">
                    <h3 class="font-medium text-gray-900 mb-2">Attachments</h3>
                    <div class="space-y-2">
                        @foreach($assignment->attachments as $attachment)
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $attachment['name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($attachment['size'] / 1024, 1) }} KB</p>
                                </div>
                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Submission Section -->
        @if(!$submission && $assignment->canAcceptSubmissions())
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Submit Your Work</h2>
                
                <form method="POST" action="{{ route('student.homework.store', $assignment) }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="submission_text" class="block text-sm font-medium text-gray-700 mb-2">
                            Your Response
                        </label>
                        <textarea 
                            id="submission_text" 
                            name="submission_text" 
                            rows="8" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your homework response here..."
                            required
                        >{{ old('submission_text') }}</textarea>
                        @error('submission_text')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                            Attachments (Optional)
                        </label>
                        <input 
                            type="file" 
                            id="attachments" 
                            name="attachments[]" 
                            multiple 
                            accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                        <p class="text-xs text-gray-500 mt-1">Supported formats: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG, GIF (Max 10MB per file)</p>
                        @error('attachments')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            @if($isOverdue)
                                <span class="text-red-600 font-medium">This is a late submission</span>
                            @else
                                <span class="text-green-600 font-medium">On time submission</span>
                            @endif
                        </div>
                        
                        <button 
                            type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Submit Assignment
                        </button>
                    </div>
                </form>
            </div>
        @elseif($submission)
            <!-- Submitted Work Display -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Your Submission</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Response</h3>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $submission->submission_text ?: 'No text response provided.' }}</p>
                        </div>
                    </div>

                    @if($submission->attachments && count($submission->attachments) > 0)
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Your Attachments</h3>
                            <div class="space-y-2">
                                @foreach($submission->attachments as $attachment)
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $attachment['name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ number_format($attachment['size'] / 1024, 1) }} KB</p>
                                        </div>
                                        <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Download
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($submission->status === 'graded' && $submission->marks_obtained !== null)
                        <div class="border-t pt-4">
                            <h3 class="font-medium text-gray-900 mb-2">Grade</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <p class="text-2xl font-bold text-blue-600">{{ $submission->marks_obtained }}/{{ $submission->total_marks }}</p>
                                    <p class="text-sm text-blue-700">Score</p>
                                </div>
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <p class="text-2xl font-bold text-green-600">{{ number_format($submission->percentage, 1) }}%</p>
                                    <p class="text-sm text-green-700">Percentage</p>
                                </div>
                                <div class="text-center p-4 bg-purple-50 rounded-lg">
                                    <p class="text-2xl font-bold text-purple-600">{{ $submission->grade }}</p>
                                    <p class="text-sm text-purple-700">Grade</p>
                                </div>
                            </div>
                            
                            @if($submission->teacher_feedback)
                                <div class="mt-4">
                                    <h4 class="font-medium text-gray-900 mb-2">Teacher Feedback</h4>
                                    <div class="p-4 bg-yellow-50 rounded-lg">
                                        <p class="text-gray-700 whitespace-pre-wrap">{{ $submission->teacher_feedback }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="border-t pt-4">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-600">Your submission is being reviewed by your teacher.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800">Submission Closed</h3>
                        <p class="text-sm text-yellow-700 mt-1">This assignment is no longer accepting submissions.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh every 30 seconds to check for grade updates
    setInterval(function() {
        if (!document.querySelector('form:not(.hidden)') && 
            !document.activeElement.tagName.match(/INPUT|TEXTAREA|SELECT|BUTTON/)) {
            window.location.reload();
        }
    }, 30000);

    // Real-time notification polling
    if (typeof window.pollNotifications === 'function') {
        window.pollNotifications();
    }
});
</script>
@endpush
