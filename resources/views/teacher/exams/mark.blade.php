@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Mark Exam</h1>
                        <h2 class="text-xl text-gray-700 mt-1">{{ $attempt->examSchedule->title }}</h2>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">Student: {{ $attempt->student->user->name }}</div>
                        <div class="text-sm text-gray-600">Subject: {{ $attempt->examSchedule->subject->name ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">Class: {{ $attempt->examSchedule->class->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="bg-white rounded-lg shadow-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Student Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Submission Details</h4>
                        <div class="space-y-1 text-sm text-gray-600">
                            <div>Started: {{ $attempt->started_at->format('M d, Y \a\t H:i') }}</div>
                            <div>Submitted: {{ $attempt->submitted_at->format('M d, Y \a\t H:i') }}</div>
                            <div>Duration: {{ $attempt->started_at->diffForHumans($attempt->submitted_at, true) }}</div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Exam Details</h4>
                        <div class="space-y-1 text-sm text-gray-600">
                            <div>Total Questions: {{ $attempt->answers->count() }}</div>
                            <div>Total Marks: {{ $attempt->examSchedule->total_marks ?? 100 }}</div>
                            <div>Passing Marks: {{ $attempt->examSchedule->passing_marks ?? 50 }}</div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Current Status</h4>
                        <div class="space-y-1 text-sm text-gray-600">
                            <div>Status: <span class="font-semibold text-yellow-600">Awaiting Marking</span></div>
                            <div>Questions Answered: {{ $attempt->answers->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marking Form -->
        <form action="{{ route('teacher.exams.mark.store', $attempt) }}" method="POST" id="markingForm">
            @csrf
            
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Mark Questions</h3>
                </div>
                
                <div class="p-6">
                    @foreach($attempt->answers as $index => $answer)
                    <div class="mb-8 p-6 border border-gray-200 rounded-lg">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">
                                    Question {{ $index + 1 }} ({{ $answer->question->marks }} marks)
                                </h4>
                                <p class="text-gray-700 mb-4">{{ $answer->question->question_text }}</p>
                            </div>
                        </div>

                        <!-- Student's Answer -->
                        <div class="mb-4">
                            <h5 class="text-sm font-semibold text-gray-700 mb-2">Student's Answer:</h5>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                @if($answer->question->type === 'mcq')
                                    <span class="text-gray-900 font-medium">{{ $answer->answer_text }}</span>
                                @else
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $answer->answer_text }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Correct Answer (if MCQ) -->
                        @if($answer->question->type === 'mcq' && $answer->question->correct_answer)
                        <div class="mb-4">
                            <h5 class="text-sm font-semibold text-gray-700 mb-2">Correct Answer:</h5>
                            <div class="p-4 bg-green-50 rounded-lg">
                                <span class="text-green-900 font-medium">{{ $answer->question->correct_answer }}</span>
                            </div>
                        </div>
                        @endif

                        <!-- Marking Input -->
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <label for="marks_{{ $answer->id }}" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Marks Awarded (0 - {{ $answer->question->marks }}):
                                </label>
                                <input type="number" 
                                       name="answers[{{ $answer->id }}][marks_awarded]" 
                                       id="marks_{{ $answer->id }}"
                                       value="{{ old('answers.'.$answer->id.'.marks_awarded', $answer->marks_awarded) }}"
                                       min="0" 
                                       max="{{ $answer->question->marks }}" 
                                       step="0.5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                                @error('answers.'.$answer->id.'.marks_awarded')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex space-x-2">
                                <button type="button" 
                                        onclick="setMarks({{ $answer->id }}, 0)" 
                                        class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                    0
                                </button>
                                <button type="button" 
                                        onclick="setMarks({{ $answer->id }}, {{ $answer->question->marks / 2 }})" 
                                        class="px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors">
                                    Half
                                </button>
                                <button type="button" 
                                        onclick="setMarks({{ $answer->id }}, {{ $answer->question->marks }})" 
                                        class="px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                                    Full
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-center space-x-4">
                <a href="{{ route('teacher.exams.submissions', $attempt->examSchedule) }}" 
                   class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Submissions
                </a>
                <button type="submit" 
                        class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition-colors text-lg font-semibold">
                    Submit Marks
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function setMarks(answerId, marks) {
    document.getElementById('marks_' + answerId).value = marks;
}

// Auto-save functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('markingForm');
    const inputs = form.querySelectorAll('input[type="number"]');
    
    // Auto-save every 30 seconds
    setInterval(function() {
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        localStorage.setItem('marking_draft_{{ $attempt->id }}', JSON.stringify(data));
    }, 30000);
    
    // Load saved data
    const savedData = localStorage.getItem('marking_draft_{{ $attempt->id }}');
    if (savedData) {
        const data = JSON.parse(savedData);
        inputs.forEach(input => {
            if (data[input.name]) {
                input.value = data[input.name];
            }
        });
    }
    
    // Clear saved data on successful submit
    form.addEventListener('submit', function() {
        localStorage.removeItem('marking_draft_{{ $attempt->id }}');
    });
});

// Calculate total marks
function calculateTotal() {
    let total = 0;
    document.querySelectorAll('input[type="number"]').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    return total;
}

// Update total display
document.addEventListener('input', function(e) {
    if (e.target.type === 'number') {
        const total = calculateTotal();
        console.log('Total marks:', total);
    }
});
</script>
@endsection
