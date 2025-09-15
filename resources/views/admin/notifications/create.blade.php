@extends('layouts.app')

@section('title', 'Send Notification')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Send Notification</h1>
        <a href="{{ route('admin.notifications.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Back to Notifications
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Create New Notification</h2>
                <p class="text-sm text-gray-600">Send notifications to students, teachers, or staff members</p>
            </div>

            <form method="POST" action="{{ route('admin.notifications.store') }}" class="p-6">
                @csrf

                <!-- Notification Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                               placeholder="Enter notification title"
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                        <select id="type" 
                                name="type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror"
                                required>
                            <option value="">Select notification type</option>
                            <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General</option>
                            <option value="fee_due" {{ old('type') == 'fee_due' ? 'selected' : '' }}>Fee Due</option>
                            <option value="exam_schedule" {{ old('type') == 'exam_schedule' ? 'selected' : '' }}>Exam Schedule</option>
                            <option value="attendance_alert" {{ old('type') == 'attendance_alert' ? 'selected' : '' }}>Attendance Alert</option>
                            <option value="grade_published" {{ old('type') == 'grade_published' ? 'selected' : '' }}>Grade Published</option>
                            <option value="homework_assigned" {{ old('type') == 'homework_assigned' ? 'selected' : '' }}>Homework Assigned</option>
                            <option value="payment_received" {{ old('type') == 'payment_received' ? 'selected' : '' }}>Payment Received</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Message -->
                <div class="mb-6">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                    <textarea id="message" 
                              name="message" 
                              rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('message') border-red-500 @enderror"
                              placeholder="Enter your notification message"
                              required>{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">You can use variables like {student_name}, {amount}, {date} etc. in your message.</p>
                </div>

                <!-- Delivery Method -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Method *</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="delivery_method" value="email" {{ old('delivery_method') == 'email' ? 'checked' : '' }} class="mr-3">
                            <div>
                                <div class="font-medium text-gray-900">Email</div>
                                <div class="text-sm text-gray-500">Send via email</div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="delivery_method" value="sms" {{ old('delivery_method') == 'sms' ? 'checked' : '' }} class="mr-3">
                            <div>
                                <div class="font-medium text-gray-900">SMS</div>
                                <div class="text-sm text-gray-500">Send via SMS</div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="delivery_method" value="both" {{ old('delivery_method') == 'both' ? 'checked' : '' }} class="mr-3">
                            <div>
                                <div class="font-medium text-gray-900">Both</div>
                                <div class="text-sm text-gray-500">Send via email and SMS</div>
                            </div>
                        </label>
                    </div>
                    @error('delivery_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- User Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recipients *</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($userGroups as $key => $label)
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="user_selection" value="{{ $key }}" {{ old('user_selection') == $key ? 'checked' : '' }} class="mr-3">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $label }}</div>
                                    <div class="text-sm text-gray-500">
                                        @if($key == 'all_students')
                                            Send to all students
                                        @elseif($key == 'all_teachers')
                                            Send to all teachers
                                        @elseif($key == 'all_staff')
                                            Send to all staff members
                                        @else
                                            Select specific users
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('user_selection')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Specific Users Selection -->
                <div id="specific-users" class="mb-6" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Users</label>
                    <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($users as $user)
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded">
                                    <input type="checkbox" 
                                           name="user_ids[]" 
                                           value="{{ $user->id }}" 
                                           {{ in_array($user->id, old('user_ids', [])) ? 'checked' : '' }}
                                           class="mr-2">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }} ({{ ucfirst($user->user_type) }})</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @error('user_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Scheduling -->
                <div class="mb-6">
                    <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-2">Schedule (Optional)</label>
                    <input type="datetime-local" 
                           id="scheduled_at" 
                           name="scheduled_at" 
                           value="{{ old('scheduled_at') }}"
                           min="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Leave empty to send immediately</p>
                    @error('scheduled_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.notifications.index') }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Send Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userSelectionRadios = document.querySelectorAll('input[name="user_selection"]');
    const specificUsersDiv = document.getElementById('specific-users');
    
    function toggleSpecificUsers() {
        const specificUsersRadio = document.querySelector('input[name="user_selection"][value="specific_users"]');
        if (specificUsersRadio && specificUsersRadio.checked) {
            specificUsersDiv.style.display = 'block';
        } else {
            specificUsersDiv.style.display = 'none';
        }
    }
    
    userSelectionRadios.forEach(radio => {
        radio.addEventListener('change', toggleSpecificUsers);
    });
    
    // Initial check
    toggleSpecificUsers();
});
</script>
@endsection
