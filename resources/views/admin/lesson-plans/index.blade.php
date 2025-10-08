@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-book mr-2"></i>
                        All Lesson Plans
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.lesson-plans.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus mr-1"></i>
                            Create Lesson Plan
                        </a>
                        <a href="{{ route('admin.lesson-plans.dashboard') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-tachometer-alt mr-1"></i>
                            Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="submitted">Submitted</option>
                                <option value="first_level_approved">First Level Approved</option>
                                <option value="second_level_approved">Second Level Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="teacherFilter">
                                <option value="">All Teachers</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="subjectFilter">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary" onclick="filterLessonPlans()">
                                <i class="fas fa-filter mr-1"></i>
                                Filter
                            </button>
                        </div>
                    </div>

                    <!-- Lesson Plans Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Title</th>
                                    <th>Teacher</th>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Lesson Date</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lessonPlans as $plan)
                                <tr>
                                    <td>
                                        <strong>{{ $plan->title }}</strong>
                                        @if($plan->description)
                                            <br><small class="text-muted">{{ Str::limit($plan->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $plan->teacher->user->name }}</td>
                                    <td>{{ $plan->subject->name }}</td>
                                    <td>{{ $plan->class->name }}</td>
                                    <td>{{ $plan->lesson_date ? $plan->lesson_date->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ 
                                            $plan->status === 'approved' || $plan->status === 'second_level_approved' ? 'success' : 
                                            ($plan->status === 'rejected' ? 'danger' : 
                                            ($plan->status === 'first_level_approved' ? 'info' : 'warning')) 
                                        }}">
                                            {{ ucfirst(str_replace('_', ' ', $plan->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $plan->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.lesson-plans.show', $plan) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.lesson-plans.edit', $plan) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($plan->status === 'submitted')
                                            <button type="button" class="btn btn-sm btn-success" onclick="approvePlan({{ $plan->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="rejectPlan({{ $plan->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.lesson-plans.download', $plan) }}" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fas fa-book mr-2"></i>
                                        No lesson plans found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $lessonPlans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterLessonPlans() {
    const status = document.getElementById('statusFilter').value;
    const teacher = document.getElementById('teacherFilter').value;
    const subject = document.getElementById('subjectFilter').value;
    
    let url = new URL(window.location);
    url.searchParams.set('status', status);
    url.searchParams.set('teacher_id', teacher);
    url.searchParams.set('subject_id', subject);
    
    window.location.href = url.toString();
}

function approvePlan(planId) {
    if (confirm('Are you sure you want to approve this lesson plan?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/lesson-plans/${planId}/approve`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectPlan(planId) {
    const reason = prompt('Please enter the reason for rejection:');
    if (reason) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/lesson-plans/${planId}/reject`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const reasonField = document.createElement('input');
        reasonField.type = 'hidden';
        reasonField.name = 'rejection_reason';
        reasonField.value = reason;
        
        form.appendChild(csrfToken);
        form.appendChild(reasonField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
