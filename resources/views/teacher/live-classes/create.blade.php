@extends('layouts.app')

@section('title', 'Schedule Live Class')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-video me-2"></i>Schedule Live Class
        </h2>
        <a href="{{ route('teacher.live-classes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Live Classes
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('teacher.live-classes.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">
                                Class Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="title" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="class_id" class="form-label">Class</label>
                                <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror">
                                    <option value="">Select Class (Optional)</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="subject_id" class="form-label">Subject</label>
                                <select name="subject_id" id="subject_id" class="form-select @error('subject_id') is-invalid @enderror">
                                    <option value="">Select Subject (Optional)</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="platform" class="form-label">
                                    Platform <span class="text-danger">*</span>
                                </label>
                                <select name="platform" id="platform" class="form-select @error('platform') is-invalid @enderror" required>
                                    <option value="zoom" {{ old('platform') === 'zoom' ? 'selected' : '' }}>Zoom</option>
                                    <option value="google_meet" {{ old('platform') === 'google_meet' ? 'selected' : '' }}>Google Meet</option>
                                    <option value="microsoft_teams" {{ old('platform') === 'microsoft_teams' ? 'selected' : '' }}>Microsoft Teams</option>
                                    <option value="custom" {{ old('platform') === 'custom' ? 'selected' : '' }}>Custom URL</option>
                                    <option value="other" {{ old('platform') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('platform')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="scheduled_at" class="form-label">
                                    Scheduled At <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" name="scheduled_at" id="scheduled_at" 
                                       class="form-control @error('scheduled_at') is-invalid @enderror" 
                                       value="{{ old('scheduled_at') }}" required>
                                @error('scheduled_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="meeting_url" class="form-label">
                                    Meeting URL <span class="text-danger">*</span>
                                </label>
                                <input type="url" name="meeting_url" id="meeting_url" 
                                       class="form-control @error('meeting_url') is-invalid @enderror" 
                                       value="{{ old('meeting_url') }}" placeholder="https://..." required>
                                @error('meeting_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="duration_minutes" class="form-label">
                                    Duration (minutes) <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="duration_minutes" id="duration_minutes" 
                                       class="form-control @error('duration_minutes') is-invalid @enderror" 
                                       value="{{ old('duration_minutes', 60) }}" min="15" max="480" required>
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="meeting_id" class="form-label">Meeting ID</label>
                                <input type="text" name="meeting_id" id="meeting_id" 
                                       class="form-control @error('meeting_id') is-invalid @enderror" 
                                       value="{{ old('meeting_id') }}">
                                @error('meeting_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="meeting_password" class="form-label">Meeting Password</label>
                                <input type="text" name="meeting_password" id="meeting_password" 
                                       class="form-control @error('meeting_password') is-invalid @enderror" 
                                       value="{{ old('meeting_password') }}">
                                @error('meeting_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_recorded" id="is_recorded" value="1" 
                                       class="form-check-input" {{ old('is_recorded') ? 'checked' : '' }}>
                                <label for="is_recorded" class="form-check-label">Record this class</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="2" 
                                      class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('teacher.live-classes.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Schedule Class
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

