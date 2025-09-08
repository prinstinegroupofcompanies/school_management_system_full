@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Exam Submissions</h1>
                        <h2 class="text-xl text-gray-700 mt-1">{{ $examSchedule->title }}</h2>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">Subject: {{ $examSchedule->subject->name ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">Class: {{ $examSchedule->class->name ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">Date: {{ $examSchedule->start_date->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Total Students</div>
                        <div class="text-2xl font-semibold text-gray-900">{{ $examSchedule->class->students->count() ?? 0 }}</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Submitted</div>
                        <div class="text-2xl font-semibold text-gray-900">{{ $attempts->where('status', 'submitted')->count() }}</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Graded</div>
                        <div class="text-2xl font-semibold text-gray-900">{{ $attempts->where('status', 'graded')->count() }}</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Not Submitted</div>
                        <div class="text-2xl font-semibold text-gray-900">{{ ($examSchedule->class->students->count() ?? 0) - $attempts->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Student Submissions</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attempts as $attempt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $attempt->student->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $attempt->student->student_id ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y H:i') : 'Not submitted' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($attempt->started_at && $attempt->submitted_at)
                                    {{ $attempt->started_at->diffForHumans($attempt->submitted_at, true) }}
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($attempt->status === 'submitted')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Awaiting Marking
                                </span>
                            @elseif($attempt->status === 'graded')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Graded
                                </span>
                            @elseif($attempt->status === 'in_progress')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    In Progress
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Not Started
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($attempt->status === 'graded')
                                <div class="text-sm text-gray-900">
                                    {{ $attempt->score }}/{{ $attempt->examSchedule->total_marks ?? 100 }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    @php
                                        $percentage = $attempt->examSchedule->total_marks ? 
                                            round(($attempt->score / $attempt->examSchedule->total_marks) * 100, 2) : 0;
                                    @endphp
                                    {{ $percentage }}%
                                </div>
                            @else
                                <div class="text-sm text-gray-500">-</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($attempt->status === 'submitted')
                                <a href="{{ route('teacher.exams.mark', $attempt) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">Mark Exam</a>
                            @elseif($attempt->status === 'graded')
                                <a href="{{ route('teacher.exams.mark', $attempt) }}" 
                                   class="text-green-600 hover:text-green-900 mr-3">View Marks</a>
                            @elseif($attempt->status === 'in_progress')
                                <span class="text-gray-500">In Progress</span>
                            @else
                                <span class="text-gray-500">Not Started</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="text-gray-400 text-6xl mb-4">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Submissions Yet</h3>
                            <p class="text-gray-600">No students have submitted this exam yet.</p>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ route('teacher.exams.index') }}" 
               class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                Back to Exams
            </a>
            @if($attempts->where('status', 'submitted')->count() > 0)
            <button onclick="markAllSubmissions()" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Mark All Submissions
            </button>
            @endif
        </div>
    </div>
</div>

<script>
function markAllSubmissions() {
    const submittedAttempts = {{ $attempts->where('status', 'submitted')->pluck('id')->toJson() }};
    if (submittedAttempts.length === 0) {
        alert('No submissions to mark.');
        return;
    }
    
    if (confirm(`Mark all ${submittedAttempts.length} submitted exams?`)) {
        submittedAttempts.forEach(attemptId => {
            window.open(`{{ url('/teacher/exams/attempts') }}/${attemptId}/mark`, '_blank');
        });
    }
}
</script>
@endsection
