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
                    <h1 class="text-3xl font-bold text-gray-900">Academic Transcript</h1>
                    <p class="mt-2 text-gray-600">{{ $student->user->name }} - {{ $student->classRoom->name ?? 'N/A' }}</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-print mr-2"></i>Print Transcript
                    </button>
                    <a href="{{ route('student.grades.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Grades
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Transcript Document -->
        <div class="bg-white shadow-2xl rounded-lg overflow-hidden print:shadow-none print:rounded-none print:bg-white border border-gray-300">
            <!-- Header Section -->
            <div class="p-8">
                <!-- School Information -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold mb-2">School Name</h1>
                    <p class="text-lg text-gray-700">School Address, City, Country</p>
                    <p class="text-sm text-gray-600 mt-2">Phone: (123) 456-7890 | Email: info@school.com</p>
                </div>

                <!-- Student Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-center mb-6 text-gray-900">Academic Transcript</h2>
                    
                    <div class="grid grid-cols-2 gap-8 mb-6">
                        <div class="space-y-3">
                            <div class="flex">
                                <span class="font-semibold w-32">Student Name:</span>
                                <span class="text-gray-900">{{ $student->user->name }}</span>
                            </div>
                            <div class="flex">
                                <span class="font-semibold w-32">Student ID:</span>
                                <span class="text-gray-900">{{ $student->admission_number ?? 'N/A' }}</span>
                            </div>
                            <div class="flex">
                                <span class="font-semibold w-32">Class:</span>
                                <span class="text-gray-900">{{ $student->classRoom->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex">
                                <span class="font-semibold w-32">Academic Year:</span>
                                <span class="text-gray-900">{{ date('Y') }}</span>
                            </div>
                            <div class="flex">
                                <span class="font-semibold w-32">Date Issued:</span>
                                <span class="text-gray-900">{{ now()->format('F d, Y') }}</span>
                            </div>
                            <div class="flex">
                                <span class="font-semibold w-32">Status:</span>
                                <span class="text-gray-900">Active Student</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Performance Summary -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Academic Performance Summary</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_subjects'] }}</div>
                            <div class="text-sm text-gray-600">Subjects</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $stats['average_score'] ? number_format($stats['average_score'], 2) : 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-600">Overall Average</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_credits'] }}</div>
                            <div class="text-sm text-gray-600">Total Credits</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-orange-600">
                                {{ $stats['average_score'] ? GradeHelper::getLetterGrade($stats['average_score']) : 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-600">Overall Grade</div>
                        </div>
                    </div>
                </div>

                <!-- Subject Grades Table -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Subject Grades</h3>
                    
                    @if($grades->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-300">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-3 px-4 text-left text-xs font-medium uppercase tracking-wider border-r border-gray-300 text-gray-900">Subject</th>
                                        <th class="py-3 px-4 text-center text-xs font-medium uppercase tracking-wider border-r border-gray-300 text-gray-900">Semester 1</th>
                                        <th class="py-3 px-4 text-center text-xs font-medium uppercase tracking-wider border-r border-gray-300 text-gray-900">Semester 2</th>
                                        <th class="py-3 px-4 text-center text-xs font-medium uppercase tracking-wider border-r border-gray-300 text-gray-900">Year Average</th>
                                        <th class="py-3 px-4 text-center text-xs font-medium uppercase tracking-wider text-gray-900">Final Grade</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-300">
                                    @foreach($grades as $subjectName => $subjectGrades)
                                        @php
                                            $latestGrade = $subjectGrades->first();
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                                {{ $subjectName }}
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap text-center text-sm text-gray-900 border-r border-gray-300">
                                                @if($latestGrade->sem1_avg)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($latestGrade->sem1_avg) }}">
                                                        {{ number_format($latestGrade->sem1_avg, 1) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap text-center text-sm text-gray-900 border-r border-gray-300">
                                                @if($latestGrade->sem2_avg)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($latestGrade->sem2_avg) }}">
                                                        {{ number_format($latestGrade->sem2_avg, 1) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap text-center text-sm font-bold text-gray-900 border-r border-gray-300">
                                                @if($latestGrade->year_avg)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($latestGrade->year_avg) }}">
                                                        {{ number_format($latestGrade->year_avg, 1) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">
                                                @if($latestGrade->year_avg)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ GradeHelper::getGradeColorClass($latestGrade->year_avg) }}">
                                                        {{ GradeHelper::getLetterGrade($latestGrade->year_avg) }}
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
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-graduation-cap text-4xl mb-4"></i>
                            <p class="text-lg">No grades available for transcript generation.</p>
                        </div>
                    @endif
                </div>

                <!-- Grade Legend -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Grade Scale</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">A+</span>
                            <span class="text-sm text-gray-600">90-100</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">A</span>
                            <span class="text-sm text-gray-600">80-89</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">B+</span>
                            <span class="text-sm text-gray-600">70-79</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">B</span>
                            <span class="text-sm text-gray-600">60-69</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">C+</span>
                            <span class="text-sm text-gray-600">50-59</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-200 text-red-900">C</span>
                            <span class="text-sm text-gray-600">40-49</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-200 text-red-900">D</span>
                            <span class="text-sm text-gray-600">0-39</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Section -->
                <div class="grid grid-cols-2 gap-8 mt-12">
                    <!-- Left Side - Remarks -->
                    <div>
                        <div class="mb-2">
                            <span class="font-semibold">Remarks:</span>
                        </div>
                        <div class="border-b border-gray-300 pb-2 mb-4 h-16">
                            <!-- Empty space for remarks -->
                        </div>
                        <div class="text-sm text-gray-900">
                            <span class="font-semibold">Academic Advisor</span>
                        </div>
                    </div>

                    <!-- Right Side - Signature -->
                    <div class="flex justify-end">
                        <div class="text-center">
                            <div class="h-16 w-32 bg-gray-100 border border-gray-300 rounded flex items-center justify-center mb-2">
                                <span class="text-xs text-gray-600">Signature</span>
                            </div>
                            <div class="text-sm">
                                <div class="border-t border-gray-300 pt-1 w-32 mx-auto"></div>
                                <span class="text-xs text-gray-900">Registrar</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Transcript Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Grading System</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Grades are calculated based on period assessments and examinations</li>
                        <li>• Semester averages are computed from all period grades</li>
                        <li>• Year average is the average of both semester averages</li>
                        <li>• Minimum passing grade is 40% (D)</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Academic Standing</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Students must maintain a 70% average for promotion</li>
                        <li>• This transcript is official and verifiable</li>
                        <li>• All grades are subject to administrative approval</li>
                        <li>• For verification, contact the school registrar</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .print\\:bg-white {
        background-color: white !important;
    }
    .print\\:shadow-none {
        box-shadow: none !important;
    }
    .print\\:rounded-none {
        border-radius: 0 !important;
    }
    body {
        background: white !important;
    }
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
