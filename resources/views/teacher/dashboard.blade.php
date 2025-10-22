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
                        <i class="fas fa-chalkboard-teacher text-blue-600 mr-3"></i>
                        Teacher Dashboard
                    </h1>
                    <p class="text-lg text-gray-600">Welcome back, {{ auth()->user()->name }}</p>
                    <div class="flex items-center space-x-4 mt-2">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-user-tie mr-2"></i>
                            Teacher
                        </span>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-calendar mr-2"></i>
                            {{ date('M d, Y') }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="refreshDashboard()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Classes</dt>
                                <dd class="text-2xl font-bold text-gray-900" id="total-classes">{{ $stats['total_classes'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-book text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Subjects</dt>
                                <dd class="text-2xl font-bold text-gray-900" id="total-subjects">{{ $stats['total_subjects'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                <dd class="text-2xl font-bold text-gray-900" id="total-students">{{ $stats['total_students'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Exams</dt>
                                <dd class="text-2xl font-bold text-gray-900" id="upcoming-exams">{{ $stats['upcoming_exams'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- My Classes -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <div class="px-6 py-6 bg-gradient-to-r from-blue-600 to-indigo-600">
                    <h3 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-chalkboard mr-3"></i>
                        My Classes
                    </h3>
                </div>
                <div class="p-6">
                    <div id="classes-list" class="space-y-4">
                        @if(isset($classes) && $classes->count() > 0)
                            @foreach($classes as $class)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $class->name ?? 'Unknown Class' }}</h4>
                                    <p class="text-sm text-gray-500">{{ $class->code ?? $class->name }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-semibold text-blue-600">{{ $class->students_count ?? 0 }}</span>
                                    <p class="text-xs text-gray-500">Students</p>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-chalkboard text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500">No classes assigned yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- My Subjects -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <div class="px-6 py-6 bg-gradient-to-r from-green-600 to-blue-600">
                    <h3 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-book mr-3"></i>
                        My Subjects
                    </h3>
                </div>
                <div class="p-6">
                    <div id="subjects-list" class="space-y-4">
                        @if(isset($subjects) && $subjects->count() > 0)
                            @foreach($subjects as $subject)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $subject->name ?? 'Unknown Subject' }}</h4>
                                    <p class="text-sm text-gray-500">{{ $subject->code ?? 'No Code' }}</p>
                                </div>
                                <div class="text-right">
                                    <i class="fas fa-book text-green-500"></i>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-book text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500">No subjects assigned yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="mt-8">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <div class="px-6 py-6 bg-gradient-to-r from-purple-600 to-pink-600">
                    <h3 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-history mr-3"></i>
                        Recent Activities
                    </h3>
                </div>
                <div class="p-6">
                    <div id="recent-activities" class="space-y-4">
                        @if(isset($recentActivities) && $recentActivities->count() > 0)
                            @foreach($recentActivities as $activity)
                            <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-bell text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900">{{ $activity['description'] ?? 'No description' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ isset($activity['created_at']) ? \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() : 'Unknown time' }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-history text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500">No recent activities</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshDashboard() {
    // Show loading state
    const refreshBtn = document.querySelector('button[onclick="refreshDashboard()"]');
    const originalText = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Refreshing...';
    refreshBtn.disabled = true;

    // Reload the page to get fresh data
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Auto-refresh every 5 minutes
setInterval(() => {
    // Only refresh if user is active (not idle)
    if (!document.hidden) {
        window.location.reload();
    }
}, 300000); // 5 minutes

// Real-time updates for statistics
document.addEventListener('DOMContentLoaded', function() {
    console.log('Teacher dashboard loaded with real-time data');
    
    // Update statistics every 30 seconds
    setInterval(updateDashboardData, 30000);
});

function updateDashboardData() {
    fetch('{{ route("teacher.dashboard.data") }}')
        .then(response => response.json())
        .then(data => {
            if (data.stats) {
                // Update statistics cards
                document.getElementById('total-classes').textContent = data.stats.total_classes || 0;
                document.getElementById('total-subjects').textContent = data.stats.total_subjects || 0;
                document.getElementById('total-students').textContent = data.stats.total_students || 0;
                document.getElementById('upcoming-exams').textContent = data.stats.upcoming_exams || 0;
                
                // Add a subtle animation to show data was updated
                const cards = document.querySelectorAll('[id^="total-"], [id^="upcoming-"]');
                cards.forEach(card => {
                    card.style.transform = 'scale(1.05)';
                    card.style.transition = 'transform 0.2s ease';
                    setTimeout(() => {
                        card.style.transform = 'scale(1)';
                    }, 200);
                });
            }
        })
        .catch(error => {
            console.log('Dashboard update failed:', error);
        });
}
</script>
@endsection
