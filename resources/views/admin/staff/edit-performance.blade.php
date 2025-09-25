@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Edit Performance Evaluation</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.staff.performance.show', $performance) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Performance
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form method="POST" action="{{ route('admin.staff.performance.update', $performance) }}">
                    @csrf
                    @method('PUT')

                    <!-- Staff and Evaluator Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="staff_id" class="block text-sm font-medium text-gray-700">Staff Member *</label>
                            <select name="staff_id" id="staff_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select Staff Member</option>
                                @foreach($staff as $s)
                                    <option value="{{ $s->id }}" {{ $performance->staff_id == $s->id ? 'selected' : '' }}>
                                        {{ $s->user->name }} ({{ $s->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('staff_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="evaluator_id" class="block text-sm font-medium text-gray-700">Evaluator *</label>
                            <select name="evaluator_id" id="evaluator_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select Evaluator</option>
                                @foreach($evaluators as $evaluator)
                                    <option value="{{ $evaluator->id }}" {{ $performance->evaluator_id == $evaluator->id ? 'selected' : '' }}>
                                        {{ $evaluator->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('evaluator_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Evaluation Period -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label for="evaluation_period" class="block text-sm font-medium text-gray-700">Evaluation Period *</label>
                            <input type="text" name="evaluation_period" id="evaluation_period" value="{{ old('evaluation_period', $performance->evaluation_period) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            @error('evaluation_period')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="period_start" class="block text-sm font-medium text-gray-700">Period Start *</label>
                            <input type="date" name="period_start" id="period_start" value="{{ old('period_start', $performance->period_start ? $performance->period_start->format('Y-m-d') : '') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            @error('period_start')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="period_end" class="block text-sm font-medium text-gray-700">Period End *</label>
                            <input type="date" name="period_end" id="period_end" value="{{ old('period_end', $performance->period_end ? $performance->period_end->format('Y-m-d') : '') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            @error('period_end')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Performance Scores -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Scores (1-10)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label for="punctuality" class="block text-sm font-medium text-gray-700">Punctuality *</label>
                                    <input type="number" name="punctuality" id="punctuality" min="1" max="10" value="{{ old('punctuality', $performance->punctuality) }}" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    @error('punctuality')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="work_quality" class="block text-sm font-medium text-gray-700">Work Quality *</label>
                                    <input type="number" name="work_quality" id="work_quality" min="1" max="10" value="{{ old('work_quality', $performance->work_quality) }}" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    @error('work_quality')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="teamwork" class="block text-sm font-medium text-gray-700">Teamwork *</label>
                                    <input type="number" name="teamwork" id="teamwork" min="1" max="10" value="{{ old('teamwork', $performance->teamwork) }}" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    @error('teamwork')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="communication" class="block text-sm font-medium text-gray-700">Communication *</label>
                                    <input type="number" name="communication" id="communication" min="1" max="10" value="{{ old('communication', $performance->communication) }}" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    @error('communication')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="initiative" class="block text-sm font-medium text-gray-700">Initiative *</label>
                                    <input type="number" name="initiative" id="initiative" min="1" max="10" value="{{ old('initiative', $performance->initiative) }}" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    @error('initiative')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="problem_solving" class="block text-sm font-medium text-gray-700">Problem Solving *</label>
                                    <input type="number" name="problem_solving" id="problem_solving" min="1" max="10" value="{{ old('problem_solving', $performance->problem_solving) }}" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    @error('problem_solving')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Rating and Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="performance_rating" class="block text-sm font-medium text-gray-700">Performance Rating *</label>
                            <select name="performance_rating" id="performance_rating" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select Rating</option>
                                <option value="excellent" {{ old('performance_rating', $performance->performance_rating) == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="good" {{ old('performance_rating', $performance->performance_rating) == 'good' ? 'selected' : '' }}>Good</option>
                                <option value="satisfactory" {{ old('performance_rating', $performance->performance_rating) == 'satisfactory' ? 'selected' : '' }}>Satisfactory</option>
                                <option value="needs_improvement" {{ old('performance_rating', $performance->performance_rating) == 'needs_improvement' ? 'selected' : '' }}>Needs Improvement</option>
                                <option value="unsatisfactory" {{ old('performance_rating', $performance->performance_rating) == 'unsatisfactory' ? 'selected' : '' }}>Unsatisfactory</option>
                            </select>
                            @error('performance_rating')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="draft" {{ old('status', $performance->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ old('status', $performance->status) == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="reviewed" {{ old('status', $performance->status) == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                <option value="approved" {{ old('status', $performance->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="disputed" {{ old('status', $performance->status) == 'disputed' ? 'selected' : '' }}>Disputed</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Strengths and Areas for Improvement -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="strengths" class="block text-sm font-medium text-gray-700">Strengths</label>
                            <textarea name="strengths" id="strengths" rows="4" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('strengths', $performance->strengths) }}</textarea>
                            @error('strengths')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="areas_for_improvement" class="block text-sm font-medium text-gray-700">Areas for Improvement</label>
                            <textarea name="areas_for_improvement" id="areas_for_improvement" rows="4" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('areas_for_improvement', $performance->areas_for_improvement) }}</textarea>
                            @error('areas_for_improvement')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Goals and Comments -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="goals" class="block text-sm font-medium text-gray-700">Goals</label>
                            <textarea name="goals" id="goals" rows="4" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('goals', $performance->goals) }}</textarea>
                            @error('goals')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="comments" class="block text-sm font-medium text-gray-700">Comments</label>
                            <textarea name="comments" id="comments" rows="4" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('comments', $performance->comments) }}</textarea>
                            @error('comments')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('admin.staff.performance.show', $performance) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Update Performance Evaluation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
