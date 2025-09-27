@extends('layouts.app')

@section('title', 'Grade Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('student.grades.index') }}">My Grades</a></li>
                        <li class="breadcrumb-item active">Grade Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Grade Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-school me-2"></i>
                            Grade Information
                        </h5>
                        <a href="{{ route('student.grades.index') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Back to Grades
                        </a>
                    </div>

                    @php
                        $grade = \App\Models\Grade::with(['subject', 'class', 'teacher.user'])->find($id);
                    @endphp

                    @if($grade)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">Subject Information</h6>
                                        <div class="mb-3">
                                            <strong>Subject:</strong> {{ $grade->subject->name ?? 'N/A' }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Class:</strong> {{ $grade->class->name ?? 'N/A' }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Teacher:</strong> {{ $grade->teacher->user->name ?? 'N/A' }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Academic Year:</strong> {{ $grade->academic_year }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Semester:</strong> {{ $grade->semester }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-success">Grade Details</h6>
                                        <div class="mb-3">
                                            <strong>Year Average:</strong> 
                                            <span class="badge bg-{{ $grade->year_avg >= 80 ? 'success' : ($grade->year_avg >= 60 ? 'warning' : 'danger') }}">
                                                {{ number_format($grade->year_avg, 2) }}%
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Grade Letter:</strong> 
                                            <span class="badge bg-{{ $grade->year_avg >= 80 ? 'success' : ($grade->year_avg >= 60 ? 'warning' : 'danger') }}">
                                                @if($grade->year_avg >= 90) A
                                                @elseif($grade->year_avg >= 80) B
                                                @elseif($grade->year_avg >= 70) C
                                                @elseif($grade->year_avg >= 60) D
                                                @else F
                                                @endif
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Status:</strong> 
                                            <span class="badge bg-{{ $grade->status === 'approved' ? 'success' : ($grade->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($grade->status) }}
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Created:</strong> {{ $grade->created_at->format('M d, Y') }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Updated:</strong> {{ $grade->updated_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Grade Breakdown -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-info">Grade Breakdown</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-primary">{{ $grade->first_test ?? 'N/A' }}</div>
                                                    <div class="text-sm text-muted">First Test</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-primary">{{ $grade->second_test ?? 'N/A' }}</div>
                                                    <div class="text-sm text-muted">Second Test</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-primary">{{ $grade->third_test ?? 'N/A' }}</div>
                                                    <div class="text-sm text-muted">Third Test</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-success">{{ $grade->year_avg ?? 'N/A' }}</div>
                                                    <div class="text-sm text-muted">Year Average</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Analysis -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-warning">Performance Analysis</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold {{ $grade->year_avg >= 80 ? 'text-success' : ($grade->year_avg >= 60 ? 'text-warning' : 'text-danger') }}">
                                                        {{ $grade->year_avg >= 80 ? 'Excellent' : ($grade->year_avg >= 60 ? 'Good' : 'Needs Improvement') }}
                                                    </div>
                                                    <div class="text-sm text-muted">Performance Level</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold {{ $grade->year_avg >= 50 ? 'text-success' : 'text-danger' }}">
                                                        {{ $grade->year_avg >= 50 ? 'Passed' : 'Failed' }}
                                                    </div>
                                                    <div class="text-sm text-muted">Result</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-info">
                                                        {{ $grade->year_avg >= 90 ? 'A+' : ($grade->year_avg >= 80 ? 'A' : ($grade->year_avg >= 70 ? 'B' : ($grade->year_avg >= 60 ? 'C' : 'F'))) }}
                                                    </div>
                                                    <div class="text-sm text-muted">Grade Point</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="mdi mdi-alert-circle text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-muted">Grade Not Found</h5>
                            <p class="text-muted">The requested grade record could not be found.</p>
                            <a href="{{ route('student.grades.index') }}" class="btn btn-primary">
                                <i class="mdi mdi-arrow-left me-1"></i>
                                Back to Grades
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

.text-2xl {
    font-size: 1.5rem;
    font-weight: 700;
}

.text-sm {
    font-size: 0.875rem;
}

.text-muted {
    color: #6c757d !important;
}
</style>
@endsection
