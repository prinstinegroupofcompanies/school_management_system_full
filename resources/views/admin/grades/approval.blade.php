@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Grade Approval</h1>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.grades.all') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        View All Grades
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending Approval</dt>
                                <dd class="text-lg font-medium text-gray-900 stats-value" data-pending-count>{{ $stats['pending_grades'] }}</dd>
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
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Approved</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['approved_grades'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Rejected</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['rejected_grades'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Grades</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_grades'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('admin.grades.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                        <select name="class_id" id="class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                        <select name="subject_id" id="subject_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700">Teacher</label>
                        <select name="teacher_id" id="teacher_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Teachers</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form id="bulkForm" method="POST">
                    @csrf
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button type="button" id="selectAll" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Select All
                            </button>
                            <button type="button" id="bulkApprove" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Approve Selected
                            </button>
                            <button type="button" id="bulkReject" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Reject Selected
                            </button>
                        </div>
                        <span id="selectedCount" class="text-sm text-gray-500">0 selected</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Grades Table -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Grades Pending Approval</h3>
                
                @if($grades->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" id="masterCheckbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Avg</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($grades as $grade)
                                    <tr class="grade-row" data-grade-id="{{ $grade->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" class="grade-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $grade->id }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $grade->student->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $grade->class->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $grade->subject->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $grade->teacher->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $grade->year_avg ? number_format($grade->year_avg, 2) : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $grade->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.grades.show', $grade) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                <form action="{{ route('admin.grades.approve', $grade) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Approve this grade?')">Approve</button>
                                                </form>
                                                <form action="{{ route('admin.grades.reject', $grade) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Reject this grade?')">Reject</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $grades->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No grades pending approval.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const masterCheckbox = document.getElementById('masterCheckbox');
    const gradeCheckboxes = document.querySelectorAll('.grade-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const selectAllBtn = document.getElementById('selectAll');
    const bulkApproveBtn = document.getElementById('bulkApprove');
    const bulkRejectBtn = document.getElementById('bulkReject');
    const bulkForm = document.getElementById('bulkForm');

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.grade-checkbox:checked');
        selectedCount.textContent = `${selected.length} selected`;
        
        bulkApproveBtn.disabled = selected.length === 0;
        bulkRejectBtn.disabled = selected.length === 0;
    }

    masterCheckbox.addEventListener('change', function() {
        gradeCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    gradeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checked = document.querySelectorAll('.grade-checkbox:checked');
            masterCheckbox.checked = checked.length === gradeCheckboxes.length;
            masterCheckbox.indeterminate = checked.length > 0 && checked.length < gradeCheckboxes.length;
            updateSelectedCount();
        });
    });

    selectAllBtn.addEventListener('click', function() {
        gradeCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        masterCheckbox.checked = true;
        updateSelectedCount();
    });

    bulkApproveBtn.addEventListener('click', function() {
        const selected = Array.from(document.querySelectorAll('.grade-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) return;
        
        if (confirm(`Approve ${selected.length} grades?`)) {
            bulkForm.action = '{{ route("admin.grades.bulk-approve") }}';
            bulkForm.method = 'POST';
            
            // Add selected grade IDs to form
            selected.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'grade_ids[]';
                input.value = id;
                bulkForm.appendChild(input);
            });
            
            bulkForm.submit();
        }
    });

    bulkRejectBtn.addEventListener('click', function() {
        const selected = Array.from(document.querySelectorAll('.grade-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) return;
        
        if (confirm(`Reject ${selected.length} grades?`)) {
            bulkForm.action = '{{ route("admin.grades.bulk-reject") }}';
            bulkForm.method = 'POST';
            
            // Add selected grade IDs to form
            selected.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'grade_ids[]';
                input.value = id;
                bulkForm.appendChild(input);
            });
            
            bulkForm.submit();
        }
    });

    updateSelectedCount();
});
</script>
@endsection
