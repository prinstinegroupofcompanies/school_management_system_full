@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Exam Header -->
        <div class="bg-white rounded-lg shadow-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $examSchedule->title }}</h1>
                        <div class="mt-2 text-sm text-gray-600">
                            <div class="flex items-center space-x-4">
                                <span><i class="fas fa-book mr-2"></i>{{ $examSchedule->subject->name ?? 'Subject' }}</span>
                                <span><i class="fas fa-users mr-2"></i>{{ $examSchedule->class->name ?? 'Class' }}</span>
                                <span><i class="fas fa-calendar mr-2"></i>{{ $examSchedule->start_date->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">Total Marks: {{ $examSchedule->total_marks ?? 100 }}</div>
                        <div class="text-sm text-gray-600">Questions: {{ $questions->count() }}</div>
                    </div>
                </div>
            </div>
            
            @if($examSchedule->instructions)
            <div class="px-6 py-4 bg-blue-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">Instructions:</h3>
                <p class="text-sm text-blue-800">{{ $examSchedule->instructions }}</p>
            </div>
            @endif
        </div>

        <!-- Exam Form -->
        <form action="{{ route('student.exams.submit', $examSchedule) }}" method="POST" id="examForm">
            @csrf
            
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Questions</h2>
                </div>
                
                <div class="p-6">
                    @foreach($questions as $index => $question)
                    <div class="mb-8 p-6 border border-gray-200 rounded-lg">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                Question {{ $index + 1 }} ({{ $question->marks }} marks)
                            </h3>
                            <p class="text-gray-700">{{ $question->question_text }}</p>
                        </div>

                        @if($question->type === 'mcq' && $question->options)
                        <div class="space-y-3">
                            @foreach($question->options as $optionIndex => $option)
                            <div class="flex items-center">
                                <input type="radio" 
                                       name="answers[{{ $question->id }}]" 
                                       value="{{ $option }}" 
                                       id="q{{ $question->id }}_{{ $optionIndex }}"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                       @if($attempt && $attempt->answers->where('exam_question_id', $question->id)->first())
                                           {{ $attempt->answers->where('exam_question_id', $question->id)->first()->answer_text === $option ? 'checked' : '' }}
                                       @endif>
                                <label for="q{{ $question->id }}_{{ $optionIndex }}" class="ml-3 text-sm text-gray-700">
                                    {{ $option }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div>
                            <textarea name="answers[{{ $question->id }}]" 
                                      rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Enter your answer here...">@if($attempt && $attempt->answers->where('exam_question_id', $question->id)->first()){{ $attempt->answers->where('exam_question_id', $question->id)->first()->answer_text }}@endif</textarea>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-center">
                <button type="submit" 
                        class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition-colors text-lg font-semibold"
                        onclick="return confirm('Are you sure you want to submit your exam? This action cannot be undone.')">
                    Submit Exam
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-save functionality (optional)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('examForm');
    const inputs = form.querySelectorAll('input, textarea');
    
    // Auto-save every 30 seconds
    setInterval(function() {
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        localStorage.setItem('exam_draft_{{ $examSchedule->id }}', JSON.stringify(data));
    }, 30000);
    
    // Load saved data
    const savedData = localStorage.getItem('exam_draft_{{ $examSchedule->id }}');
    if (savedData) {
        const data = JSON.parse(savedData);
        inputs.forEach(input => {
            if (data[input.name]) {
                if (input.type === 'radio') {
                    input.checked = input.value === data[input.name];
                } else {
                    input.value = data[input.name];
                }
            }
        });
    }
    
    // Clear saved data on successful submit
    form.addEventListener('submit', function() {
        localStorage.removeItem('exam_draft_{{ $examSchedule->id }}');
    });
});
</script>
@endsection
