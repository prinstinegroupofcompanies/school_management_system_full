@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Bulk Grade Entry</h1>
        <div class="flex space-x-4">
            <a href="{{ route('teacher.grades.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back to Grades
            </a>
            <a href="{{ route('teacher.grades.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Single Entry
            </a>
        </div>
    </div>

    <div class="max-w-6xl mx-auto bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('teacher.grades.bulk-store') }}" class="space-y-6">
            @csrf
            
            <!-- Assessment Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Class</label>
                    <select name="class_id" id="bulk_class_id" class="mt-1 block w-full border-gray-300 rounded-md" onchange="loadStudents()" required>
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
                    <select name="subject_id" id="bulk_subject_id" class="mt-1 block w-full border-gray-300 rounded-md" onchange="loadStudents()" required>
                        <option value="">Select subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ (isset($selectedSubjectId) && (int)$selectedSubjectId === $subject->id) ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Assessment Type</label>
                    <select name="assessment_type" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="">Select type</option>
                        @foreach($assessmentTypes as $key => $type)
                            <option value="{{ $key }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Assessment Title</label>
                    <input type="text" name="assessment_title" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Assessment Date</label>
                    <input type="date" name="assessment_date" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Academic Year</label>
                    <input type="text" name="academic_year" value="{{ date('Y') }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Semester</label>
                    <select name="semester" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        @foreach($semesters as $key => $semester)
                            <option value="{{ $key }}">{{ $semester }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Maximum Score</label>
                    <input type="number" name="max_score" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="assessment_description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md"></textarea>
            </div>

            <!-- Students Grades -->
            <div id="students-container" class="hidden">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Student Grades</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comments</th>
                            </tr>
                        </thead>
                        <tbody id="students-list" class="bg-white divide-y divide-gray-200">
                            <!-- Students will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <input type="checkbox" name="save_as_draft" id="save_as_draft" class="h-4 w-4 text-blue-600">
                <label for="save_as_draft" class="text-sm text-gray-700">Save as draft</label>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" onclick="history.back()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Save Grades
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function loadStudents() {
    const classId = document.getElementById('bulk_class_id').value;
    const subjectId = document.getElementById('bulk_subject_id').value;
    
    if (classId && subjectId) {
        // Show loading state
        const container = document.getElementById('students-container');
        const list = document.getElementById('students-list');
        
        container.classList.remove('hidden');
        list.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Loading students...</td></tr>';
        
        // Mock students for now - in real implementation, fetch via AJAX
        setTimeout(() => {
            list.innerHTML = `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Sample Student 1</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="hidden" name="students[0][student_id]" value="1">
                        <input type="number" name="students[0][raw_score]" step="0.01" class="w-full border-gray-300 rounded-md" required>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="text" name="students[0][comments]" class="w-full border-gray-300 rounded-md">
                    </td>
                </tr>
            `;
        }, 500);
    } else {
        document.getElementById('students-container').classList.add('hidden');
    }
}
</script>
@endsection
