@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Reports Management</h1>
                <div class="flex items-center space-x-4">
                    <button onclick="generateReport('academic')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Generate Academic Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Report Types Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Academic Reports -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 19 16.5 19c-1.746 0-3.332-.477-4.5-1.253"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Academic Reports</h3>
                            <p class="text-sm text-gray-500">Student performance and exam results</p>
                        </div>
                    </div>
                    <div class="mt-4 space-y-2">
                        <button onclick="generateReport('academic')" class="w-full text-left px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded-md">
                            • Class Performance Report
                        </button>
                        <button onclick="generateReport('exam')" class="w-full text-left px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded-md">
                            • Exam Results Summary
                        </button>
                        <button onclick="generateReport('attendance')" class="w-full text-left px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded-md">
                            • Attendance Summary
                        </button>
                    </div>
                </div>
            </div>

            <!-- Financial Reports -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Financial Reports</h3>
                            <p class="text-sm text-gray-500">Fee collection and financial status</p>
                        </div>
                    </div>
                    <div class="mt-4 space-y-2">
                        <button onclick="generateReport('financial')" class="w-full text-left px-3 py-2 text-sm text-green-600 hover:bg-green-50 rounded-md">
                            • Fee Collection Report
                        </button>
                        <button onclick="generateReport('outstanding')" class="w-full text-left px-3 py-2 text-sm text-green-600 hover:bg-green-50 rounded-md">
                            • Outstanding Fees Report
                        </button>
                        <button onclick="generateReport('revenue')" class="w-full text-left px-3 py-2 text-sm text-green-600 hover:bg-green-50 rounded-md">
                            • Revenue Analysis
                        </button>
                    </div>
                </div>
            </div>

            <!-- Administrative Reports -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Administrative Reports</h3>
                            <p class="text-sm text-gray-500">System usage and user statistics</p>
                        </div>
                    </div>
                    <div class="mt-4 space-y-2">
                        <button onclick="generateReport('user')" class="w-full text-left px-3 py-2 text-sm text-purple-600 hover:bg-purple-50 rounded-md">
                            • User Activity Report
                        </button>
                        <button onclick="generateReport('system')" class="w-full text-left px-3 py-2 text-sm text-purple-600 hover:bg-purple-50 rounded-md">
                            • System Usage Report
                        </button>
                        <button onclick="generateReport('audit')" class="w-full text-left px-3 py-2 text-sm text-purple-600 hover:bg-purple-50 rounded-md">
                            • Audit Trail Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Generation Form -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Generate Custom Report</h3>
                <form method="POST" action="{{ route('admin.reports.generate') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label for="report_type" class="block text-sm font-medium text-gray-700">Report Type</label>
                        <select name="report_type" id="report_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select Report Type</option>
                            <option value="academic">Academic Report</option>
                            <option value="financial">Financial Report</option>
                            <option value="attendance">Attendance Report</option>
                            <option value="user">User Activity Report</option>
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date" id="end_date" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Reports</h3>
                    <span class="text-sm text-gray-500">Total: {{ $reports->total() ?? 0 }} reports</span>
                </div>
                
                @if(isset($reports) && $reports->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reports as $report)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $report->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $report->description ?? 'No description' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($report->type ?? 'N/A') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $report->generated_by ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($report->created_at)
                                            {{ $report->created_at->format('M d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if($report->created_at)
                                            {{ $report->created_at->format('H:i') }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.reports.download', $report) }}" class="text-blue-600 hover:text-blue-900">Download</a>
                                        <a href="{{ route('admin.reports.show', $report) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        <form method="POST" action="{{ route('admin.reports.destroy', $report) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this report?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $reports->links() }}
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No reports found</h3>
                    <p class="mt-1 text-sm text-gray-500">Generate your first report to get started.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function generateReport(type) {
    // Set the report type in the form
    document.getElementById('report_type').value = type;
    
    // Set default dates (current month)
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    document.getElementById('start_date').value = firstDay.toISOString().split('T')[0];
    document.getElementById('end_date').value = lastDay.toISOString().split('T')[0];
    
    // Scroll to the form
    document.querySelector('.bg-white.shadow.rounded-lg.mb-6').scrollIntoView({ behavior: 'smooth' });
}
</script>
@endsection
