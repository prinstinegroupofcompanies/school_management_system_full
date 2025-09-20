@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Grade Analytics</h1>
                    <p class="mt-2 text-gray-600">Comprehensive academic performance analytics</p>
                </div>
                <a href="{{ route('admin.grades.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Grades
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Analytics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-chart-bar text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Grades</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $gradeDistribution->sum('count') ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-graduation-cap text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Average GPA</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($classPerformance->avg('avg_gpa') ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending Approval</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-trophy text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">A Grades</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $gradeDistribution->where('letter_grade', 'A')->first()->count ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade Distribution Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Grade Distribution</h2>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                @foreach(['A+', 'A', 'B+', 'B', 'C+', 'C'] as $grade)
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-2 rounded-full flex items-center justify-center font-bold text-white
                        {{ in_array($grade, ['A+', 'A']) ? 'bg-green-500' : (in_array($grade, ['B+', 'B']) ? 'bg-blue-500' : 'bg-yellow-500') }}">
                        {{ $grade }}
                    </div>
                    <div class="text-sm font-medium">{{ $gradeDistribution->where('letter_grade', $grade)->first()->count ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Students</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Performance Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Subject Performance -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Subject Performance</h2>
                <div class="space-y-3">
                    @forelse($subjectPerformance ?? [] as $subject)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-900">Subject {{ $subject->subject_id }}</div>
                            <div class="text-sm text-gray-500">{{ $subject->total_grades }} grades</div>
                        </div>
                        <div class="text-right">
                            <div class="font-medium text-gray-900">{{ number_format($subject->avg_percentage, 1) }}%</div>
                            <div class="text-sm text-gray-500">Average</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        No subject performance data available
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Class Performance -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Class Performance</h2>
                <div class="space-y-3">
                    @forelse($classPerformance ?? [] as $class)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-900">Class {{ $class->class_id }}</div>
                            <div class="text-sm text-gray-500">{{ $class->total_grades }} grades</div>
                        </div>
                        <div class="text-right">
                            <div class="font-medium text-gray-900">{{ number_format($class->avg_gpa, 2) }}</div>
                            <div class="text-sm text-gray-500">GPA</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        No class performance data available
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
