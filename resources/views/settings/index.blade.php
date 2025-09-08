@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="card-premium">
    <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 2rem;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-cog me-3"></i>System Settings</h3>
        </div>
    </div>
    
    <div class="card-body p-4">
        @if(isset($school) && $school)
            <!-- School Information -->
            <div class="card-premium mb-4">
                <div class="card-header" style="background: var(--secondary-gradient); color: white; padding: 1.5rem;">
                    <h4 class="mb-0"><i class="fas fa-school me-2"></i>School Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label font-semibold text-gray-700">School Name:</label>
                                <p class="text-gray-900">{{ $school->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-semibold text-gray-700">Address:</label>
                                <p class="text-gray-900">{{ $school->address }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-semibold text-gray-700">Phone:</label>
                                <p class="text-gray-900">{{ $school->phone }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label font-semibold text-gray-700">Email:</label>
                                <p class="text-gray-900">{{ $school->email }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-semibold text-gray-700">Website:</label>
                                <p class="text-gray-900">
                                    <a href="{{ $school->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        {{ $school->website }}
                                    </a>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-semibold text-gray-700">Status:</label>
                                <span class="status-badge-premium {{ $school->status }}">
                                    {{ ucfirst($school->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Settings -->
            <div class="card-premium">
                <div class="card-header" style="background: var(--success-gradient); color: white; padding: 1.5rem;">
                    <h4 class="mb-0"><i class="fas fa-cogs me-2"></i>System Settings</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label font-semibold text-gray-700">Timezone:</label>
                                <p class="text-gray-900">{{ $settings['timezone'] ?? 'Not set' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-semibold text-gray-700">Date Format:</label>
                                <p class="text-gray-900">{{ $settings['date_format'] ?? 'Not set' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-semibold text-gray-700">Time Format:</label>
                                <p class="text-gray-900">{{ $settings['time_format'] ?? 'Not set' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label font-semibold text-gray-700">Currency:</label>
                                <p class="text-gray-900">{{ $settings['currency'] ?? 'Not set' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-semibold text-gray-700">Language:</label>
                                <p class="text-gray-900">{{ $settings['language'] ?? 'Not set' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- No School Information -->
            <div class="text-center py-12">
                <i class="fas fa-school text-6xl text-gray-300 mb-4"></i>
                <h5 class="text-xl font-semibold text-gray-600 mb-2">No School Information Found</h5>
                <p class="text-gray-500 mb-4">Please configure your school information first.</p>
                <a href="{{ route('settings.general') }}" class="btn-premium">
                    <i class="fas fa-cog me-2"></i> Add School Information
                </a>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="{{ route('settings.general') }}" class="btn-premium">
                <i class="fas fa-edit me-2"></i> Edit Settings
            </a>
            <a href="{{ route('dashboard') }}" class="btn-premium" style="background: var(--secondary-gradient);">
                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection