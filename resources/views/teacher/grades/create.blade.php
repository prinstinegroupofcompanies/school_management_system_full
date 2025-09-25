@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Enter Grades</h1>
                <a href="{{ route('teacher.grades.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Grades
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form id="gradeForm" action="{{ route('teacher.grades.store') }}" method="POST">
                    @csrf
                    
                    <!-- Class and Subject Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                            <select name="class_id" id="class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="">Select a class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} - {{ $class->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                            <select name="subject_id" id="subject_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="">Select a subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ $selectedSubjectId == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} - {{ $subject->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Student Selection and Grade Entry -->
                    <div id="students-section" class="{{ $students->count() > 0 ? '' : 'hidden' }}">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Student Grades</h3>
                        
                        <div class="space-y-4">
                            @foreach($students as $student)
                                <div class="border rounded-lg p-4 bg-gray-50">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-medium text-gray-900">
                                            {{ $student->user->name }} ({{ $student->admission_number }})
                                        </h4>
                                        <input type="checkbox" class="student-select" data-student-id="{{ $student->id }}" checked>
                                    </div>
                                    
                                    <input type="hidden" name="students[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <!-- Semester 1 -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Semester 1 - Period 1</label>
                                            <input type="number" name="students[{{ $student->id }}][sem1_p1]" 
                                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                                   min="0" max="100" step="0.01">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Semester 1 - Period 2</label>
                                            <input type="number" name="students[{{ $student->id }}][sem1_p2]" 
                                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                                   min="0" max="100" step="0.01">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Semester 1 - Period 3</label>
                                            <input type="number" name="students[{{ $student->id }}][sem1_p3]" 
                                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                                   min="0" max="100" step="0.01">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Semester 1 - Exam</label>
                                            <input type="number" name="students[{{ $student->id }}][sem1_exam]" 
                                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                                   min="0" max="100" step="0.01">
                                        </div>
                                        
                                        <!-- Semester 2 -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Semester 2 - Period 4</label>
                                            <input type="number" name="students[{{ $student->id }}][sem2_p4]" 
                                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                                   min="0" max="100" step="0.01">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Semester 2 - Period 5</label>
                                            <input type="number" name="students[{{ $student->id }}][sem2_p5]" 
                                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                                   min="0" max="100" step="0.01">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Semester 2 - Period 6</label>
                                            <input type="number" name="students[{{ $student->id }}][sem2_p6]" 
                                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                                   min="0" max="100" step="0.01">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Semester 2 - Exam</label>
                                            <input type="number" name="students[{{ $student->id }}][sem2_exam]" 
                                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                                   min="0" max="100" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Save Grades
                            </button>
                        </div>
                    </div>

                    <div id="no-students" class="{{ $students->count() == 0 ? '' : 'hidden' }} text-center py-8">
                        <p class="text-gray-500">Please select a class and subject to view students.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');
    const studentsSection = document.getElementById('students-section');
    const noStudentsSection = document.getElementById('no-students');

    // Load students when class and subject are selected
    function loadStudents() {
        const classId = classSelect.value;
        const subjectId = subjectSelect.value;
        
        if (classId && subjectId) {
            // Redirect to the same page with selected class and subject
            const url = new URL(window.location);
            url.searchParams.set('class_id', classId);
            url.searchParams.set('subject_id', subjectId);
            window.location.href = url.toString();
        } else {
            studentsSection.classList.add('hidden');
            noStudentsSection.classList.remove('hidden');
        }
    }

    classSelect.addEventListener('change', loadStudents);
    subjectSelect.addEventListener('change', loadStudents);

    // Handle student selection checkboxes
    document.querySelectorAll('.student-select').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const studentId = this.dataset.studentId;
            const studentContainer = this.closest('.border');
            const inputs = studentContainer.querySelectorAll('input[type="number"]');
            
            inputs.forEach(input => {
                input.disabled = !this.checked;
                if (!this.checked) {
                    input.value = '';
                }
            });
        });
    });

    // Form submission - only include selected students
    document.getElementById('gradeForm').addEventListener('submit', function(e) {
        const selectedStudents = Array.from(document.querySelectorAll('.student-select:checked'))
            .map(checkbox => checkbox.dataset.studentId);
        
        if (selectedStudents.length === 0) {
            e.preventDefault();
            alert('Please select at least one student.');
            return;
        }
        
        // Remove unchecked students from form data
        document.querySelectorAll('.student-select:not(:checked)').forEach(checkbox => {
            const studentId = checkbox.dataset.studentId;
            const studentContainer = checkbox.closest('.border');
            const inputs = studentContainer.querySelectorAll('input');
            inputs.forEach(input => input.remove());
        });
    });
});
</script>
@endsection