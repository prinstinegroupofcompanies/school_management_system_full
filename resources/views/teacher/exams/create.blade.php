@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New Exam</h1>
        <a href="{{ route('teacher.exams.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Back to Exams
        </a>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('teacher.exams.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Exam Title</label>
                    <input type="text" name="title" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Exam Type</label>
                    <select name="exam_type" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="">Select type</option>
                        @foreach($examTypes as $key => $type)
                            <option value="{{ $key }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Subject</label>
                    <select name="subject_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="">Select subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Class</label>
                    <select name="class_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="">Select class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input type="datetime-local" name="start_time" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">End Time</label>
                    <input type="datetime-local" name="end_time" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Marks</label>
                    <input type="number" name="total_marks" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                    <input type="number" name="duration" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Instructions</label>
                <textarea name="instructions" rows="4" class="mt-1 block w-full border-gray-300 rounded-md"></textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_published" id="is_published" class="h-4 w-4 text-blue-600">
                <label for="is_published" class="ml-2 text-sm text-gray-700">Publish immediately</label>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" onclick="history.back()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Create Exam
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
