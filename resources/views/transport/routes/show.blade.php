@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('transport.index') }}">Transport</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('transport.routes') }}">Routes</a></li>
                        <li class="breadcrumb-item active">Route Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Route Details</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Route Information</h5>
                    <div>
                        <a href="{{ route('transport.routes.edit', $route) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                        <a href="{{ route('transport.routes') }}" class="btn btn-secondary btn-sm">Back to Routes</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Route Name:</strong></td>
                                    <td>{{ $route->route_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Route Code:</strong></td>
                                    <td><span class="badge bg-primary">{{ $route->route_code }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Start Location:</strong></td>
                                    <td>{{ $route->start_location }}</td>
                                </tr>
                                <tr>
                                    <td><strong>End Location:</strong></td>
                                    <td>{{ $route->end_location }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Distance:</strong></td>
                                    <td>{{ $route->distance_km }} KM</td>
                                </tr>
                                <tr>
                                    <td><strong>Fare Amount:</strong></td>
                                    <td>{{ $route->currency }} {{ number_format($route->fare_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>{{ $route->estimated_duration ? $route->estimated_duration . ' minutes' : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $route->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($route->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($route->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6><strong>Description:</strong></h6>
                            <p class="text-muted">{{ $route->description }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6><strong>Route Information:</strong></h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h5 class="text-primary mb-1">{{ $route->distance_km }}</h5>
                                        <small class="text-muted">Distance (KM)</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h5 class="text-success mb-1">{{ $route->currency }} {{ number_format($route->fare_amount, 2) }}</h5>
                                        <small class="text-muted">Fare Amount</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h5 class="text-info mb-1">{{ $route->estimated_duration ?? 'N/A' }}</h5>
                                        <small class="text-muted">Duration (min)</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h5 class="text-warning mb-1">{{ $route->created_at->format('M d, Y') }}</h5>
                                        <small class="text-muted">Created Date</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
