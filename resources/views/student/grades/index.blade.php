@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
use App\Helpers\GradeHelper;
@endphp

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Grades</h1>
                    <p class="mt-2 text-gray-600">Academic Year {{ $academicYear }} - View your grades by period and subject</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('student.grades.grade-sheet') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-certificate mr-2"></i>Official Grade Sheet
                    </a>
                    <a href="{{ route('student.grades.transcript') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-file-alt mr-2"></i>View Transcript
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-book text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Subjects</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_subjects'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Overall Average</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['average_score'] ? number_format($stats['average_score'], 2) : 'N/A' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Semester 1</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['semester1_average'] ? number_format($stats['semester1_average'], 2) : 'N/A' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-calendar-check text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Semester 2</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['semester2_average'] ? number_format($stats['semester2_average'], 2) : 'N/A' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <i class="fas fa-trophy text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Highest Score</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['highest_score'] ? number_format($stats['highest_score'], 2) : 'N/A' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ $stats['is_eligible_for_promotion'] ? 'bg-green-500' : 'bg-red-500' }} rounded-md flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Promotion Status</dt>
                                <dd class="text-lg font-medium {{ $stats['is_eligible_for_promotion'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $stats['is_eligible_for_promotion'] ? 'Eligible' : 'Not Eligible' }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Averages -->
        @if($stats['period_averages'])
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Period Averages</h3>
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                    @for($period = 1; $period <= 6; $period++)
                        <div class="text-center">
                            <div class="text-sm font-medium text-gray-500">Period {{ $period }}</div>
                            <div class="text-lg font-semibold text-gray-900">
                                {{ $stats['period_averages']['period_' . $period] ? number_format($stats['period_averages']['period_' . $period], 2) : 'N/A' }}
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
        @endif

        <!-- Period-based Grades Display -->
        @if($grades->count() > 0)
            <!-- Period Tabs -->
            <div class="bg-white shadow-lg rounded-lg mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="showPeriod('semester1')" id="semester1-tab" class="period-tab active py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600">
                            <i class="fas fa-calendar-alt mr-2"></i>Semester 1
                        </button>
                        <button onclick="showPeriod('semester2')" id="semester2-tab" class="period-tab py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <i class="fas fa-calendar-check mr-2"></i>Semester 2
                        </button>
                        <button onclick="showPeriod('yearly')" id="yearly-tab" class="period-tab py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <i class="fas fa-trophy mr-2"></i>Year Summary
                        </button>
                    </nav>
                </div>

                <!-- Semester 1 Grades -->
                <div id="semester1-content" class="period-content p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Semester 1 Grades</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Period 1</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Period 2</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Period 3</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Average</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($grades as $grade)
                                    <tr class="grade-row" data-grade-id="{{ $grade->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $grade->subject->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $grade->teacher->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            @if($grade->sem1_p1)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem1_p1) }}">
                                                    {{ number_format($grade->sem1_p1, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            @if($grade->sem1_p2)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem1_p2) }}">
                                                    {{ number_format($grade->sem1_p2, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            @if($grade->sem1_p3)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem1_p3) }}">
                                                    {{ number_format($grade->sem1_p3, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            @if($grade->sem1_exam)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem1_exam) }}">
                                                    {{ number_format($grade->sem1_exam, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">
                                            @if($grade->sem1_avg)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem1_avg) }}">
                                                    {{ number_format($grade->sem1_avg, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">
                                            @if($grade->sem1_avg)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem1_avg) }}">
                                                    {{ GradeHelper::getLetterGrade($grade->sem1_avg) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Semester 2 Grades (Hidden by default) -->
                <div id="semester2-content" class="period-content p-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Semester 2 Grades</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Period 4</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Period 5</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Period 6</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Average</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($grades as $grade)
                                    <tr class="grade-row" data-grade-id="{{ $grade->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $grade->subject->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $grade->teacher->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            @if($grade->sem2_p4)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem2_p4) }}">
                                                    {{ number_format($grade->sem2_p4, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            @if($grade->sem2_p5)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem2_p5) }}">
                                                    {{ number_format($grade->sem2_p5, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            @if($grade->sem2_p6)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem2_p6) }}">
                                                    {{ number_format($grade->sem2_p6, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            @if($grade->sem2_exam)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem2_exam) }}">
                                                    {{ number_format($grade->sem2_exam, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">
                                            @if($grade->sem2_avg)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem2_avg) }}">
                                                    {{ number_format($grade->sem2_avg, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">
                                            @if($grade->sem2_avg)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem2_avg) }}">
                                                    {{ GradeHelper::getLetterGrade($grade->sem2_avg) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Year Summary Grades (Hidden by default) -->
                <div id="yearly-content" class="period-content p-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Year Summary Grades</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Semester 1 Avg</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Semester 2 Avg</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Year Average</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Final Grade</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($grades as $grade)
                                    <tr class="grade-row" data-grade-id="{{ $grade->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $grade->subject->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $grade->teacher->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            @if($grade->sem1_avg)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem1_avg) }}">
                                                    {{ number_format($grade->sem1_avg, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                            @if($grade->sem2_avg)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->sem2_avg) }}">
                                                    {{ number_format($grade->sem2_avg, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">
                                            @if($grade->year_avg)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->year_avg) }}">
                                                    {{ number_format($grade->year_avg, 1) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">
                                            @if($grade->year_avg)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($grade->year_avg) }}">
                                                    {{ GradeHelper::getLetterGrade($grade->year_avg) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white shadow-lg rounded-lg p-6 text-center text-gray-500">
                <i class="fas fa-graduation-cap text-4xl mb-4"></i>
                <p class="text-lg">No approved grades found for this academic year.</p>
                <p class="text-sm mt-2">Grades will appear here once your teachers submit them and they are approved by the administration.</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodTabs = document.querySelectorAll('.period-tab');
    const periodContents = document.querySelectorAll('.period-content');

    function showPeriod(periodId) {
        // Hide all content
        periodContents.forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active state from all tabs
        periodTabs.forEach(tab => {
            tab.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
            tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });

        // Show selected content
        document.getElementById(periodId + '-content').classList.remove('hidden');
        
        // Activate selected tab
        const activeTab = document.getElementById(periodId + '-tab');
        activeTab.classList.add('active', 'border-indigo-500', 'text-indigo-600');
        activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    }

    // Show the first period by default
    showPeriod('semester1');
});
</script>
@endsection