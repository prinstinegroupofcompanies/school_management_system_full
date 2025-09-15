@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Enter Student Grades</h1>
        <div class="flex space-x-4">
            <a href="{{ route('teacher.grades.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back to Grades
            </a>
            <a href="{{ route('teacher.grades.exam-questions') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                Exam Questions
            </a>
        </div>
    </div>
    
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('teacher.grades.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    <label class="block text-sm font-medium text-gray-700">Student</label>
                    <input type="text" id="student_search" placeholder="Search student by name..." class="mt-1 mb-2 block w-full border-gray-300 rounded-md" oninput="filterStudents()" {{ empty($students) ? 'disabled' : '' }}>
                    <select name="student_id" id="student_id" class="block w-full border-gray-300 rounded-md" size="8" required {{ empty($students) ? 'disabled' : '' }}>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->class->name ?? '' }})</option>
                        @endforeach
                    </select>
                    <button type="button" class="mt-2 px-3 py-1 text-sm bg-gray-100 rounded border" onclick="loadEligibleStudents()">Reload list</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Academic Year</label>
                    <input type="number" name="academic_year" class="mt-1 block w-full border-gray-300 rounded-md" value="{{ date('Y') }}" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Semester</label>
                    <select name="semester" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Semester 1</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm text-gray-700">1st Period</label>
                            <input type="number" name="sem1_p1" min="0" max="100" step="0.01" class="mt-1 w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">2nd Period</label>
                            <input type="number" name="sem1_p2" min="0" max="100" step="0.01" class="mt-1 w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">3rd Period</label>
                            <input type="number" name="sem1_p3" min="0" max="100" step="0.01" class="mt-1 w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Exam</label>
                            <input type="number" name="sem1_exam" min="0" max="100" step="0.01" class="mt-1 w-full border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Semester 2</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm text-gray-700">4th Period</label>
                            <input type="number" name="sem2_p4" min="0" max="100" step="0.01" class="mt-1 w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">5th Period</label>
                            <input type="number" name="sem2_p5" min="0" max="100" step="0.01" class="mt-1 w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">6th Period</label>
                            <input type="number" name="sem2_p6" min="0" max="100" step="0.01" class="mt-1 w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Exam</label>
                            <input type="number" name="sem2_exam" min="0" max="100" step="0.01" class="mt-1 w-full border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save (Pending Approval)</button>
                <a href="{{ route('teacher.grades.index') }}" class="ml-2 px-4 py-2 bg-gray-600 text-white rounded">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
    function onClassChange(sel){
        const classId = sel.value;
        const subjectSelect = document.getElementById('subject_id');
        const studentSelect = document.getElementById('student_id');
        
        // Clear subject and student dropdowns
        subjectSelect.innerHTML = '<option value="">Select subject</option>';
        studentSelect.innerHTML = '';
        
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
        
        // Update URL parameters
        const params = new URLSearchParams(window.location.search);
        if (classId) {
            params.set('class_id', classId);
        } else {
            params.delete('class_id');
        }
        window.location.search = params.toString();
    }
    function filterStudents() {
        const input = document.getElementById('student_search');
        const filter = (input.value || '').toLowerCase();
        const select = document.getElementById('student_id');
        if (!select) return;
        const options = select.options;
        for (let i = 0; i < options.length; i++) {
            const txt = options[i].text.toLowerCase();
            const match = txt.indexOf(filter) > -1;
            options[i].hidden = !match;
        }
        // Ensure a visible option is selected if current selection is hidden
        if (select.selectedIndex >= 0 && select.options[select.selectedIndex] && select.options[select.selectedIndex].hidden) {
            for (let i = 0; i < options.length; i++) {
                if (!options[i].hidden) { select.selectedIndex = i; break; }
            }
        }
    }

    function onSubjectChange(sel) {
        const classId = document.querySelector('select[name="class_id"]').value;
        const subjectId = sel.value;
        const studentSelect = document.getElementById('student_id');
        
        // Clear student dropdown
        studentSelect.innerHTML = '';
        
        if (classId && subjectId) {
            loadEligibleStudents();
        }
    }

    function loadEligibleStudents() {
        const classId = document.querySelector('select[name="class_id"]').value;
        const subjectId = document.getElementById('subject_id').value;
        if (!classId || !subjectId) return;
        
        fetch(`{{ route('teacher.grades.students') }}?class_id=${classId}&subject_id=${subjectId}`)
            .then(r => r.json())
            .then(resp => {
                const select = document.getElementById('student_id');
                select.innerHTML = '';
                (resp.data || []).forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = `${s.name} (${s.class})`;
                    select.appendChild(opt);
                });
                
                // Enable student search and select
                document.getElementById('student_search').disabled = false;
                select.disabled = false;
            })
            .catch(() => {});
    }
</script>
@endsection


