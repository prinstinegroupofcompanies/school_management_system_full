@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header -->
    <div class="bg-white shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 opacity-5"></div>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 relative">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-clipboard-list text-blue-600 mr-3"></i>
                        My Exams
                    </h1>
                    <p class="text-lg text-gray-600">View and take your assigned exams</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('student.exams.upcoming') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-calendar mr-2"></i>
                        Upcoming Exams
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
        <!-- Online Exams (Exam Papers) -->
        @if($examPapers && $examPapers->count() > 0)
        <div class="mb-8">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <div class="px-6 py-6 bg-gradient-to-r from-blue-600 to-indigo-600">
                    <h3 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-laptop mr-3"></i>
                        Online Exams
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($examPapers as $exam)
                        <div class="bg-gray-50 rounded-xl p-6 hover:shadow-lg transition-all duration-200">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $exam->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $exam->subject->name ?? 'Unknown Subject' }}</p>
                                    <p class="text-xs text-gray-500">{{ $exam->classRoom->name ?? 'Unknown Class' }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($exam->is_published) bg-green-100 text-green-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $exam->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-clock mr-2"></i>
                                    Duration: {{ $exam->duration_minutes }} minutes
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-star mr-2"></i>
                                    Total Marks: {{ $exam->total_marks }}
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Start: {{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('M d, Y H:i') : 'Not set' }}
                                </div>
                            </div>

                            @if($exam->is_published && $exam->start_time && \Carbon\Carbon::parse($exam->start_time)->isFuture())
                            <div class="flex space-x-2">
                                <a href="{{ route('student.exams.show', $exam->id) }}" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium text-center transition-colors">
                                    <i class="fas fa-eye mr-1"></i>
                                    View Details
                                </a>
                                <a href="{{ route('student.exams.start', $exam->id) }}" 
                                   class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium text-center transition-colors">
                                    <i class="fas fa-play mr-1"></i>
                                    Start Exam
                                </a>
                            </div>
                            @elseif($exam->is_published)
                            <div class="text-center">
                                <span class="text-sm text-gray-500">Exam has ended</span>
                            </div>
                            @else
                            <div class="text-center">
                                <span class="text-sm text-gray-500">Exam not yet published</span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Traditional Exam Schedules -->
        @if($examSchedules && $examSchedules->count() > 0)
        <div class="mb-8">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <div class="px-6 py-6 bg-gradient-to-r from-green-600 to-blue-600">
                    <h3 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-calendar-alt mr-3"></i>
                        Scheduled Exams
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($examSchedules as $exam)
                        <div class="bg-gray-50 rounded-xl p-6 hover:shadow-lg transition-all duration-200">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $exam->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $exam->subject->name ?? 'Unknown Subject' }}</p>
                                    <p class="text-xs text-gray-500">{{ $exam->class->name ?? 'Unknown Class' }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($exam->is_active) bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $exam->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Date: {{ \Carbon\Carbon::parse($exam->start_date)->format('M d, Y') }}
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-clock mr-2"></i>
                                    Time: {{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }}
                                </div>
                                @if($exam->venue)
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    Venue: {{ $exam->venue }}
                                </div>
                                @endif
                            </div>

                            <div class="text-center">
                                <span class="text-sm text-gray-500">Traditional Exam</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Exam Attempts History -->
        @if($examAttempts && $examAttempts->count() > 0)
        <div class="mb-8">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <div class="px-6 py-6 bg-gradient-to-r from-purple-600 to-pink-600">
                    <h3 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-history mr-3"></i>
                        Exam History
                    </h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($examAttempts as $attempt)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $attempt->examPaper->title ?? 'Unknown Exam' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $attempt->examPaper->subject->name ?? 'Unknown Subject' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $attempt->score ?? 'N/A' }}/{{ $attempt->examPaper->total_marks ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($attempt->status === 'completed') bg-green-100 text-green-800
                                            @elseif($attempt->status === 'in_progress') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($attempt->status ?? 'Unknown') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attempt->created_at ? \Carbon\Carbon::parse($attempt->created_at)->format('M d, Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('student.exams.result', $attempt->id) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- No Exams Message -->
        @if((!$examPapers || $examPapers->count() == 0) && (!$examSchedules || $examSchedules->count() == 0) && (!$examAttempts || $examAttempts->count() == 0))
        <div class="text-center py-12">
            <div class="text-gray-400">
                <i class="fas fa-clipboard-list text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No exams available</h3>
                <p class="text-gray-500">You don't have any exams assigned at the moment.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection