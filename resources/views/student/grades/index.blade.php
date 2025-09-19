@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Grades</h1>
                    <p class="mt-2 text-gray-600">View your academic performance and progress</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('student.grades.transcript') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-file-alt mr-2"></i>
                        View Transcript
                    </a>
                    <a href="{{ route('student.grades.download-transcript') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Academic Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Current GPA</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($academicStats['current_gpa'] ?? 0, 2) }}</p>
                        <p class="text-sm text-gray-500">{{ $academicStats['gpa_grade'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-graduation-cap text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Credits</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $academicStats['total_credits'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500">Completed</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-award text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Class Rank</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $academicStats['class_rank'] ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">of {{ $academicStats['total_students'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-trophy text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Achievement Level</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $academicStats['achievement_level'] ?? 'Developing' }}</p>
                        <p class="text-sm text-gray-500">Performance</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm mb-6">
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                        <select name="academic_year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Years</option>
                            @foreach($academicYears as $year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                        <select name="semester" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Semesters</option>
                            <option value="fall" {{ request('semester') === 'fall' ? 'selected' : '' }}>Fall</option>
                            <option value="spring" {{ request('semester') === 'spring' ? 'selected' : '' }}>Spring</option>
                            <option value="summer" {{ request('semester') === 'summer' ? 'selected' : '' }}>Summer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <select name="subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Grades by Subject -->
        <div class="space-y-6">
            @forelse($gradesBySubject as $subjectName => $subjectGrades)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-semibold text-white">{{ $subjectName }}</h3>
                            <p class="text-blue-100 text-sm">
                                {{ $subjectGrades->count() }} assessments • 
                                Average: {{ number_format($subjectGrades->avg('percentage'), 1) }}% • 
                                Grade: {{ $subjectGrades->first()->letter_grade ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-white">{{ number_format($subjectGrades->avg('gpa_points'), 2) }}</div>
                            <div class="text-blue-100 text-sm">GPA Points</div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Assessment</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Type</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Date</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Score</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Grade</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Status</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($subjectGrades as $grade)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-gray-900">{{ $grade->assessment_title }}</div>
                                        @if($grade->assessment_description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($grade->assessment_description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($grade->assessment_type === 'final') bg-red-100 text-red-800
                                            @elseif($grade->assessment_type === 'midterm') bg-orange-100 text-orange-800
                                            @elseif($grade->assessment_type === 'quiz') bg-blue-100 text-blue-800
                                            @elseif($grade->assessment_type === 'project') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($grade->assessment_type) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-900">
                                        {{ $grade->assessment_date ? $grade->assessment_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center">
                                            <div class="font-medium text-gray-900">{{ $grade->raw_score }}/{{ $grade->max_score }}</div>
                                            <div class="ml-2 text-sm text-gray-500">({{ number_format($grade->percentage, 1) }}%)</div>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full" 
                                                 style="width: {{ min(100, $grade->percentage) }}%"></div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex flex-col">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($grade->letter_grade === 'A' || $grade->letter_grade === 'A+') bg-green-100 text-green-800
                                                @elseif($grade->letter_grade === 'B' || $grade->letter_grade === 'B+') bg-blue-100 text-blue-800
                                                @elseif($grade->letter_grade === 'C' || $grade->letter_grade === 'C+') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ $grade->letter_grade ?? 'N/A' }}
                                            </span>
                                            <span class="text-xs text-gray-500 mt-1">{{ number_format($grade->gpa_points, 2) }} GPA</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($grade->status === 'published') bg-green-100 text-green-800
                                            @elseif($grade->status === 'pending_approval') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $grade->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <a href="{{ route('student.grades.show', $grade) }}" 
                                           class="text-blue-600 hover:text-blue-900 font-medium">
                                            <i class="fas fa-eye mr-1"></i>View Details
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                <div class="text-gray-400">
                    <i class="fas fa-chart-bar text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Grades Available</h3>
                    <p class="text-gray-500">Your grades will appear here once your teachers publish them.</p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Grade Legend -->
        <div class="mt-8 bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Grading Scale</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 text-green-800 rounded-full flex items-center justify-center font-bold mx-auto mb-2">A+</div>
                    <div class="text-sm font-medium">97-100%</div>
                    <div class="text-xs text-gray-500">4.0 GPA</div>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 text-green-800 rounded-full flex items-center justify-center font-bold mx-auto mb-2">A</div>
                    <div class="text-sm font-medium">93-96%</div>
                    <div class="text-xs text-gray-500">3.7 GPA</div>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center font-bold mx-auto mb-2">B+</div>
                    <div class="text-sm font-medium">87-92%</div>
                    <div class="text-xs text-gray-500">3.3 GPA</div>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center font-bold mx-auto mb-2">B</div>
                    <div class="text-sm font-medium">83-86%</div>
                    <div class="text-xs text-gray-500">3.0 GPA</div>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-yellow-100 text-yellow-800 rounded-full flex items-center justify-center font-bold mx-auto mb-2">C+</div>
                    <div class="text-sm font-medium">77-82%</div>
                    <div class="text-xs text-gray-500">2.3 GPA</div>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-yellow-100 text-yellow-800 rounded-full flex items-center justify-center font-bold mx-auto mb-2">C</div>
                    <div class="text-sm font-medium">70-76%</div>
                    <div class="text-xs text-gray-500">2.0 GPA</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
