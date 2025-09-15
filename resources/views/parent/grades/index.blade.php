@extends('layouts.app')

@section('title', 'Student Grades')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Student Grades</h1>
        <div class="flex space-x-4">
            <button onclick="exportGrades()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                Export Grades
            </button>
        </div>
    </div>

    <!-- Student Selection -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <form method="GET" class="flex items-center space-x-4">
            <div class="min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Student</label>
                <select name="student_id" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ $selectedStudentId == $student->id ? 'selected' : '' }}>
                            {{ $student->user->name }} ({{ $student->student_id }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if($selectedStudentId && $grades->count() > 0)
        @php
            $selectedStudent = $students->firstWhere('id', $selectedStudentId);
        @endphp

        <!-- Student Info -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $selectedStudent->user->name }} - Academic Performance</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $grades->count() }}</div>
                    <div class="text-sm text-gray-600">Total Subjects</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($grades->avg('year_avg'), 1) }}</div>
                    <div class="text-sm text-gray-600">Average Grade</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $grades->where('year_avg', '>=', 80)->count() }}</div>
                    <div class="text-sm text-gray-600">A Grades (80+)</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $grades->where('year_avg', '>=', 60)->count() }}</div>
                    <div class="text-sm text-gray-600">Passing Grades (60+)</div>
                </div>
            </div>
        </div>

        <!-- Grades by Academic Year -->
        @foreach($grades->groupBy('academic_year') as $year => $yearGrades)
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Academic Year {{ $year }}</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester 1</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester 2</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Average</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($yearGrades as $grade)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $grade->subject->name }}</div>
                                        <div class="text-sm text-gray-500">Teacher: {{ $grade->teacher->user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $grade->class->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            <div class="flex space-x-1">
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $grade->sem1_p1 ?? '-' }}</span>
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $grade->sem1_p2 ?? '-' }}</span>
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $grade->sem1_p3 ?? '-' }}</span>
                                            </div>
                                            <div class="text-center">
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded font-semibold">
                                                    Avg: {{ $grade->sem1_avg ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            <div class="flex space-x-1">
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $grade->sem2_p4 ?? '-' }}</span>
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $grade->sem2_p5 ?? '-' }}</span>
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $grade->sem2_p6 ?? '-' }}</span>
                                            </div>
                                            <div class="text-center">
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded font-semibold">
                                                    Avg: {{ $grade->sem2_avg ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-semibold rounded-full">
                                            {{ $grade->year_avg ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $yearAvg = $grade->year_avg;
                                            $gradeLetter = $yearAvg >= 90 ? 'A+' : 
                                                         ($yearAvg >= 80 ? 'A' : 
                                                         ($yearAvg >= 70 ? 'B+' : 
                                                         ($yearAvg >= 60 ? 'B' : 
                                                         ($yearAvg >= 50 ? 'C' : 'F'))));
                                            $gradeColor = $yearAvg >= 80 ? 'green' : 
                                                        ($yearAvg >= 60 ? 'yellow' : 'red');
                                        @endphp
                                        <span class="px-3 py-1 bg-{{ $gradeColor }}-100 text-{{ $gradeColor }}-800 text-sm font-bold rounded-full">
                                            {{ $gradeLetter }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $grade->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                               ($grade->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($grade->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

    @elseif($selectedStudentId)
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No grades found</h3>
                <p class="mt-1 text-sm text-gray-500">No grades have been recorded for this student yet.</p>
            </div>
        </div>
    @else
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No students found</h3>
                <p class="mt-1 text-sm text-gray-500">No students are associated with your account.</p>
            </div>
        </div>
    @endif
</div>

<script>
function exportGrades() {
    if (confirm('Export grades as PDF?')) {
        // This would typically trigger a download
        alert('Export functionality would be implemented here');
    }
}
</script>
@endsection
