@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-book text-blue-600 mr-2"></i>
                            {{ $subject->name }}
                        </h1>
                        <p class="text-gray-600 mt-1">{{ $subject->code ?? 'No Code' }} - {{ $subject->description ?? 'No Description' }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('teacher.subjects.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Subjects
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Subject Information -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Subject Information</h2>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Subject Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $subject->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Subject Code</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $subject->code ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Level</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $subject->level ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Type</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $subject->type ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Hours Per Week</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $subject->hours_per_week ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Passing Marks</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $subject->passing_marks ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Full Marks</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $subject->full_marks ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($subject->status === 'active') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($subject->status ?? 'Unknown') }}
                                </span>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Description</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $subject->description ?? 'No description available' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grades -->
                @if($subject->grades && $subject->grades->count() > 0)
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Recent Grades</h2>
                    </div>
                    <div class="px-6 py-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($subject->grades->take(10) as $grade)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $grade->student->first_name ?? 'N/A' }} {{ $grade->student->last_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($grade->grade >= 90) bg-green-100 text-green-800
                                                @elseif($grade->grade >= 80) bg-blue-100 text-blue-800
                                                @elseif($grade->grade >= 70) bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $grade->grade ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $grade->created_at ? \Carbon\Carbon::parse($grade->created_at)->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ucfirst($grade->exam_type ?? 'Assignment') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Quick Stats -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Subject Statistics</h2>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Total Students</span>
                                <span class="text-sm font-bold text-gray-900">{{ $subjectStats['total_students'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Total Grades</span>
                                <span class="text-sm font-bold text-gray-900">{{ $subjectStats['total_grades'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Average Grade</span>
                                <span class="text-sm font-bold text-gray-900">
                                    {{ $subjectStats['average_grade'] ? number_format($subjectStats['average_grade'], 1) : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Pending Grades</span>
                                <span class="text-sm font-bold text-gray-900">{{ $subjectStats['pending_grades'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Approved Grades</span>
                                <span class="text-sm font-bold text-gray-900">{{ $subjectStats['approved_grades'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                @if($recentActivities && $recentActivities->count() > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Recent Activities</h2>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach($recentActivities as $activity)
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">{{ $activity['description'] ?? 'Activity' }}</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ isset($activity['created_at']) ? \Carbon\Carbon::parse($activity['created_at'])->format('M d') : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
