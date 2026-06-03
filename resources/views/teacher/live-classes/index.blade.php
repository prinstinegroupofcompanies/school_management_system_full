@extends('layouts.app')

@section('title', 'Live Classes')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-video me-2"></i>Live Classes
        </h2>
        <a href="{{ route('teacher.live-classes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Schedule Live Class
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
            <form method="GET" action="{{ route('teacher.live-classes.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="live" {{ request('status') === 'live' ? 'selected' : '' }}>Live</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('teacher.live-classes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Live Classes Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Scheduled At</th>
                            <th>Duration</th>
                            <th>Platform</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($liveClasses as $liveClass)
                        <tr>
                            <td>
                                <strong>{{ $liveClass->title }}</strong>
                                @if($liveClass->description)
                                    <br><small class="text-muted">{{ Str::limit($liveClass->description, 50) }}</small>
                                @endif
                            </td>
                            <td>{{ $liveClass->classRoom->name ?? 'N/A' }}</td>
                            <td>{{ $liveClass->subject->name ?? 'N/A' }}</td>
                            <td>{{ $liveClass->scheduled_at->format('M d, Y g:i A') }}</td>
                            <td>{{ $liveClass->duration_minutes }} min</td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $liveClass->platform)) }}</span>
                            </td>
                            <td>
                                @if($liveClass->status === 'scheduled')
                                    <span class="badge bg-primary">Scheduled</span>
                                @elseif($liveClass->status === 'live')
                                    <span class="badge bg-success">Live</span>
                                @elseif($liveClass->status === 'completed')
                                    <span class="badge bg-secondary">Completed</span>
                                @else
                                    <span class="badge bg-warning">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('teacher.live-classes.show', $liveClass) }}" class="btn btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($liveClass->status === 'scheduled')
                                        <form action="{{ route('teacher.live-classes.start', $liveClass) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Start">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('teacher.live-classes.cancel', $liveClass) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this class?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning" title="Cancel">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @elseif($liveClass->status === 'live')
                                        <form action="{{ route('teacher.live-classes.end', $liveClass) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger" title="End Class">
                                                <i class="fas fa-stop"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <p class="text-muted mb-0">No live classes found.</p>
                                <a href="{{ route('teacher.live-classes.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus me-2"></i>Schedule Your First Live Class
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($liveClasses->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $liveClasses->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

