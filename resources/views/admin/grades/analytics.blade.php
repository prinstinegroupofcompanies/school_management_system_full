@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Grades Analytics</h1>
                    <p class="mt-2 text-gray-600">Comprehensive analysis of student performance and grade trends</p>
                    <p class="mt-1 text-sm text-gray-500">
                        <i class="fas fa-clock mr-1"></i>
                        Last updated: <span id="lastUpdateTime">{{ now()->format('H:i:s') }}</span>
                        <span class="ml-2 text-green-600">
                            <i class="fas fa-circle text-xs"></i> Auto-refresh enabled
                        </span>
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button id="refreshBtn" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <i id="refreshIcon" class="fas fa-sync-alt mr-2"></i>
                        <span id="refreshText">Refresh Data</span>
                    </button>
                    <a href="{{ route('admin.grades.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Grades
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Analytics</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                    <select name="year" id="year" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                    <select name="semester" id="semester" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Semesters</option>
                        <option value="1" {{ $semester == '1' ? 'selected' : '' }}>Semester 1</option>
                        <option value="2" {{ $semester == '2' ? 'selected' : '' }}>Semester 2</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Key Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div id="total-grades" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-graduation-cap text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Grades</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_grades']) }}</p>
                    </div>
                </div>
            </div>

            <div id="approved-grades" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Approved</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['approved_grades']) }}</p>
                    </div>
                </div>
            </div>

            <div id="pending-grades" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_grades']) }}</p>
                    </div>
                </div>
            </div>

            <div id="rejected-grades" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Rejected</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['rejected_grades']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Statistics Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div id="average-grade" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Average Grade</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['average_year_grade'] }}%</p>
                    </div>
                </div>
            </div>

            <div id="approval-rate" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-indigo-100 rounded-full">
                        <i class="fas fa-percentage text-indigo-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Approval Rate</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['approval_rate'] }}%</p>
                    </div>
                </div>
            </div>

            <div id="last-update" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <i class="fas fa-clock text-gray-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Last Update</p>
                        <p class="text-2xl font-bold text-gray-900" id="lastUpdateTime">{{ now()->format('H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Status Distribution -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Grade Status Distribution</h3>
                <div class="space-y-4">
                    @foreach($statusDistribution as $status => $count)
                        @php
                            $percentage = $stats['total_grades'] > 0 ? round(($count / $stats['total_grades']) * 100, 1) : 0;
                            $color = $status === 'approved' ? 'green' : ($status === 'pending' ? 'yellow' : 'red');
                            $bgColor = $color === 'green' ? 'bg-green-500' : ($color === 'yellow' ? 'bg-yellow-500' : 'bg-red-500');
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 {{ $bgColor }} rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 capitalize">{{ $status }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-gray-900">{{ number_format($count) }}</span>
                                <span class="text-xs text-gray-500 ml-1">({{ $percentage }}%)</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="{{ $bgColor }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    @endforeach
                </div>
                <!-- Chart Canvas for Real-time Updates -->
                <div class="mt-6">
                    <canvas id="statusChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Grade Distribution -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Grade Range Distribution</h3>
                <div class="space-y-3">
                    @foreach($gradeRanges as $range => $count)
                        @php
                            $percentage = $stats['approved_grades'] > 0 ? round(($count / $stats['approved_grades']) * 100, 1) : 0;
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ $range }}</span>
                            <div class="text-right">
                                <span class="text-sm font-bold text-gray-900">{{ number_format($count) }}</span>
                                <span class="text-xs text-gray-500 ml-1">({{ $percentage }}%)</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Top Performing Students</h3>
            <div class="overflow-x-auto">
                <table id="topStudentsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subjects</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topStudents as $index => $studentData)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                        {{ $index < 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $studentData['student']->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $studentData['student']->admission_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $studentData['student']->class->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $studentData['average'] >= 90 ? 'bg-green-100 text-green-800' : 
                                           ($studentData['average'] >= 80 ? 'bg-blue-100 text-blue-800' : 
                                           ($studentData['average'] >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                        {{ $studentData['average'] }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $studentData['subject_count'] }} subjects
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <i class="fas fa-chart-bar text-6xl mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No approved grades found</h3>
                                        <p class="text-gray-500">Approved grades will appear here for analysis.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Subject & Class Performance -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Subject Performance -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Subject Performance</h3>
                <div class="space-y-4">
                    @forelse($subjectStats as $subjectData)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $subjectData['subject']->name }}</div>
                                <div class="text-xs text-gray-500">{{ $subjectData['count'] }} grades</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-gray-900">{{ $subjectData['average'] }}%</div>
                                <div class="text-xs text-gray-500">Range: {{ $subjectData['lowest'] }}-{{ $subjectData['highest'] }}%</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-book text-4xl mb-2"></i>
                            <p>No subject data available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Class Performance -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Class Performance</h3>
                <div class="space-y-4">
                    @forelse($classStats as $classData)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $classData['class']->name }}</div>
                                <div class="text-xs text-gray-500">{{ $classData['student_count'] }} students</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-gray-900">{{ $classData['average'] }}%</div>
                                <div class="text-xs text-gray-500">{{ $classData['count'] }} grades</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-users text-4xl mb-2"></i>
                            <p>No class data available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Teacher Performance -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Teacher Performance</h3>
            <div class="overflow-x-auto">
                <table id="teacherPerformanceTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grades Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending Approval</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($teacherStats as $teacherData)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <i class="fas fa-chalkboard-teacher text-green-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $teacherData['teacher']->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $teacherData['teacher']->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($teacherData['grades_count']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $teacherData['average'] >= 80 ? 'bg-green-100 text-green-800' : 
                                           ($teacherData['average'] >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $teacherData['average'] }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $teacherData['pending_count'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $teacherData['pending_count'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <i class="fas fa-chalkboard-teacher text-6xl mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No teacher data available</h3>
                                        <p class="text-gray-500">Teacher performance data will appear here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Monthly Approval Trends - {{ $year }}</h3>
            <div class="grid grid-cols-6 md:grid-cols-12 gap-4">
                @foreach($monthlyTrends as $month => $count)
                    @php
                        $monthName = date('M', mktime(0, 0, 0, $month, 1));
                        $maxCount = max($monthlyTrends);
                        $height = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                    @endphp
                    <div class="text-center">
                        <div class="bg-blue-100 rounded-t-lg mx-auto" style="width: 20px; height: {{ $height }}px; min-height: 4px;"></div>
                        <div class="text-xs text-gray-600 mt-2">{{ $monthName }}</div>
                        <div class="text-xs font-medium text-gray-900">{{ $count }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for enhanced visualizations -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Pusher for real-time updates -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<!-- Remove realtime.js to prevent 401 errors -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let statusChart = null;
    let lastUpdateTime = null;
    
    // Real-time update configuration
    const UPDATE_INTERVAL = 30000; // 30 seconds
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // Initialize charts
    initializeCharts();
    
    // Start auto-refresh
    startAutoRefresh();
    
    // Initialize WebSocket for real-time updates
    initializeWebSocket();
    
    
    
    // Add manual refresh button functionality
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            refreshData();
        });
    }
    
    
    // Add filter change listeners
    const yearSelect = document.getElementById('year');
    const semesterSelect = document.getElementById('semester');
    
    if (yearSelect) {
        yearSelect.addEventListener('change', function() {
            refreshData();
        });
    }
    
    if (semesterSelect) {
        semesterSelect.addEventListener('change', function() {
            refreshData();
        });
    }
    
    function initializeCharts() {
        // Status Distribution Pie Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Pending', 'Rejected'],
                    datasets: [{
                        data: [
                            {{ $stats['approved_grades'] }},
                            {{ $stats['pending_grades'] }},
                            {{ $stats['rejected_grades'] }}
                        ],
                        backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }
    
    function startAutoRefresh() {
        setInterval(function() {
            refreshData();
        }, 15000); // 15 seconds for more responsive updates
        
        // Show last update time
        updateLastRefreshTime();
    }
    
    function refreshData() {
        const yearElement = document.getElementById('year');
        const semesterElement = document.getElementById('semester');
        
        if (!yearElement || !semesterElement) {
            console.error('Year or semester elements not found');
            return;
        }
        
        const year = yearElement.value;
        const semester = semesterElement.value;
        
        // Show loading state
        setRefreshLoading(true);
        
        fetch(`{{ route('admin.grades.analytics.data') }}?year=${year}&semester=${semester}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data && typeof data === 'object') {
                updateCharts(data);
                updateStatistics(data);
                updateLastRefreshTime();
            } else {
                throw new Error('Invalid data received from server');
            }
        })
        .catch(error => {
            console.error('Error fetching analytics data:', error);
            showNotification(`Failed to refresh data: ${error.message}`, 'error');
        })
        .finally(() => {
            setRefreshLoading(false);
        });
    }
    
    function setRefreshLoading(loading) {
        const refreshIcon = document.getElementById('refreshIcon');
        const refreshText = document.getElementById('refreshText');
        const refreshBtn = document.getElementById('refreshBtn');
        
        if (loading) {
            refreshIcon.className = 'fas fa-spinner fa-spin mr-2';
            refreshText.textContent = 'Refreshing...';
            refreshBtn.disabled = true;
            refreshBtn.classList.add('opacity-75', 'cursor-not-allowed');
        } else {
            refreshIcon.className = 'fas fa-sync-alt mr-2';
            refreshText.textContent = 'Refresh Data';
            refreshBtn.disabled = false;
            refreshBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    }
    
    function updateCharts(data) {
        // Update status distribution chart
        if (statusChart) {
            try {
                statusChart.data.datasets[0].data = [
                    data.statusDistribution.approved,
                    data.statusDistribution.pending,
                    data.statusDistribution.rejected
                ];
                statusChart.update('active');
            } catch (error) {
                console.error('Error updating chart:', error);
            }
        }
        
        // Update progress bars
        updateProgressBars(data);
    }
    
    function updateProgressBars(data) {
        const totalGrades = data.stats.total_grades;
        if (totalGrades === 0) return;
        
        // Update approved progress bar
        const approvedCount = data.statusDistribution.approved;
        const approvedPercentage = Math.round((approvedCount / totalGrades) * 100);
        updateProgressBar('approved', approvedCount, approvedPercentage);
        
        // Update pending progress bar
        const pendingCount = data.statusDistribution.pending;
        const pendingPercentage = Math.round((pendingCount / totalGrades) * 100);
        updateProgressBar('pending', pendingCount, pendingPercentage);
        
        // Update rejected progress bar
        const rejectedCount = data.statusDistribution.rejected;
        const rejectedPercentage = Math.round((rejectedCount / totalGrades) * 100);
        updateProgressBar('rejected', rejectedCount, rejectedPercentage);
    }
    
    function updateProgressBar(status, count, percentage) {
        // Find the progress bar container for this status
        const statusBars = document.querySelectorAll('.space-y-4 > div');
        statusBars.forEach(bar => {
            const statusText = bar.querySelector('span.capitalize');
            if (statusText && statusText.textContent.toLowerCase() === status) {
                // Update count and percentage
                const countElement = bar.querySelector('.text-right .text-sm.font-bold');
                const percentageElement = bar.querySelector('.text-right .text-xs');
                const progressBar = bar.nextElementSibling.querySelector('div[style*="width"]');
                
                if (countElement) countElement.textContent = count.toLocaleString();
                if (percentageElement) percentageElement.textContent = `(${percentage}%)`;
                if (progressBar) progressBar.style.width = `${percentage}%`;
            }
        });
    }
    
    function updateStatistics(data) {
        try {
            // Update statistics cards
            updateStatCard('total-grades', data.stats.total_grades);
            updateStatCard('pending-grades', data.stats.pending_grades);
            updateStatCard('approved-grades', data.stats.approved_grades);
            updateStatCard('rejected-grades', data.stats.rejected_grades);
            updateStatCard('average-grade', data.stats.average_year_grade + '%');
            updateStatCard('approval-rate', data.stats.approval_rate + '%');
            
            // Update top students table
            updateTopStudentsTable(data.topStudents);
            
            // Update teacher performance table
            updateTeacherPerformanceTable(data.teacherStats);
            
            // Update grade ranges
            updateGradeRanges(data.gradeRanges);
            
        } catch (error) {
            console.error('Error updating statistics:', error);
            showNotification('Error updating some statistics', 'error');
        }
    }
    
    function updateStatCard(cardId, value) {
        const card = document.getElementById(cardId);
        if (card) {
            const valueElement = card.querySelector('.text-3xl, .text-2xl');
            if (valueElement) {
                valueElement.textContent = value;
            }
        }
    }
    
    function updateGradeRanges(gradeRanges) {
        if (!gradeRanges) return;
        
        // Find all grade range elements and update them
        const rangeElements = document.querySelectorAll('.space-y-3 > div');
        rangeElements.forEach(element => {
            const rangeText = element.querySelector('span.text-sm.font-medium');
            if (rangeText) {
                const rangeKey = rangeText.textContent.trim();
                if (gradeRanges[rangeKey] !== undefined) {
                    const countElement = element.querySelector('.text-right .text-sm.font-bold');
                    const percentageElement = element.querySelector('.text-right .text-xs');
                    const progressBar = element.nextElementSibling.querySelector('div[style*="width"]');
                    
                    if (countElement) countElement.textContent = gradeRanges[rangeKey].toLocaleString();
                    
                    // Calculate percentage based on approved grades
                    const totalApproved = Object.values(gradeRanges).reduce((sum, count) => sum + count, 0);
                    const percentage = totalApproved > 0 ? Math.round((gradeRanges[rangeKey] / totalApproved) * 100) : 0;
                    
                    if (percentageElement) percentageElement.textContent = `(${percentage}%)`;
                    if (progressBar) progressBar.style.width = `${percentage}%`;
                }
            }
        });
    }
    
    function updateTopStudentsTable(students) {
        const tbody = document.querySelector('#topStudentsTable tbody');
        if (tbody) {
            tbody.innerHTML = '';
            
            if (students && students.length > 0) {
                students.forEach((studentData, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${studentData.student.user.name}</div>
                                    <div class="text-sm text-gray-500">${studentData.student.admission_number}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${studentData.student.class ? studentData.student.class.name : 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getGradeColor(studentData.average)}">
                                ${studentData.average}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${studentData.subject_count} subjects
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <i class="fas fa-chart-bar text-6xl mb-4"></i>
                            <p class="text-lg">No student data available</p>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            }
        }
    }
    
    function updateTeacherPerformanceTable(teachers) {
        const tbody = document.querySelector('#teacherPerformanceTable tbody');
        if (tbody) {
            tbody.innerHTML = '';
            
            if (teachers && teachers.length > 0) {
                teachers.forEach((teacherData, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-chalkboard-teacher text-green-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${teacherData.teacher.user.name}</div>
                                    <div class="text-sm text-gray-500">${teacherData.teacher.user.email}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${teacherData.grades_count}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getGradeColor(teacherData.average)}">
                                ${teacherData.average}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${teacherData.pending_count > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}">
                                ${teacherData.pending_count}
                            </span>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <i class="fas fa-chalkboard-teacher text-6xl mb-4"></i>
                            <p class="text-lg">No teacher data available</p>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            }
        }
    }
    
    function getGradeColor(grade) {
        if (grade >= 90) return 'bg-green-100 text-green-800';
        if (grade >= 80) return 'bg-blue-100 text-blue-800';
        if (grade >= 70) return 'bg-yellow-100 text-yellow-800';
        return 'bg-red-100 text-red-800';
    }
    
    function updateLastRefreshTime() {
        const timeElement = document.getElementById('lastUpdateTime');
        if (timeElement) {
            timeElement.textContent = new Date().toLocaleTimeString();
        }
    }
    
    function initializeWebSocket() {
        // For development, use enhanced polling instead of WebSocket
        // This ensures real-time updates work even without Pusher configuration
        console.log('Using enhanced polling for real-time updates');
        showNotification('Real-time updates enabled via polling', 'info');
        
        // Enhanced polling with shorter intervals for better responsiveness
        setInterval(function() {
            // Check for updates every 10 seconds for more responsive updates
            refreshData();
        }, 10000);
        
        // Also check for updates when the page becomes visible again
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                refreshData();
            }
        });
        
        // Check for updates when the window regains focus
        window.addEventListener('focus', function() {
            refreshData();
        });
    }
    
    
    function showNotification(message, type = 'info') {
        // Simple notification system
        const notification = document.createElement('div');
        const colors = {
            'error': 'bg-red-500',
            'success': 'bg-green-500',
            'warning': 'bg-yellow-500',
            'info': 'bg-blue-500'
        };
        
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded-md text-white z-50 ${colors[type] || colors.info} shadow-lg`;
        notification.textContent = message;
        
        // Add close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.className = 'ml-2 text-white hover:text-gray-200 font-bold';
        closeBtn.onclick = () => notification.remove();
        notification.appendChild(closeBtn);
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
});
</script>
@endsection