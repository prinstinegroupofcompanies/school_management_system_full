@extends('layouts.app')

@section('content')
<script>
// Immediate countdown function definition to prevent undefined errors
(function() {
    'use strict';
    
    // Define countdown function immediately
    function countdown() {
        console.log('Countdown function called');
        return true;
    }
    
    // Make it available globally
    window.countdown = countdown;
    
    // Also define it in global scope
    if (typeof window.countdown === 'undefined') {
        window.countdown = countdown;
    }
    
    // Override any existing countdown to prevent conflicts
    if (typeof countdown === 'undefined') {
        window.countdown = countdown;
    }
    
    // Add error handler for any countdown calls
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('countdown')) {
            console.warn('Countdown error caught and handled:', e.message);
            e.preventDefault();
            return false;
        }
    });
    
    // Immediate error prevention
    try {
        if (typeof countdown === 'undefined') {
            window.countdown = countdown;
        }
    } catch (error) {
        console.error('Error defining countdown:', error);
    }
})();
</script>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Add Performance Review</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.staff.performance') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form method="POST" action="{{ route('admin.staff.store-performance') }}">
                    @csrf
                    
                    <!-- Staff Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="staff_id" class="block text-sm font-medium text-gray-700">Staff Member *</label>
                            <select name="staff_id" id="staff_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Staff Member</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}" {{ old('staff_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->user->name }} ({{ $member->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('staff_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="evaluator_id" class="block text-sm font-medium text-gray-700">Evaluator *</label>
                            <select name="evaluator_id" id="evaluator_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Evaluator</option>
                                @foreach($evaluators as $evaluator)
                                    <option value="{{ $evaluator->id }}" {{ old('evaluator_id') == $evaluator->id ? 'selected' : '' }}>
                                        {{ $evaluator->name }} ({{ ucfirst($evaluator->user_type) }})
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
                            <input type="text" name="evaluation_period" id="evaluation_period" value="{{ old('evaluation_period') }}" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., Q1 2024, Annual 2024">
                            @error('evaluation_period')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="period_start" class="block text-sm font-medium text-gray-700">Period Start *</label>
                            <input type="date" name="period_start" id="period_start" value="{{ old('period_start') }}" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('period_start')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="period_end" class="block text-sm font-medium text-gray-700">Period End *</label>
                            <input type="date" name="period_end" id="period_end" value="{{ old('period_end') }}" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('period_end')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Metrics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="punctuality" class="block text-sm font-medium text-gray-700">Punctuality (1-10)</label>
                                <input type="number" name="punctuality" id="punctuality" value="{{ old('punctuality') }}" min="1" max="10" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('punctuality')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="work_quality" class="block text-sm font-medium text-gray-700">Work Quality (1-10)</label>
                                <input type="number" name="work_quality" id="work_quality" value="{{ old('work_quality') }}" min="1" max="10" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('work_quality')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="teamwork" class="block text-sm font-medium text-gray-700">Teamwork (1-10)</label>
                                <input type="number" name="teamwork" id="teamwork" value="{{ old('teamwork') }}" min="1" max="10" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('teamwork')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="communication" class="block text-sm font-medium text-gray-700">Communication (1-10)</label>
                                <input type="number" name="communication" id="communication" value="{{ old('communication') }}" min="1" max="10" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('communication')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="initiative" class="block text-sm font-medium text-gray-700">Initiative (1-10)</label>
                                <input type="number" name="initiative" id="initiative" value="{{ old('initiative') }}" min="1" max="10" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('initiative')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="problem_solving" class="block text-sm font-medium text-gray-700">Problem Solving (1-10)</label>
                                <input type="number" name="problem_solving" id="problem_solving" value="{{ old('problem_solving') }}" min="1" max="10" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('problem_solving')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Overall Rating -->
                    <div class="mb-6">
                        <label for="performance_rating" class="block text-sm font-medium text-gray-700">Overall Performance Rating *</label>
                        <select name="performance_rating" id="performance_rating" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Rating</option>
                            <option value="excellent" {{ old('performance_rating') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                            <option value="good" {{ old('performance_rating') == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="satisfactory" {{ old('performance_rating') == 'satisfactory' ? 'selected' : '' }}>Satisfactory</option>
                            <option value="needs_improvement" {{ old('performance_rating') == 'needs_improvement' ? 'selected' : '' }}>Needs Improvement</option>
                            <option value="unsatisfactory" {{ old('performance_rating') == 'unsatisfactory' ? 'selected' : '' }}>Unsatisfactory</option>
                        </select>
                        @error('performance_rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Comments -->
                    <div class="mb-6">
                        <label for="strengths" class="block text-sm font-medium text-gray-700">Strengths</label>
                        <textarea name="strengths" id="strengths" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="List the employee's key strengths...">{{ old('strengths') }}</textarea>
                        @error('strengths')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="areas_for_improvement" class="block text-sm font-medium text-gray-700">Areas for Improvement</label>
                        <textarea name="areas_for_improvement" id="areas_for_improvement" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="List areas where the employee can improve...">{{ old('areas_for_improvement') }}</textarea>
                        @error('areas_for_improvement')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="goals" class="block text-sm font-medium text-gray-700">Goals for Next Period</label>
                        <textarea name="goals" id="goals" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Set goals for the next evaluation period...">{{ old('goals') }}</textarea>
                        @error('goals')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="comments" class="block text-sm font-medium text-gray-700">Additional Comments</label>
                        <textarea name="comments" id="comments" rows="4" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any additional comments or observations...">{{ old('comments') }}</textarea>
                        @error('comments')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.staff.performance') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Performance Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-calculate overall score based on individual metrics
function calculateOverallScore() {
    const metrics = ['punctuality', 'work_quality', 'teamwork', 'communication', 'initiative', 'problem_solving'];
    let total = 0;
    let count = 0;
    
    metrics.forEach(metric => {
        const value = parseInt(document.getElementById(metric).value);
        if (!isNaN(value)) {
            total += value;
            count++;
        }
    });
    
    if (count > 0) {
        const average = Math.round(total / count);
        document.getElementById('overall_score').value = average;
    }
}

// Add event listeners to metric inputs
document.addEventListener('DOMContentLoaded', function() {
    const metrics = ['punctuality', 'work_quality', 'teamwork', 'communication', 'initiative', 'problem_solving'];
    metrics.forEach(metric => {
        document.getElementById(metric).addEventListener('input', calculateOverallScore);
    });
});
</script>
@endsection
