@extends('layouts.app')

@section('title', 'Homework Assignment Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header -->
    <div class="bg-white shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 opacity-5"></div>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 relative">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-book-open text-blue-600 mr-3"></i>
                        {{ $assignment->title }}
                    </h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span class="flex items-center">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            {{ $assignment->classRoom->name ?? 'N/A' }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-book mr-2"></i>
                            {{ $assignment->subject->name ?? 'N/A' }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-calendar mr-2"></i>
                            Due: {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') : 'N/A' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('teacher.homework.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Homework
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['total_students'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Submitted</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['submitted_count'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-star text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Graded</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['graded_count'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending Review</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['pending_review'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-percentage text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Submission Rate</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['submission_rate'], 1) }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Assignment Details -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                    <div class="px-6 py-6 bg-gradient-to-r from-blue-600 to-indigo-600">
                        <h3 class="text-xl font-semibold text-white flex items-center">
                            <i class="fas fa-info-circle mr-3"></i>
                            Assignment Details
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $assignment->description ?? 'No description provided' }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Points</label>
                                <p class="text-lg font-semibold text-blue-600">{{ $assignment->total_points ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Average Score</label>
                                <p class="text-lg font-semibold text-green-600">{{ $stats['avg_score'] ? number_format($stats['avg_score'], 1) : 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Created</label>
                                <p class="text-gray-900">{{ $assignment->created_at ? \Carbon\Carbon::parse($assignment->created_at)->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $assignment->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($assignment->status ?? 'Unknown') }}
                                </span>
                            </div>
                        </div>

                        @if($assignment->attachments)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                            <div class="space-y-2">
                                @foreach(json_decode($assignment->attachments, true) ?? [] as $attachment)
                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" 
                                   class="flex items-center p-2 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-paperclip mr-2 text-gray-500"></i>
                                    <span class="text-sm text-gray-700">{{ $attachment['name'] ?? 'Attachment' }}</span>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Submissions List -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                    <div class="px-6 py-6 bg-gradient-to-r from-green-600 to-blue-600">
                        <h3 class="text-xl font-semibold text-white flex items-center">
                            <i class="fas fa-list mr-3"></i>
                            Student Submissions
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        @if($submissions->count() > 0)
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($submissions as $submission)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-white">
                                                            {{ substr($submission->student->user->name ?? 'N/A', 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $submission->student->user->name ?? 'N/A' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $submission->student->user->email ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y H:i') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($submission->score !== null)
                                                <span class="font-semibold text-green-600">{{ $submission->score }}/{{ $assignment->total_points }}</span>
                                            @else
                                                <span class="text-gray-400">Not graded</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($submission->status === 'graded') bg-green-100 text-green-800
                                                @elseif($submission->status === 'submitted') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($submission->status ?? 'Unknown') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('teacher.homework.grade', $submission->id) }}" 
                                                   class="text-blue-600 hover:text-blue-900 transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($submission->submission_text || $submission->attachments)
                                                <a href="#" onclick="viewSubmission({{ $submission->id }})" 
                                                   class="text-green-600 hover:text-green-900 transition-colors">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <div class="px-6 py-4 border-t border-gray-200">
                                {{ $submissions->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No submissions yet</h3>
                                <p class="text-gray-500">Students haven't submitted their homework yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submission Modal -->
<div id="submissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Submission Details</h3>
                <button onclick="closeSubmissionModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="submissionContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewSubmission(submissionId) {
    // This would typically load submission details via AJAX
    // For now, we'll just show a placeholder
    document.getElementById('submissionContent').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-blue-500 mb-4"></i>
            <p class="text-gray-600">Loading submission details...</p>
        </div>
    `;
    document.getElementById('submissionModal').classList.remove('hidden');
}

function closeSubmissionModal() {
    document.getElementById('submissionModal').classList.add('hidden');
}
</script>
@endsection
