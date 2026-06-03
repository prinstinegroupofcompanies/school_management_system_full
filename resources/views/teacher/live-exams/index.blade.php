@extends('layouts.app')

@section('title', 'Live Exams')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>Live Exams
        </h2>
        <a href="{{ route('teacher.live-exams.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Live Exam
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.live-exams.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('teacher.live-exams.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Live Exams Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Start Time</th>
                            <th>Duration</th>
                            <th>Total Marks</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($liveExams as $liveExam)
                        <tr>
                            <td>
                                <strong>{{ $liveExam->title }}</strong>
                                @if($liveExam->description)
                                    <br><small class="text-muted">{{ Str::limit($liveExam->description, 50) }}</small>
                                @endif
                            </td>
                            <td>{{ $liveExam->classRoom->name ?? 'N/A' }}</td>
                            <td>{{ $liveExam->subject->name ?? 'N/A' }}</td>
                            <td>{{ $liveExam->start_time->format('M d, Y g:i A') }}</td>
                            <td>{{ $liveExam->duration_minutes }} min</td>
                            <td>{{ $liveExam->total_marks }}</td>
                            <td>
                                @if($liveExam->status === 'scheduled')
                                    <span class="badge bg-primary">Scheduled</span>
                                @elseif($liveExam->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($liveExam->status === 'completed')
                                    <span class="badge bg-secondary">Completed</span>
                                @else
                                    <span class="badge bg-warning">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('teacher.live-exams.show', $liveExam) }}" class="btn btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('teacher.live-exams.attempts', $liveExam) }}" class="btn btn-outline-primary" title="View Attempts">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    @if($liveExam->status === 'scheduled')
                                        <form action="{{ route('teacher.live-exams.start', $liveExam) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Start Exam">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <p class="text-muted mb-0">No live exams found.</p>
                                <a href="{{ route('teacher.live-exams.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus me-2"></i>Create Your First Live Exam
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($liveExams->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $liveExams->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

