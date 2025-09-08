@extends('layouts.app')

@section('title', 'My Teachers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Teachers</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($teachers as $teacher)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <div class="avatar-circle mx-auto mb-3">
                                            @if($teacher->profile_photo)
                                                <img src="{{ asset('storage/' . $teacher->profile_photo) }}" alt="{{ $teacher->name }}" class="rounded-circle" width="80" height="80">
                                            @else
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                    <i class="fas fa-user fa-2x"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <h5 class="card-title">{{ $teacher->name }}</h5>
                                        <p class="text-muted">{{ $teacher->email }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="text-primary">Subjects Taught:</h6>
                                        @forelse($teacher->subjects as $subject)
                                            <span class="badge badge-info mr-1">{{ $subject->name }}</span>
                                        @empty
                                            <span class="text-muted">No subjects assigned</span>
                                        @endforelse
                                    </div>
                                    
                                    @if($teacher->phone)
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i> {{ $teacher->phone }}
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No teachers found for your class.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle img {
    object-fit: cover;
}
</style>
@endsection
