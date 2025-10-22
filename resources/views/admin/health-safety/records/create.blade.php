@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Create Health Record</h1>
        <form action="{{ route('admin.health-safety.records.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Student -->
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700">Student <span class="text-red-500">*</span></label>
                <select id="student_id" name="student_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Record Type -->
            <div>
                <label for="record_type" class="block text-sm font-medium text-gray-700">Record Type <span class="text-red-500">*</span></label>
                <input type="text" id="record_type" name="record_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Record Date -->
            <div>
                <label for="record_date" class="block text-sm font-medium text-gray-700">Record Date <span class="text-red-500">*</span></label>
                <input type="date" id="record_date" name="record_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Expiry Date -->
            <div>
                <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                <input type="date" id="expiry_date" name="expiry_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Health Provider -->
            <div>
                <label for="health_provider" class="block text-sm font-medium text-gray-700">Health Provider</label>
                <input type="text" id="health_provider" name="health_provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Provider Contact -->
            <div>
                <label for="provider_contact" class="block text-sm font-medium text-gray-700">Provider Contact</label>
                <input type="text" id="provider_contact" name="provider_contact" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <!-- Medical Notes -->
            <div>
                <label for="medical_notes" class="block text-sm font-medium text-gray-700">Medical Notes</label>
                <textarea id="medical_notes" name="medical_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.health-safety.records.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create Health Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection