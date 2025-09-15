@extends('layouts.app')

@section('title', 'Create Exam Questions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Create Exam Questions</h1>
        
        <form method="POST" action="{{ route('teacher.grades.exam-questions.store') }}" class="space-y-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Exam Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Class</label>
                        <select name="class_id" id="filter_class_id" class="mt-1 block w-full border-gray-300 rounded-md" onchange="onClassChange(this)" required>
                            <option value="">Select class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (isset($selectedClassId) && (int)$selectedClassId === $class->id) ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subject</label>
                        <select name="subject_id" id="subject_id" class="mt-1 block w-full border-gray-300 rounded-md" required onchange="onSubjectChange(this)">
                            <option value="">Select subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ (isset($selectedSubjectId) && (int)$selectedSubjectId === $subject->id) ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Exam Title</label>
                        <input type="text" name="title" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="e.g., Mid-term Mathematics Exam" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Marks</label>
                        <input type="number" name="total_marks" class="mt-1 block w-full border-gray-300 rounded-md" min="1" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" class="mt-1 block w-full border-gray-300 rounded-md" min="1" value="60" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Exam Date (Optional)</label>
                        <input type="datetime-local" name="exam_date" class="mt-1 block w-full border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="Exam instructions or additional information..."></textarea>
                </div>
            </div>

            <!-- Questions Section -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Exam Questions</h2>
                    <button type="button" onclick="addQuestion()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Add Question
                    </button>
                </div>
                
                <div id="questions-container">
                    <!-- Questions will be added here dynamically -->
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('teacher.grades.exam-questions') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Create Exam Questions
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let questionCount = 0;

function addQuestion() {
    questionCount++;
    const container = document.getElementById('questions-container');
    
    const questionDiv = document.createElement('div');
    questionDiv.className = 'border border-gray-300 rounded-lg p-4 mb-4 bg-white';
    questionDiv.innerHTML = `
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-md font-medium text-gray-900">Question ${questionCount}</h3>
            <button type="button" onclick="removeQuestion(this)" class="text-red-600 hover:text-red-800">
                Remove
            </button>
        </div>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Question Text</label>
                <textarea name="questions[${questionCount}][question]" rows="3" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="Enter the question..." required></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Question Type</label>
                    <select name="questions[${questionCount}][type]" class="mt-1 block w-full border-gray-300 rounded-md" onchange="toggleOptions(${questionCount})" required>
                        <option value="">Select type</option>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="short_answer">Short Answer</option>
                        <option value="essay">Essay</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Marks</label>
                    <input type="number" name="questions[${questionCount}][marks]" class="mt-1 block w-full border-gray-300 rounded-md" min="1" required>
                </div>
            </div>
            
            <div id="options-${questionCount}" class="hidden">
                <label class="block text-sm font-medium text-gray-700">Options (one per line)</label>
                <textarea name="questions[${questionCount}][options]" rows="4" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="Option A&#10;Option B&#10;Option C&#10;Option D"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Correct Answer</label>
                <input type="text" name="questions[${questionCount}][correct_answer]" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="Enter the correct answer..." required>
            </div>
        </div>
    `;
    
    container.appendChild(questionDiv);
}

function removeQuestion(button) {
    button.closest('.border').remove();
}

function toggleOptions(questionNum) {
    const select = document.querySelector(`select[name="questions[${questionNum}][type]"]`);
    const optionsDiv = document.getElementById(`options-${questionNum}`);
    
    if (select.value === 'multiple_choice') {
        optionsDiv.classList.remove('hidden');
        optionsDiv.querySelector('textarea').required = true;
    } else {
        optionsDiv.classList.add('hidden');
        optionsDiv.querySelector('textarea').required = false;
    }
}

function onClassChange(sel) {
    const classId = sel.value;
    const subjectSelect = document.getElementById('subject_id');
    
    // Clear subject dropdown
    subjectSelect.innerHTML = '<option value="">Select subject</option>';
    
    if (classId) {
        // Load subjects for selected class
        fetch(`{{ route('teacher.grades.subjects') }}?class_id=${classId}`)
            .then(response => response.json())
            .then(data => {
                data.subjects.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.id;
                    option.textContent = subject.name;
                    subjectSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading subjects:', error));
    }
}

function onSubjectChange(sel) {
    // Subject change logic if needed
}

// Add first question by default
document.addEventListener('DOMContentLoaded', function() {
    addQuestion();
});
</script>
@endsection
