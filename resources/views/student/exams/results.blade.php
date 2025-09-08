@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Results Header -->
        <div class="bg-white rounded-lg shadow-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Exam Results</h1>
                        <h2 class="text-xl text-gray-700 mt-1">{{ $attempt->examSchedule->title }}</h2>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">Subject: {{ $attempt->examSchedule->subject->name ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">Class: {{ $attempt->examSchedule->class->name ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">Date: {{ $attempt->examSchedule->start_date->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Score Summary -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg mb-6 text-white">
            <div class="px-6 py-8">
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2">{{ $attempt->score }}</div>
                    <div class="text-xl mb-4">out of {{ $attempt->examSchedule->total_marks ?? 100 }} marks</div>
                    <div class="text-lg">
                        @php
                            $percentage = $attempt->examSchedule->total_marks ? 
                                round(($attempt->score / $attempt->examSchedule->total_marks) * 100, 2) : 0;
                        @endphp
                        {{ $percentage }}%
                    </div>
                    <div class="mt-4">
                        @if($percentage >= 80)
                            <span class="bg-green-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                                Excellent!
                            </span>
                        @elseif($percentage >= 70)
                            <span class="bg-blue-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                                Good!
                            </span>
                        @elseif($percentage >= 60)
                            <span class="bg-yellow-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                                Satisfactory
                            </span>
                        @elseif($percentage >= 50)
                            <span class="bg-orange-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                                Needs Improvement
                            </span>
                        @else
                            <span class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                                Failed
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Results -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Detailed Results</h3>
            </div>
            
            <div class="p-6">
                @foreach($attempt->answers as $index => $answer)
                <div class="mb-6 p-6 border border-gray-200 rounded-lg">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">
                                Question {{ $index + 1 }} ({{ $answer->question->marks }} marks)
                            </h4>
                            <p class="text-gray-700 mb-3">{{ $answer->question->question_text }}</p>
                        </div>
                        <div class="ml-4 text-right">
                            <div class="text-lg font-semibold text-gray-900">
                                {{ $answer->marks_awarded }}/{{ $answer->question->marks }}
                            </div>
                            @if($answer->question->marks > 0)
                            <div class="text-sm text-gray-600">
                                {{ round(($answer->marks_awarded / $answer->question->marks) * 100) }}%
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Student's Answer -->
                    <div class="mb-4">
                        <h5 class="text-sm font-semibold text-gray-700 mb-2">Your Answer:</h5>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            @if($answer->question->type === 'mcq')
                                <span class="text-gray-900">{{ $answer->answer_text }}</span>
                            @else
                                <p class="text-gray-900">{{ $answer->answer_text }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Correct Answer (if MCQ) -->
                    @if($answer->question->type === 'mcq' && $answer->question->correct_answer)
                    <div class="mb-4">
                        <h5 class="text-sm font-semibold text-gray-700 mb-2">Correct Answer:</h5>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <span class="text-green-900">{{ $answer->question->correct_answer }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Answer Status -->
                    <div class="flex items-center">
                        @if($answer->marks_awarded == $answer->question->marks)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-2"></i>Correct
                            </span>
                        @elseif($answer->marks_awarded > 0)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-check-circle mr-2"></i>Partially Correct
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times mr-2"></i>Incorrect
                            </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Exam Information -->
        <div class="bg-white rounded-lg shadow-lg mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Exam Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Submission Details</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div>Started: {{ $attempt->started_at->format('M d, Y \a\t H:i') }}</div>
                            <div>Submitted: {{ $attempt->submitted_at->format('M d, Y \a\t H:i') }}</div>
                            <div>Duration: {{ $attempt->started_at->diffForHumans($attempt->submitted_at, true) }}</div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Exam Details</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div>Total Questions: {{ $attempt->answers->count() }}</div>
                            <div>Total Marks: {{ $attempt->examSchedule->total_marks ?? 100 }}</div>
                            <div>Passing Marks: {{ $attempt->examSchedule->passing_marks ?? 50 }}</div>
                            <div>Status: 
                                <span class="font-semibold {{ $percentage >= ($attempt->examSchedule->passing_marks ?? 50) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $percentage >= ($attempt->examSchedule->passing_marks ?? 50) ? 'Passed' : 'Failed' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ route('student.exams.index') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Back to Exams
            </a>
            <button onclick="window.print()" 
                    class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                Print Results
            </button>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
