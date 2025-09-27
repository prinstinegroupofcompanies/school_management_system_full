@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Add Grade</h1>
                <a href="{{ route('teacher.grades.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Grades
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form method="POST" action="{{ route('teacher.grades.store') }}" id="gradeForm">
                    @csrf
                    
                    <!-- Class and Subject Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Class <span class="text-red-500">*</span></label>
                            <select name="class_id" id="class_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">Subject <span class="text-red-500">*</span></label>
                            <select name="subject_id" id="subject_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ $selectedSubjectId == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Load Students Button -->
                    <div class="mb-8">
                        <button type="button" id="loadStudentsBtn" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200" disabled>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Load Students
                        </button>
                    </div>

                    <!-- Students Grades Form -->
                    <div id="studentsForm" class="hidden">
                        <div class="border-t pt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-6">Enter Grades for Students</h3>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester 1</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester 2</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th class="px-6 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <div class="grid grid-cols-4 gap-2">
                                                    <span>P1</span>
                                                    <span>P2</span>
                                                    <span>P3</span>
                                                    <span>Exam</span>
                                                </div>
                                            </th>
                                            <th class="px-6 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <div class="grid grid-cols-4 gap-2">
                                                    <span>P4</span>
                                                    <span>P5</span>
                                                    <span>P6</span>
                                                    <span>Exam</span>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentsTableBody" class="bg-white divide-y divide-gray-200">
                                        <!-- Students will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-8 flex justify-end space-x-3">
                                <button type="button" id="cancelBtn" class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-md transition-colors duration-200">
                                    Cancel
                                </button>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Save Grades
                                </button>
                            </div>
                        </div>
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
    const loadStudentsBtn = document.getElementById('loadStudentsBtn');
    const studentsForm = document.getElementById('studentsForm');
    const studentsTableBody = document.getElementById('studentsTableBody');
    const cancelBtn = document.getElementById('cancelBtn');

    // Enable/disable load students button
    function updateLoadButton() {
        const classSelected = classSelect.value;
        const subjectSelected = subjectSelect.value;
        loadStudentsBtn.disabled = !classSelected || !subjectSelected;
    }

    classSelect.addEventListener('change', updateLoadButton);
    subjectSelect.addEventListener('change', updateLoadButton);

    // Load students when button is clicked
    loadStudentsBtn.addEventListener('click', function() {
        const classId = classSelect.value;
        const subjectId = subjectSelect.value;

        if (!classId || !subjectId) {
            alert('Please select both class and subject');
            return;
        }

        // Show loading state
        loadStudentsBtn.disabled = true;
        loadStudentsBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading...';

        // Fetch students
        fetch(`/teacher/grades/students?class_id=${classId}&subject_id=${subjectId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Clear previous students
            studentsTableBody.innerHTML = '';

            // Add students to table
            data.forEach(student => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ${student.name}
                        <input type="hidden" name="students[${student.id}][student_id]" value="${student.id}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="grid grid-cols-4 gap-2">
                            <input type="number" name="students[${student.id}][sem1_p1]" min="0" max="100" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="P1">
                            <input type="number" name="students[${student.id}][sem1_p2]" min="0" max="100" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="P2">
                            <input type="number" name="students[${student.id}][sem1_p3]" min="0" max="100" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="P3">
                            <input type="number" name="students[${student.id}][sem1_exam]" min="0" max="100" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Exam">
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="grid grid-cols-4 gap-2">
                            <input type="number" name="students[${student.id}][sem2_p4]" min="0" max="100" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="P4">
                            <input type="number" name="students[${student.id}][sem2_p5]" min="0" max="100" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="P5">
                            <input type="number" name="students[${student.id}][sem2_p6]" min="0" max="100" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="P6">
                            <input type="number" name="students[${student.id}][sem2_exam]" min="0" max="100" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Exam">
                        </div>
                    </td>
                `;
                studentsTableBody.appendChild(row);
            });

            // Show the form
            studentsForm.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load students');
        })
        .finally(() => {
            // Reset button
            loadStudentsBtn.disabled = false;
            loadStudentsBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>Load Students';
        });
    });

    // Cancel button
    cancelBtn.addEventListener('click', function() {
        studentsForm.classList.add('hidden');
        studentsTableBody.innerHTML = '';
    });

    // Form submission
    document.getElementById('gradeForm').addEventListener('submit', function(e) {
        const students = studentsTableBody.querySelectorAll('tr');
        if (students.length === 0) {
            e.preventDefault();
            alert('Please load students first');
            return;
        }

        // Check if at least one grade is entered
        let hasGrades = false;
        students.forEach(row => {
            const inputs = row.querySelectorAll('input[type="number"]');
            inputs.forEach(input => {
                if (input.value && input.value > 0) {
                    hasGrades = true;
                }
            });
        });

        if (!hasGrades) {
            e.preventDefault();
            alert('Please enter at least one grade');
            return;
        }
    });
});
</script>
@endsection