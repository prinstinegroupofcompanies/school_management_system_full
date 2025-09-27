@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Official Grade Sheet</h1>
                    <p class="mt-2 text-gray-600">Period {{ $semester }} - {{ $year }} - {{ $student->user->name }}</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Grade Sheet
                    </button>
                    <a href="{{ route('student.grades.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Periods
                    </a>
                    <a href="{{ route('student.grades.download', ['year' => $year, 'semester' => $semester]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Grade Sheet Document -->
        <div class="bg-white text-gray-900 shadow-2xl rounded-lg overflow-hidden print:shadow-none print:rounded-none print:bg-white border border-gray-300">
            <!-- Header Section -->
            <div class="p-8">
                <!-- School Information -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold mb-2">School Name</h1>
                    <p class="text-lg">Address</p>
                </div>

                <!-- Student Information Grid -->
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div class="flex">
                            <span class="font-semibold w-24">Period:</span>
                            <span>{{ $semester }}</span>
                        </div>
                        <div class="flex">
                            <span class="font-semibold w-24">Year:</span>
                            <span>{{ $year }}</span>
                        </div>
                        <div class="flex">
                            <span class="font-semibold w-24">Student Name:</span>
                            <span>{{ $student->user->name }}</span>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div class="flex">
                            <span class="font-semibold w-16">Year:</span>
                            <span>{{ $academicYear ?? date('Y') }}</span>
                        </div>
                        <div class="flex">
                            <span class="font-semibold w-16">Grade:</span>
                            <span>{{ $student->classRoom->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Grade Sheet Table -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-center mb-4">Grade Sheet</h2>
                    
                    <div class="border border-gray-300">
                        <!-- Table Header -->
                        <div class="grid grid-cols-2 border-b border-gray-300 bg-gray-100">
                            <div class="p-4 font-semibold text-center border-r border-gray-300 text-gray-900">Subject</div>
                            <div class="p-4 font-semibold text-center text-gray-900">Grade</div>
                        </div>

                        <!-- Table Body -->
                        @if($grades->count() > 0)
                            @foreach($grades as $grade)
                                <div class="grid grid-cols-2 border-b border-gray-300 hover:bg-gray-50">
                                    <div class="p-4 border-r border-gray-300 text-gray-900">{{ $grade->subject->name }}</div>
                                    <div class="p-4 text-center text-gray-900">
                                        @if($grade->year_avg)
                                            {{ number_format($grade->year_avg, 1) }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="grid grid-cols-2">
                                <div class="p-4 border-r border-gray-300 text-gray-900">No grades available</div>
                                <div class="p-4 text-center text-gray-900">-</div>
                            </div>
                        @endif

                        <!-- Average Row -->
                        <div class="grid grid-cols-2 bg-gray-200">
                            <div class="p-4 font-semibold border-r border-gray-300 text-gray-900">Average</div>
                            <div class="p-4 text-center font-semibold text-gray-900">
                                @if($stats['average_score'])
                                    {{ number_format($stats['average_score'], 1) }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Section -->
                <div class="grid grid-cols-2 gap-8">
                    <!-- Left Side - Remarks -->
                    <div>
                        <div class="mb-2">
                            <span class="font-semibold">Remark:</span>
                        </div>
                        <div class="border-b border-gray-300 pb-2 mb-4">
                            <!-- Empty line for remarks -->
                        </div>
                        <div class="text-sm text-gray-900">
                            <span class="font-semibold">Authorized Signature</span>
                        </div>
                    </div>

                    <!-- Right Side - Signature -->
                    <div class="flex justify-end">
                        <div class="text-center">
                            @if($adminSignature)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($adminSignature) }}" alt="Authorized Signature" class="h-16 w-32 object-contain mx-auto bg-white p-2 rounded border border-gray-300">
                                </div>
                            @else
                                <div class="h-16 w-32 bg-gray-100 border border-gray-300 rounded flex items-center justify-center mb-2">
                                    <span class="text-xs text-gray-600">Signature</span>
                                </div>
                            @endif
                            <div class="text-sm">
                                <div class="border-t border-gray-300 pt-1 w-32 mx-auto"></div>
                                <span class="text-xs text-gray-900">Authorized Signature</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Grade Breakdown by Semester</h3>
            
            @if($grades->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Semester 1 -->
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-3">Semester 1</h4>
                        <div class="space-y-2">
                            @foreach($grades as $grade)
                                <div class="flex justify-between items-center py-1 border-b border-gray-200">
                                    <span class="text-sm text-gray-600">{{ $grade->subject->name }}</span>
                                    <span class="text-sm font-medium">
                                        @if($grade->sem1_avg)
                                            {{ number_format($grade->sem1_avg, 1) }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Semester 2 -->
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-3">Semester 2</h4>
                        <div class="space-y-2">
                            @foreach($grades as $grade)
                                <div class="flex justify-between items-center py-1 border-b border-gray-200">
                                    <span class="text-sm text-gray-600">{{ $grade->subject->name }}</span>
                                    <span class="text-sm font-medium">
                                        @if($grade->sem2_avg)
                                            {{ number_format($grade->sem2_avg, 1) }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No grade data available for this academic year.</p>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .print\\:bg-gray-800 {
        background-color: #1f2937 !important;
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