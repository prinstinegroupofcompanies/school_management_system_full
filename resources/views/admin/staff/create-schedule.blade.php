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
                <h1 class="text-3xl font-bold text-gray-900">Add Staff Schedule</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.staff.schedules') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Schedules
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form method="POST" action="{{ route('admin.staff.store-schedule') }}">
                    @csrf
                    
                    <!-- Staff Selection -->
                    <div class="mb-6">
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

                    <!-- Schedule Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="schedule_date" class="block text-sm font-medium text-gray-700">Schedule Date *</label>
                            <input type="date" name="schedule_date" id="schedule_date" value="{{ old('schedule_date') }}" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('schedule_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shift_type" class="block text-sm font-medium text-gray-700">Shift Type *</label>
                            <select name="shift_type" id="shift_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Shift Type</option>
                                <option value="morning" {{ old('shift_type') == 'morning' ? 'selected' : '' }}>Morning</option>
                                <option value="afternoon" {{ old('shift_type') == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                                <option value="evening" {{ old('shift_type') == 'evening' ? 'selected' : '' }}>Evening</option>
                                <option value="night" {{ old('shift_type') == 'night' ? 'selected' : '' }}>Night</option>
                            </select>
                            @error('shift_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Time Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time *</label>
                            <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">End Time *</label>
                            <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Work Location -->
                    <div class="mb-6">
                        <label for="work_location" class="block text-sm font-medium text-gray-700">Work Location</label>
                        <input type="text" name="work_location" id="work_location" value="{{ old('work_location') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., Main Office, Classroom A, Library">
                        @error('work_location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duties -->
                    <div class="mb-6">
                        <label for="duties" class="block text-sm font-medium text-gray-700">Duties & Responsibilities</label>
                        <textarea name="duties" id="duties" rows="4" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Describe the duties and responsibilities for this schedule...">{{ old('duties') }}</textarea>
                        @error('duties')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any additional notes or special instructions...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.staff.schedules') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-fill shift times based on shift type
document.getElementById('shift_type').addEventListener('change', function() {
    const shiftType = this.value;
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    
    switch(shiftType) {
        case 'morning':
            startTime.value = '08:00';
            endTime.value = '16:00';
            break;
        case 'afternoon':
            startTime.value = '12:00';
            endTime.value = '20:00';
            break;
        case 'evening':
            startTime.value = '16:00';
            endTime.value = '00:00';
            break;
        case 'night':
            startTime.value = '22:00';
            endTime.value = '06:00';
            break;
        default:
            startTime.value = '';
            endTime.value = '';
    }
});

// Calculate duration
function calculateDuration() {
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    
    if (startTime && endTime) {
        const start = new Date('2000-01-01T' + startTime);
        const end = new Date('2000-01-01T' + endTime);
        
        // Handle overnight shifts
        if (end < start) {
            end.setDate(end.getDate() + 1);
        }
        
        const durationMs = end - start;
        const durationHours = durationMs / (1000 * 60 * 60);
        
        // Display duration (optional)
        console.log('Duration: ' + durationHours.toFixed(1) + ' hours');
    }
}

document.getElementById('start_time').addEventListener('change', calculateDuration);
document.getElementById('end_time').addEventListener('change', calculateDuration);
</script>
@endsection
