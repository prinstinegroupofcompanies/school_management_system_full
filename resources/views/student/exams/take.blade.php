@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 text-white">
    <!-- Exam Header -->
    <div class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-bold">{{ $exam->title }}</h1>
                    <p class="text-gray-400 text-sm">{{ $exam->subject->name }} • {{ $exam->class->name }}</p>
                </div>
                
                <!-- Timer -->
                <div class="flex items-center space-x-6">
                    <div class="bg-red-600 px-4 py-2 rounded-lg">
                        <div class="text-sm font-medium">Time Remaining</div>
                        <div id="timer" class="text-xl font-bold">{{ $remainingTime }}</div>
                    </div>
                    
                    <!-- Progress -->
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-400">Progress</div>
                        <div class="text-lg font-bold">
                            <span id="answered-count">0</span>/<span id="total-questions">{{ $exam->questions->count() }}</span>
                        </div>
                    </div>
                    
                    <!-- Auto-save indicator -->
                    <div id="save-indicator" class="flex items-center text-green-400">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="text-sm">Saved</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Question Navigation Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-lg p-4 sticky top-24">
                    <h3 class="font-semibold mb-4 text-gray-200">Question Navigation</h3>
                    <div class="grid grid-cols-5 gap-2 mb-6">
                        @foreach($exam->questions as $index => $question)
                        <button type="button" 
                                class="question-nav-btn w-10 h-10 rounded text-sm font-medium transition-colors
                                       bg-gray-700 text-gray-300 hover:bg-gray-600"
                                data-question="{{ $index + 1 }}"
                                onclick="goToQuestion({{ $index + 1 }})">
                            {{ $index + 1 }}
                        </button>
                        @endforeach
                    </div>
                    
                    <!-- Legend -->
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-gray-700 rounded mr-2"></div>
                            <span class="text-gray-400">Not Visited</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-600 rounded mr-2"></div>
                            <span class="text-gray-400">Visited</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-600 rounded mr-2"></div>
                            <span class="text-gray-400">Answered</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-600 rounded mr-2"></div>
                            <span class="text-gray-400">Current</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="button" 
                            onclick="submitExam()"
                            class="w-full mt-6 px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Exam
                    </button>
                </div>
            </div>

            <!-- Main Question Area -->
            <div class="lg:col-span-3">
                <form id="exam-form" class="space-y-6">
                    @csrf
                    <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">
                    
                    @foreach($exam->questions as $index => $question)
                    <div class="question-container bg-gray-800 rounded-lg p-6 {{ $index === 0 ? '' : 'hidden' }}" 
                         data-question="{{ $index + 1 }}">
                        
                        <!-- Question Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-200">
                                    Question {{ $index + 1 }} of {{ $exam->questions->count() }}
                                </h2>
                                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-400">
                                    <span><i class="fas fa-star mr-1"></i>{{ $question->points }} points</span>
                                    <span><i class="fas fa-clock mr-1"></i>~{{ $question->estimated_time_minutes }} min</span>
                                    <span class="px-2 py-1 bg-gray-700 rounded text-xs">
                                        {{ ucfirst($question->difficulty_level) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Question Text -->
                        <div class="mb-6">
                            <div class="text-gray-200 text-lg leading-relaxed">
                                {!! nl2br(e($question->question_text)) !!}
                            </div>
                            
                            @if($question->question_image)
                            <div class="mt-4">
                                <img src="{{ asset('storage/' . $question->question_image) }}" 
                                     alt="Question Image" 
                                     class="max-w-full h-auto rounded-lg">
                            </div>
                            @endif
                        </div>

                        <!-- Answer Options -->
                        <div class="space-y-3">
                            @if($question->question_type === 'multiple_choice')
                                @foreach($question->options as $optionIndex => $option)
                                <label class="flex items-start p-4 bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-600 transition-colors">
                                    <input type="radio" 
                                           name="question_{{ $question->id }}" 
                                           value="{{ $optionIndex }}"
                                           class="mt-1 text-blue-600 focus:ring-blue-500 focus:ring-2"
                                           onchange="saveAnswer({{ $question->id }}, this.value)">
                                    <div class="ml-3 flex-1">
                                        <div class="font-medium text-gray-200">{{ chr(65 + $optionIndex) }}.</div>
                                        <div class="text-gray-300 mt-1">{{ $option }}</div>
                                    </div>
                                </label>
                                @endforeach
                                
                            @elseif($question->question_type === 'true_false')
                                <label class="flex items-start p-4 bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-600 transition-colors">
                                    <input type="radio" 
                                           name="question_{{ $question->id }}" 
                                           value="true"
                                           class="mt-1 text-blue-600 focus:ring-blue-500 focus:ring-2"
                                           onchange="saveAnswer({{ $question->id }}, this.value)">
                                    <div class="ml-3">
                                        <div class="font-medium text-gray-200">True</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-4 bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-600 transition-colors">
                                    <input type="radio" 
                                           name="question_{{ $question->id }}" 
                                           value="false"
                                           class="mt-1 text-blue-600 focus:ring-blue-500 focus:ring-2"
                                           onchange="saveAnswer({{ $question->id }}, this.value)">
                                    <div class="ml-3">
                                        <div class="font-medium text-gray-200">False</div>
                                    </div>
                                </label>
                                
                            @elseif($question->question_type === 'short_answer')
                                <div>
                                    <textarea name="question_{{ $question->id }}" 
                                              rows="4"
                                              class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="Type your answer here..."
                                              onchange="saveAnswer({{ $question->id }}, this.value)"
                                              oninput="saveAnswer({{ $question->id }}, this.value)"></textarea>
                                </div>
                            @endif
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-700">
                            <button type="button" 
                                    onclick="previousQuestion()"
                                    class="px-6 py-2 bg-gray-700 text-gray-300 font-medium rounded-lg hover:bg-gray-600 transition-colors {{ $index === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $index === 0 ? 'disabled' : '' }}>
                                <i class="fas fa-chevron-left mr-2"></i>
                                Previous
                            </button>
                            
                            <div class="flex space-x-3">
                                <button type="button" 
                                        onclick="markForReview({{ $index + 1 }})"
                                        class="px-4 py-2 bg-yellow-600 text-white font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                                    <i class="fas fa-flag mr-2"></i>
                                    Mark for Review
                                </button>
                                
                                @if($index < $exam->questions->count() - 1)
                                <button type="button" 
                                        onclick="nextQuestion()"
                                        class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    Next
                                    <i class="fas fa-chevron-right ml-2"></i>
                                </button>
                                @else
                                <button type="button" 
                                        onclick="submitExam()"
                                        class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                                    Submit Exam
                                    <i class="fas fa-paper-plane ml-2"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div id="submit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-200 mb-4">Submit Exam?</h3>
            <p class="text-gray-400 mb-6">
                Are you sure you want to submit your exam? You have answered 
                <span id="final-answered-count">0</span> out of {{ $exam->questions->count() }} questions.
                This action cannot be undone.
            </p>
            <div class="flex space-x-3">
                <button type="button" 
                        onclick="closeSubmitModal()"
                        class="flex-1 px-4 py-2 bg-gray-700 text-gray-300 font-medium rounded-lg hover:bg-gray-600 transition-colors">
                    Continue Exam
                </button>
                <button type="button" 
                        onclick="confirmSubmit()"
                        class="flex-1 px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                    Submit Now
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentQuestion = 1;
let totalQuestions = {{ $exam->questions->count() }};
let answeredQuestions = new Set();
let timeRemaining = {{ $remainingTimeSeconds }};
let autoSaveTimeout;
let examSubmitted = false;

// Initialize exam
document.addEventListener('DOMContentLoaded', function() {
    startTimer();
    loadSavedAnswers();
    updateNavigation();
    
    // Auto-submit when time runs out
    if (timeRemaining <= 0) {
        autoSubmitExam();
    }
    
    // Prevent page refresh/close without warning
    window.addEventListener('beforeunload', function(e) {
        if (!examSubmitted) {
            e.preventDefault();
            e.returnValue = 'Your exam progress will be lost. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
});

// Timer functionality
function startTimer() {
    const timerElement = document.getElementById('timer');
    
    const timer = setInterval(function() {
        if (timeRemaining <= 0) {
            clearInterval(timer);
            autoSubmitExam();
            return;
        }
        
        const hours = Math.floor(timeRemaining / 3600);
        const minutes = Math.floor((timeRemaining % 3600) / 60);
        const seconds = timeRemaining % 60;
        
        const display = hours > 0 
            ? `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
            : `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
        timerElement.textContent = display;
        
        // Warning colors
        if (timeRemaining <= 300) { // 5 minutes
            timerElement.parentElement.className = 'bg-red-600 px-4 py-2 rounded-lg animate-pulse';
        } else if (timeRemaining <= 900) { // 15 minutes
            timerElement.parentElement.className = 'bg-yellow-600 px-4 py-2 rounded-lg';
        }
        
        timeRemaining--;
    }, 1000);
}

// Navigation functions
function goToQuestion(questionNumber) {
    if (questionNumber < 1 || questionNumber > totalQuestions) return;
    
    // Hide current question
    document.querySelector(`.question-container[data-question="${currentQuestion}"]`).classList.add('hidden');
    
    // Show target question
    document.querySelector(`.question-container[data-question="${questionNumber}"]`).classList.remove('hidden');
    
    currentQuestion = questionNumber;
    updateNavigation();
    markQuestionAsVisited(questionNumber);
}

function nextQuestion() {
    if (currentQuestion < totalQuestions) {
        goToQuestion(currentQuestion + 1);
    }
}

function previousQuestion() {
    if (currentQuestion > 1) {
        goToQuestion(currentQuestion - 1);
    }
}

function markQuestionAsVisited(questionNumber) {
    const navBtn = document.querySelector(`.question-nav-btn[data-question="${questionNumber}"]`);
    if (navBtn && !navBtn.classList.contains('bg-green-600') && !navBtn.classList.contains('bg-blue-600')) {
        navBtn.classList.remove('bg-gray-700');
        navBtn.classList.add('bg-yellow-600');
    }
}

function updateNavigation() {
    // Update current question indicator
    document.querySelectorAll('.question-nav-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600');
        if (parseInt(btn.dataset.question) === currentQuestion) {
            btn.classList.remove('bg-gray-700', 'bg-yellow-600', 'bg-green-600');
            btn.classList.add('bg-blue-600');
        }
    });
    
    // Update answered count
    document.getElementById('answered-count').textContent = answeredQuestions.size;
}

// Answer saving
function saveAnswer(questionId, answer) {
    clearTimeout(autoSaveTimeout);
    
    // Show saving indicator
    const indicator = document.getElementById('save-indicator');
    indicator.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span class="text-sm">Saving...</span>';
    indicator.className = 'flex items-center text-yellow-400';
    
    autoSaveTimeout = setTimeout(function() {
        // AJAX call to save answer
        fetch('{{ route("student.exams.save-answer") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                attempt_id: {{ $attempt->id }},
                question_id: questionId,
                answer: answer
            })
        }).then(response => {
            if (response.ok) {
                // Mark question as answered
                answeredQuestions.add(questionId);
                
                // Update navigation button
                const navBtn = document.querySelector(`.question-nav-btn[data-question="${currentQuestion}"]`);
                navBtn.classList.remove('bg-gray-700', 'bg-yellow-600', 'bg-blue-600');
                navBtn.classList.add('bg-green-600');
                
                // Show saved indicator
                indicator.innerHTML = '<i class="fas fa-check-circle mr-2"></i><span class="text-sm">Saved</span>';
                indicator.className = 'flex items-center text-green-400';
                
                updateNavigation();
            } else {
                // Show error indicator
                indicator.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i><span class="text-sm">Save Failed</span>';
                indicator.className = 'flex items-center text-red-400';
            }
        }).catch(error => {
            console.error('Save error:', error);
            indicator.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i><span class="text-sm">Save Failed</span>';
            indicator.className = 'flex items-center text-red-400';
        });
    }, 1000); // Auto-save after 1 second of inactivity
}

// Load previously saved answers
function loadSavedAnswers() {
    fetch(`{{ route("student.exams.get-answers", $attempt) }}`)
        .then(response => response.json())
        .then(data => {
            Object.keys(data).forEach(questionId => {
                const answer = data[questionId];
                answeredQuestions.add(parseInt(questionId));
                
                // Populate form fields
                const inputs = document.querySelectorAll(`[name="question_${questionId}"]`);
                inputs.forEach(input => {
                    if (input.type === 'radio' && input.value === answer) {
                        input.checked = true;
                    } else if (input.type === 'textarea') {
                        input.value = answer;
                    }
                });
            });
            
            updateNavigation();
            
            // Update navigation buttons for answered questions
            answeredQuestions.forEach(questionId => {
                // Find which question number this is
                const questionElement = document.querySelector(`[name="question_${questionId}"]`);
                if (questionElement) {
                    const container = questionElement.closest('.question-container');
                    const questionNumber = container.dataset.question;
                    const navBtn = document.querySelector(`.question-nav-btn[data-question="${questionNumber}"]`);
                    if (navBtn && questionNumber != currentQuestion) {
                        navBtn.classList.remove('bg-gray-700', 'bg-yellow-600');
                        navBtn.classList.add('bg-green-600');
                    }
                }
            });
        });
}

// Exam submission
function submitExam() {
    document.getElementById('final-answered-count').textContent = answeredQuestions.size;
    document.getElementById('submit-modal').classList.remove('hidden');
}

function closeSubmitModal() {
    document.getElementById('submit-modal').classList.add('hidden');
}

function confirmSubmit() {
    examSubmitted = true;
    
    fetch('{{ route("student.exams.submit", $attempt) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    }).then(response => {
        if (response.ok) {
            window.location.href = '{{ route("student.exams.result", $attempt) }}';
        } else {
            alert('Error submitting exam. Please try again.');
            closeSubmitModal();
            examSubmitted = false;
        }
    });
}

function autoSubmitExam() {
    examSubmitted = true;
    alert('Time is up! Your exam will be submitted automatically.');
    confirmSubmit();
}

function markForReview(questionNumber) {
    // Visual indication for review
    const navBtn = document.querySelector(`.question-nav-btn[data-question="${questionNumber}"]`);
    navBtn.style.border = '2px solid #f59e0b';
    
    // You could also save this to the server
}
</script>
@endsection