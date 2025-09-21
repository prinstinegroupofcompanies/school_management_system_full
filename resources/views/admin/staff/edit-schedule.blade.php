@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Staff Schedule</h1>
        <a href="{{ route('admin.staff.schedules') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Back to Schedules
        </a>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.staff.schedules.update', $schedule) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Staff Member</label>
                    <select name="staff_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="">Select staff member</option>
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}" {{ $schedule->staff_id == $member->id ? 'selected' : '' }}>
                                {{ $member->user->name ?? 'N/A' }} ({{ $member->employee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Schedule Date</label>
                    <input type="date" name="schedule_date" value="{{ $schedule->schedule_date }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input type="time" name="start_time" value="{{ $schedule->start_time }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">End Time</label>
                    <input type="time" name="end_time" value="{{ $schedule->end_time }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Schedule Type</label>
                    <select name="schedule_type" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="regular" {{ $schedule->schedule_type === 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="overtime" {{ $schedule->schedule_type === 'overtime' ? 'selected' : '' }}>Overtime</option>
                        <option value="holiday" {{ $schedule->schedule_type === 'holiday' ? 'selected' : '' }}>Holiday</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="scheduled" {{ $schedule->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="completed" {{ $schedule->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $schedule->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="Schedule details...">{{ $schedule->description }}</textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" onclick="history.back()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Update Schedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
