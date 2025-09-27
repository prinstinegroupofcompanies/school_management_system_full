@extends('layouts.app')

@section('title', 'My Grades')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Grades</li>
                    </ol>
                </div>
                <h4 class="page-title">My Grades</h4>
            </div>
        </div>
    </div>

    <!-- Period Selection -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="mdi mdi-school me-2"></i>
                        Available Grade Periods
                    </h5>
                    
                    @if($periods->count() > 0)
                        <div class="row">
                            @foreach($periods as $period)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-sm bg-primary-lighten rounded me-3">
                                                    <span class="avatar-title bg-primary text-primary rounded-circle font-weight-medium">
                                                        {{ $period['semester'] }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $period['period_name'] }}</h6>
                                                    <p class="text-muted mb-0 small">Academic Period</p>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-auto">
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('student.grades.grade-sheet', ['year' => $period['year'], 'semester' => $period['semester']]) }}" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="mdi mdi-eye me-1"></i>
                                                        View Grade Sheet
                                                    </a>
                                                    <a href="{{ route('student.grades.download', ['year' => $period['year'], 'semester' => $period['semester']]) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="mdi mdi-download me-1"></i>
                                                        Download PDF
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="mdi mdi-school-outline text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-muted">No Grade Periods Available</h5>
                            <p class="text-muted">Your grades will appear here once teachers submit them for each period.</p>
                            <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
                                <i class="mdi mdi-arrow-left me-1"></i>
                                Back to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    @if($periods->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="mdi mdi-chart-line me-2"></i>
                        Grade Summary
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-primary">{{ $periods->count() }}</div>
                                <div class="text-sm text-muted">Available Periods</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-success">{{ $periods->max('year') ?? 'N/A' }}</div>
                                <div class="text-sm text-muted">Latest Year</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-info">{{ $periods->max('semester') ?? 'N/A' }}</div>
                                <div class="text-sm text-muted">Latest Period</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-warning">
                                    <i class="mdi mdi-check-circle"></i>
                                </div>
                                <div class="text-sm text-muted">All Approved</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.avatar-sm {
    width: 2.5rem;
    height: 2.5rem;
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 600;
}

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.btn {
    transition: all 0.2s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}
</style>
@endsection